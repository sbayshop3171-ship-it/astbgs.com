<?php

namespace App\Lib;

use App\Models\Transaction;
use App\Models\User;

class Referral
{
    public static function processReferralCommission($user, $amount, $trx, $planId)
    {
        $refAmount = gs('referral_fixed') + ($amount * gs('referral_percentage') / 100);

        $refUser = User::active()->find($user->ref_by);

        if ($refUser) {
            $refUser->balance += $refAmount;
            $refUser->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $refUser->id;
            $transaction->amount       = $refAmount;
            $transaction->post_balance = $refUser->balance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Referral commission for subscribe plan by @' . $user->username;
            $transaction->trx          = $trx;
            $transaction->remark       = 'referral_commission';
            $transaction->save();

            createPlanHistory($planId, $refAmount, '-', 'referral_commission');
        }
    }
}
