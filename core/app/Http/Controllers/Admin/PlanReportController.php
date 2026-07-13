<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanHistory;
use Illuminate\Http\Request;

class PlanReportController extends Controller {
    public function index() {
        $pageTitle = 'Plan Analytics';
        $plans           = Plan::orderByDesc('id')->get();
        $recentHistories = PlanHistory::with('plan')->orderBy('id', 'desc')->limit(4)->get();
        $firstInvestYear = PlanHistory::selectRaw("DATE_FORMAT(created_at, '%Y') as date")->first();
        $histories = PlanHistory::with('plan')->selectRaw("
                SUM(CASE WHEN history_type = '+' THEN amount ELSE 0 END) as total_profit,
                SUM(CASE WHEN history_type = '-' THEN amount ELSE 0 END) as total_loss,
                plan_id
            ")->groupBy('plan_id')->get();

        $displayData = [];
        $totalNet = 0;

        foreach ($histories as $history) {
            $planName = $history->plan->name;
            $profit = (float) $history->total_profit;
            $loss = (float) $history->total_loss;
            $net = $profit - $loss;

            $netType = $net >= 0 ? '+' : '-';
            $netAbs = abs($net);

            $displayData[$planName] = [
                'amount'     => $netAbs,
                'type'       => $netType,
                'net'        => $net,
                'profit'     => $profit,
                'loss'       => $loss,
                'margin'     => $profit > 0 ? round(($net / $profit) * 100, 2) : 0,
            ];

            $totalNet += $netAbs;
        }

        return view('admin.reports.plan', compact('pageTitle', 'plans', 'recentHistories', 'displayData', 'firstInvestYear'));
    }

    public function investStatistics(Request $request) {

        if ($request->time == 'year') {
            $time     = now()->startOfYear();
            $prevTime = now()->startOfYear()->subYear();
        } elseif ($request->time == 'month') {
            $time     = now()->startOfMonth();
            $prevTime = now()->startOfMonth()->subMonth();
        } else {
            $time     = now()->startOfWeek();
            $prevTime = now()->startOfWeek()->subWeek();
        }

        $invests     = PlanHistory::where('created_at', '>=', $time)->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at, '%Y-%m-%d') as date")->groupBy('date')->get();
        $totalPurchase = $invests->sum('amount');

        $invests = $invests->mapWithKeys(function ($invest) {
            return [
                $invest->date => (float) $invest->amount,
            ];
        });

        $prevInvest = PlanHistory::where('created_at', '>=', $prevTime)->where('created_at', '<', $time)->sum('amount');
        $investDiff = ($prevInvest ? $totalPurchase / $prevInvest * 100 - 100 : 0);
        if ($investDiff > 0) {
            $upDown = 'up';
        } else {
            $upDown = 'down';
        }
        $investDiff = abs($investDiff);
        return [
            'invests'        => $invests,
            'total_purchase' => $totalPurchase,
            'invest_diff'    => round($investDiff, 2),
            'up_down'        => $upDown,
        ];
    }


    public function investProfitLossStatistics(Request $request) {
        if ($request->time == 'year') {
            $time = now()->startOfYear();
        } elseif ($request->time == 'month') {
            $time = now()->startOfMonth();
        } elseif ($request->time == 'week') {
            $time = now()->startOfWeek();
        } else {
            $time = date('0000-00-00 00:00:00');
        }

        $receivedAmount = PlanHistory::where('history_type', '+')->where('created_at', '>=', $time)->sum('amount');
        $paidAmount     = PlanHistory::where('history_type', '-')->where('created_at', '>=', $time)->sum('amount');
        $profitLoss     = $receivedAmount - $paidAmount;
        if ($profitLoss > 0) {
            $profitLossClass = 'text--success';
        } else {
            $profitLossClass = 'text--danger';
        }
        return [
            'received_amount' => showAmount($receivedAmount),
            'paid_amount'     => showAmount($paidAmount),
            'profit_loss'     => showAmount($profitLoss),
            'profit_loss_class'     => $profitLossClass,
        ];
    }

    public function investCommissionChart(Request $request) {
        $investQuery = PlanHistory::whereYear('created_at', $request->year)
            ->where('history_type', '+')
            ->whereMonth('created_at', $request->month);
        if ($request->plan_id && $request->plan_id != 0) {
            $investQuery->where('plan_id', $request->plan_id);
        }
        $investsQuery = $investQuery
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at, '%d') as date")
            ->groupBy('date')
            ->get();
        $investsDate = $investsQuery->pluck('date')->toArray();
        $interestQuery = PlanHistory::whereYear('created_at', $request->year)
            ->where('history_type', '-')
            ->whereMonth('created_at', $request->month);
        if ($request->plan_id && $request->plan_id != 0) {
            $interestQuery->where('plan_id', $request->plan_id);
        }
        $interests = $interestQuery
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at, '%d') as date")
            ->groupBy('date')
            ->get();
        $interestsDate = $interests->pluck('date')->toArray();
        $dataDates = array_unique(array_merge($investsDate, $interestsDate));
        sort($dataDates);
        $investsData = [];
        $commissionData = [];
        foreach ($dataDates as $date) {
            $investsData[] = $investsQuery->firstWhere('date', $date)->amount ?? 0;
            $commissionData[] = $interests->firstWhere('date', $date)->amount ?? 0;
        }
        return [
            'keys'        => array_values($dataDates),
            'invests'     => $investsData,
            'commissions' => $commissionData,
        ];
    }
}
