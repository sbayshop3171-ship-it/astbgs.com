<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\WalletService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment($id)
    {
        try {
            $planId = decrypt($id);
        } catch (\Throwable $th) {
            abort(404);
        }

        $plan = UserPlan::query()
            ->where('user_id', auth()->id())
            ->unpaid()
            ->findOrFail($planId);

        $gatewayCurrency   = $this->gatewayCurrencies();
        $pageTitle         = 'Payment Methods';
        $walletTopupAmount = old('wallet_topup_amount');

        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'plan', 'walletTopupAmount'));
    }

    public function deposit()
    {
        $gatewayCurrency   = $this->gatewayCurrencies();
        $pageTitle         = 'Add Money';
        $walletTopupAmount = old('wallet_topup_amount', request()->amount);

        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'walletTopupAmount'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'gateway'            => 'required',
            'currency'           => 'required',
            'wallet_topup_amount' => 'nullable|numeric|gt:0',
            'user_plan'          => 'nullable|integer',
            'order_id'           => 'nullable|integer',
        ]);

        $targets = collect([
            'wallet_topup_amount' => filled($request->wallet_topup_amount),
            'user_plan'           => filled($request->user_plan),
            'order_id'            => filled($request->order_id),
        ])->filter();

        if ($targets->count() !== 1) {
            $notify[] = ['error', 'Please choose exactly one payment target'];
            return back()->withNotify($notify)->withInput();
        }

        $user    = auth()->user();
        $plan    = null;
        $order   = null;
        $purpose = Status::DEPOSIT_PURPOSE_WALLET_TOPUP;
        $amount  = (float) $request->wallet_topup_amount;

        if ($request->filled('user_plan')) {
            $plan = UserPlan::query()
                ->where('id', $request->user_plan)
                ->where('user_id', auth()->id())
                ->unpaid()
                ->firstOrFail();

            $amount  = (float) $plan->price;
            $purpose = Status::DEPOSIT_PURPOSE_MEMBERSHIP_PAYMENT;
        } elseif ($request->filled('order_id')) {
            $order = Order::query()
                ->where('id', $request->order_id)
                ->where('user_id', auth()->id())
                ->pendingPayment()
                ->firstOrFail();

            $amount  = (float) $order->total;
            $purpose = Status::DEPOSIT_PURPOSE_ORDER_PAYMENT;
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')
            ->where('method_code', $request->gateway)
            ->where('currency', $request->currency)
            ->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify)->withInput();
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow payment limit'];
            return back()->withNotify($notify)->withInput();
        }

        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $deposit                  = new Deposit();
        $deposit->user_id         = $user->id;
        $deposit->user_plan_id    = $plan?->id;
        $deposit->order_id        = $order?->id;
        $deposit->purpose         = $purpose;
        $deposit->method_code     = $gate->method_code;
        $deposit->method_currency = strtoupper($gate->currency);
        $deposit->amount          = $amount;
        $deposit->charge          = $charge;
        $deposit->rate            = $gate->rate;
        $deposit->final_amount    = $finalAmount;
        $deposit->btc_amount      = 0;
        $deposit->btc_wallet      = '';
        $deposit->trx             = getTrx();
        $deposit->success_url     = $this->getSuccessUrl($purpose, $plan, $order);
        $deposit->failed_url      = $this->getFailedUrl($purpose, $plan, $order, $amount);
        $deposit->save();

        session()->put('Track', $deposit->trx);

        return to_route('user.deposit.confirm');
    }

    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }

        $data = Deposit::where('id', $id)
            ->where('status', Status::PAYMENT_INITIATE)
            ->orderBy('id', 'DESC')
            ->firstOrFail();

        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);

        return to_route('user.deposit.confirm');
    }

    public function depositConfirm()
    {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)
            ->where('status', Status::PAYMENT_INITIATE)
            ->orderBy('id', 'DESC')
            ->with('gateway')
            ->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }

        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        if (isset($data->session)) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';

        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        if (!in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
            return;
        }

        $result     = app(WalletService::class)->finalizeDeposit($deposit);
        $deposit    = $result['deposit'];
        $user       = $result['user'];
        $purpose    = $result['purpose'];
        $methodName = $result['method_name'];

        session()->forget('Track');

        if (!$isManual) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $user->id;
            $adminNotification->title     = 'Payment successful via ' . $methodName;
            $adminNotification->click_url = $purpose === Status::DEPOSIT_PURPOSE_ORDER_PAYMENT
                ? urlPath('admin.orders.show', $deposit->order_id)
                : urlPath('admin.deposit.successful');
            $adminNotification->save();
        }

        notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
            'method_name'     => $methodName,
            'method_currency' => $deposit->method_currency,
            'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
            'amount'          => showAmount($deposit->amount, currencyFormat: false),
            'charge'          => showAmount($deposit->charge, currencyFormat: false),
            'rate'            => showAmount($deposit->rate, currencyFormat: false),
            'trx'             => $deposit->trx,
            'post_balance'    => showAmount(
                $purpose === Status::DEPOSIT_PURPOSE_WALLET_TOPUP ? $user->wallet_balance : $user->balance,
                currencyFormat: false
            ),
        ]);
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);

        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Payment';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }

        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);

        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form?->form_data ?? [];

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        if ($validationRule) {
            $request->validate($validationRule);
        }

        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
            'amount'          => showAmount($data->amount, currencyFormat: false),
            'charge'          => showAmount($data->charge, currencyFormat: false),
            'rate'            => showAmount($data->rate, currencyFormat: false),
            'trx'             => $data->trx,
        ]);

        $notify[] = ['success', 'Your payment request has been taken'];

        if ($data->purpose === Status::DEPOSIT_PURPOSE_ORDER_PAYMENT && $data->order_id) {
            return to_route('user.orders.show', $data->order_id)->withNotify($notify);
        }

        if ($data->purpose === Status::DEPOSIT_PURPOSE_WALLET_TOPUP) {
            return to_route('user.deposit.index')->withNotify($notify);
        }

        return to_route('user.subscription.history')->withNotify($notify);
    }

    protected function gatewayCurrencies()
    {
        return GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderBy('method_code')->get();
    }

    protected function getSuccessUrl(string $purpose, ?UserPlan $plan, ?Order $order): string
    {
        return match ($purpose) {
            Status::DEPOSIT_PURPOSE_ORDER_PAYMENT => route('user.orders.show', $order->id),
            Status::DEPOSIT_PURPOSE_MEMBERSHIP_PAYMENT => route('user.subscription.history'),
            default => route('user.deposit.index'),
        };
    }

    protected function getFailedUrl(string $purpose, ?UserPlan $plan, ?Order $order, float $amount): string
    {
        return match ($purpose) {
            Status::DEPOSIT_PURPOSE_ORDER_PAYMENT => route('user.orders.pay', $order->id),
            Status::DEPOSIT_PURPOSE_MEMBERSHIP_PAYMENT => route('user.payment', encrypt($plan->id)),
            default => route('user.deposit.index', ['amount' => getAmount($amount)]),
        };
    }
}
