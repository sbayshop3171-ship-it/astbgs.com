<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use App\Models\Earning;
use App\Models\NotificationLog;
use App\Models\Plan;
use App\Models\PlanHistory;
use App\Models\Transaction;
use App\Models\UserLogin;
use App\Models\UserPlan;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function transaction(Request $request, $userId = null) {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::searchable(['trx', 'user:username'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->with('user');
        if ($userId) {
            $transactions = $transactions->where('user_id', $userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request) {
        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip) {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request) {
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id) {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function earningHistory(Request $request) {
        $pageTitle = 'Earning History';
        $logs = Earning::orderByDesc('id')->searchable(['user:username'])->dateFilter()->with('user', 'author', 'product')->paginate(getPaginate());
        return view('admin.reports.earning', compact('pageTitle', 'logs'));
    }

    public function downloadLog($product_id = 0, $user_id = 0) {
        $pageTitle = 'Download History';
        $query = DownloadLog::query();

        if (!empty($user_id) && $user_id != 0) {
            $query = $query->where('user_id', $user_id);
        }
        if (!empty($product_id) && $product_id != 0) {
            $query = $query->where('product_id', $product_id);
        }

        $logs = $query->orderByDesc('id')->with('user', 'product.author')->paginate(getPaginate());
        return view('admin.reports.downloads', compact('pageTitle', 'logs'));
    }

    public function subscriptionHistory(Request $request, $userId = null) {
        $pageTitle = 'Subscription History';
        $plans     = Plan::searchable(['name'])->orderByDesc('id')->get();
        $subscriptions = UserPlan::searchable(['user:username', 'plan:name'])->filter(['plan_id', 'plan_duration'])->dateFilter()->orderBy('id', 'desc')->with('user', 'plan')->paginate(getPaginate());
        return view('admin.reports.subscription_history', compact('pageTitle', 'subscriptions', 'plans'));
    }

    public function planHistory() {
        $pageTitle = 'Plan History';
        $histories     = PlanHistory::searchable(['plan:name'])->dateFilter()->with('plan')->orderByDesc('id')->paginate(getPaginate());
        return view('admin.reports.plan_history', compact('pageTitle', 'histories'));
    }
}
