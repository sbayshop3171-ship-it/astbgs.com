<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlan;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id'        => 'required|exists:plans,id',
            'plan_type'      => 'required|in:monthly,yearly'
        ]);

        $plan         = Plan::active()->with('userPlan')->findOrFail($request->plan_id);
        $planPrice    = $request->plan_type == 'monthly' ? $plan->monthly_price : $plan->yearly_price;
        $planDuration = $request->plan_type == 'monthly' ? Status::MONTHLY_PLAN : Status::YEARLY_PLAN;

        $user = auth()->user();

        $userPlan                = new UserPlan();
        $userPlan->user_id       = $user->id;
        $userPlan->plan_id       = $plan->id;
        $userPlan->plan_duration = $planDuration;
        $userPlan->price         = $planPrice;
        $userPlan->daily_limit   = $plan->daily_limit;
        $userPlan->weekly_limit  = $plan->weekly_limit;
        $userPlan->monthly_limit = $plan->monthly_limit;
        $userPlan->expired_at    = $request->plan_type == 'monthly' ? now()->addMonth() : now()->addYear();
        $userPlan->save();

        return to_route('user.payment', encrypt($userPlan->id));
    }


    public function history(Request $request)
    {
        $pageTitle = 'Subscription History';
        $subscriptions = auth()->user()->userPlans()->searchable(['plan:name'])->with('plan')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.subscription.history', compact('pageTitle', 'subscriptions'));
    }

    public function walletPay($id)
    {
        $userPlan = UserPlan::query()
            ->where('user_id', auth()->id())
            ->unpaid()
            ->with('plan')
            ->findOrFail($id);

        try {
            app(WalletService::class)->payMembership(auth()->user(), $userPlan);
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Membership activated successfully from wallet'];
        return to_route('user.subscription.history')->withNotify($notify);
    }
    public function mySubscription(Request $request)
    {
        $pageTitle = 'Membership Overview';
        $userPlan = userActivePlan();
        if (!$userPlan) {
            $notify[] = ['info', 'You do not have an active subscription plan yet'];
            return to_route('plans')->withNotify($notify);
        }
        $downloads = auth()->user()->downloadLog();
        $uses['today_uses']      = (clone $downloads)->whereDate('created_at', now())->count();
        $uses['weekly_uses']     = (clone $downloads)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $uses['monthly_uses']    = (clone $downloads)->whereMonth('created_at', now()->month)->count();
        $uses['daily_percent']   = $this->getDownloadProgressBarData($uses['today_uses'], $userPlan->daily_limit) ?? 0;
        $uses['weekly_percent']  = $this->getDownloadProgressBarData($uses['weekly_uses'], $userPlan->weekly_limit) ?? 0;
        $uses['monthly_percent'] = $this->getDownloadProgressBarData($uses['monthly_uses'], $userPlan->monthly_limit) ?? 0;

        $now = now();
        $createdAt = Carbon::parse($userPlan->created_at);
        $expiresAt = Carbon::parse($userPlan->expired_at);
        $diff = $now->diff($expiresAt);

        $totalDuration = $createdAt->diffInSeconds($expiresAt);
        $elapsed = $createdAt->diffInSeconds($now);
        $uses['progress_percent']  = $totalDuration > 0 ? min(100, ($elapsed / $totalDuration) * 100) : 100;

        if ($uses['progress_percent'] >= 90) {
            $uses['status_class'] = 'danger';
        } elseif ($uses['progress_percent'] >= 70) {
            $uses['status_class'] = 'warning';
        } else {
            $uses['status_class'] = 'success';
        }

        $isExpired = $now->greaterThanOrEqualTo($expiresAt);
        $diff = $now->diff($expiresAt);
        $uses['time_left'] = !$isExpired
            ? ($diff->m ? $diff->m . ' ' . str()->plural('month', $diff->m) : '') .
            ($diff->d ? ' ' . $diff->d . ' ' . str()->plural('day', $diff->d) : '') .
            ($diff->h ? ' ' . $diff->h . ' ' . str()->plural('hour', $diff->h) : '')
            : __('Expired');

        return view('Template::user.subscription.my_subscription', compact('pageTitle', 'userPlan', 'uses'));
    }

    protected function getDownloadProgressBarData($used, $limit)
    {
        return getAmount(($used / $limit) * 100);
    }
}
