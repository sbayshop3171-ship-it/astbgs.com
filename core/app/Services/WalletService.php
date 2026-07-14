<?php

namespace App\Services;

use App\Constants\Status;
use App\Lib\Referral;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function finalizeDeposit(Deposit $deposit): array
    {
        return DB::transaction(function () use ($deposit) {
            $deposit = Deposit::query()->lockForUpdate()->with(['userPlan.plan', 'order.items', 'gateway'])->findOrFail($deposit->id);

            if (!in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
                return [
                    'deposit'      => $deposit,
                    'user'         => $deposit->user,
                    'purpose'      => $this->resolveDepositPurpose($deposit),
                    'transaction'  => null,
                    'method_name'  => $deposit->methodName(),
                ];
            }

            $purpose = $this->resolveDepositPurpose($deposit);
            $deposit->purpose = $purpose;
            $deposit->status  = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user        = User::query()->lockForUpdate()->findOrFail($deposit->user_id);
            $methodName  = $deposit->methodName();
            $transaction = null;

            if ($purpose === Status::DEPOSIT_PURPOSE_WALLET_TOPUP) {
                $user->wallet_balance += $deposit->amount;
                $user->save();

                $transaction = $this->recordTransaction(
                    $user,
                    $deposit->amount,
                    '+',
                    Status::BALANCE_TYPE_WALLET,
                    'Wallet top-up via ' . $methodName,
                    'wallet_topup',
                    $deposit->trx,
                    $deposit->charge,
                    'deposit',
                    $deposit->id
                );
            } elseif ($purpose === Status::DEPOSIT_PURPOSE_ORDER_PAYMENT) {
                $order = Order::query()->lockForUpdate()->with('items')->findOrFail($deposit->order_id);
                if ($order->status === Status::CATALOG_ORDER_PENDING_PAYMENT) {
                    $this->markOrderPaid($order, $deposit->trx, Status::PAYMENT_SOURCE_GATEWAY, $methodName);
                }
            } else {
                $userPlan = UserPlan::query()->lockForUpdate()->with('plan')->findOrFail($deposit->user_plan_id);
                if ($userPlan->is_payment == Status::UNPAID_SUBSCRIPTION) {
                    $this->activatePlan($user, $userPlan, $deposit->trx, Status::PAYMENT_SOURCE_GATEWAY);
                }
            }

            return [
                'deposit'      => $deposit,
                'user'         => $user,
                'purpose'      => $purpose,
                'transaction'  => $transaction,
                'method_name'  => $methodName,
            ];
        });
    }

    public function payOrder(User $user, Order $order): Transaction
    {
        return DB::transaction(function () use ($user, $order) {
            $lockedUser  = User::query()->lockForUpdate()->findOrFail($user->id);
            $lockedOrder = Order::query()->lockForUpdate()->with('items')->pendingPayment()->findOrFail($order->id);

            $this->ensureSufficientBalance($lockedUser, Status::BALANCE_TYPE_WALLET, $lockedOrder->total);

            $lockedUser->wallet_balance -= $lockedOrder->total;
            $lockedUser->save();

            $trx = getTrx();

            $transaction = $this->recordTransaction(
                $lockedUser,
                $lockedOrder->total,
                '-',
                Status::BALANCE_TYPE_WALLET,
                'Wallet payment for order ' . $lockedOrder->order_number,
                'wallet_order_payment',
                $trx,
                0,
                'order',
                $lockedOrder->id
            );

            $this->markOrderPaid($lockedOrder, $trx, Status::PAYMENT_SOURCE_WALLET, __('Wallet'), $lockedOrder->total);

            return $transaction;
        });
    }

    public function payMembership(User $user, UserPlan $userPlan): Transaction
    {
        return DB::transaction(function () use ($user, $userPlan) {
            $lockedUserPlan = UserPlan::query()->lockForUpdate()->with('plan')->unpaid()->findOrFail($userPlan->id);
            $lockedUser     = User::query()->lockForUpdate()->findOrFail($user->id);

            $this->ensureSufficientBalance($lockedUser, Status::BALANCE_TYPE_WALLET, $lockedUserPlan->price);

            $lockedUser->wallet_balance -= $lockedUserPlan->price;
            $lockedUser->save();

            $trx = getTrx();

            $transaction = $this->recordTransaction(
                $lockedUser,
                $lockedUserPlan->price,
                '-',
                Status::BALANCE_TYPE_WALLET,
                'Wallet payment for membership - ' . $lockedUserPlan->plan->name,
                'wallet_membership_payment',
                $trx,
                0,
                'user_plan',
                $lockedUserPlan->id
            );

            $this->activatePlan($lockedUser, $lockedUserPlan, $trx, Status::PAYMENT_SOURCE_WALLET);

            return $transaction;
        });
    }

    public function adjustBalance(User $user, float $amount, string $action, string $remark, string $balanceType): array
    {
        return DB::transaction(function () use ($user, $amount, $action, $remark, $balanceType) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $column     = $this->getBalanceColumn($balanceType);

            if ($action === 'sub') {
                $this->ensureSufficientBalance($lockedUser, $balanceType, $amount);
                $lockedUser->{$column} -= $amount;
                $trxType = '-';
            } else {
                $lockedUser->{$column} += $amount;
                $trxType = '+';
            }

            $lockedUser->save();

            $transaction = $this->recordTransaction(
                $lockedUser,
                $amount,
                $trxType,
                $balanceType,
                $remark,
                $action === 'sub' ? 'balance_subtract' : 'balance_add',
                getTrx(),
                0,
                'admin_adjustment',
                null
            );

            return [
                'user'        => $lockedUser,
                'transaction' => $transaction,
                'column'      => $column,
            ];
        });
    }

    public function resolveDepositPurpose(Deposit $deposit): string
    {
        if ($deposit->purpose) {
            return $deposit->purpose;
        }

        if ($deposit->order_id) {
            return Status::DEPOSIT_PURPOSE_ORDER_PAYMENT;
        }

        if ($deposit->user_plan_id) {
            return Status::DEPOSIT_PURPOSE_MEMBERSHIP_PAYMENT;
        }

        return Status::DEPOSIT_PURPOSE_WALLET_TOPUP;
    }

    public function getBalanceColumn(string $balanceType): string
    {
        return $balanceType === Status::BALANCE_TYPE_WALLET ? 'wallet_balance' : 'balance';
    }

    public function ensureSufficientBalance(User $user, string $balanceType, float $amount): void
    {
        $column = $this->getBalanceColumn($balanceType);

        if ((float) $user->{$column} < $amount) {
            throw new RuntimeException(
                $balanceType === Status::BALANCE_TYPE_WALLET
                    ? 'Insufficient wallet balance for this payment'
                    : 'Insufficient earning balance for this action'
            );
        }
    }

    public function recordTransaction(
        User $user,
        float $amount,
        string $trxType,
        string $balanceType,
        string $details,
        string $remark,
        string $trx,
        float $charge = 0,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): Transaction {
        $column = $this->getBalanceColumn($balanceType);

        $transaction                 = new Transaction();
        $transaction->user_id        = $user->id;
        $transaction->amount         = $amount;
        $transaction->post_balance   = $user->{$column};
        $transaction->charge         = $charge;
        $transaction->trx_type       = $trxType;
        $transaction->details        = $details;
        $transaction->trx            = $trx;
        $transaction->remark         = $remark;
        $transaction->balance_type   = $balanceType;
        $transaction->reference_type = $referenceType;
        $transaction->reference_id   = $referenceId;
        $transaction->save();

        return $transaction;
    }

    protected function activatePlan(User $user, UserPlan $userPlan, string $trx, string $paymentSource): void
    {
        $activePlan = UserPlan::query()
            ->where('user_id', $user->id)
            ->active()
            ->paid()
            ->where('id', '!=', $userPlan->id)
            ->lockForUpdate()
            ->first();

        if ($activePlan) {
            $activePlan->status = Status::PLAN_EXPIRED;
            $activePlan->save();
        }

        $userPlan->is_payment     = Status::PAID_SUBSCRIPTION;
        $userPlan->status         = Status::PLAN_ACTIVE;
        $userPlan->payment_source = $paymentSource;
        $userPlan->payment_trx    = $trx;
        $userPlan->save();

        createPlanHistory($userPlan->plan_id, $userPlan->price, '+', 'purchase');

        if (gs('referral') && $user->ref_by) {
            Referral::processReferralCommission($user, $userPlan->price, $trx, $userPlan->plan_id);
        }
    }

    protected function markOrderPaid(Order $order, string $trx, string $paymentSource, ?string $gatewayName = null, float $walletAmount = 0): void
    {
        $order->payment_trx   = $trx;
        $order->gateway_name  = $gatewayName;
        $order->payment_source = $paymentSource;
        $order->wallet_amount = $walletAmount;
        $order->paid_at       = now();
        $order->status        = $order->items->every(fn($item) => $item->delivery_type === Status::PRODUCT_TYPE_DOWNLOADABLE)
            ? Status::CATALOG_ORDER_COMPLETED
            : Status::CATALOG_ORDER_PROCESSING;
        $order->save();
    }
}
