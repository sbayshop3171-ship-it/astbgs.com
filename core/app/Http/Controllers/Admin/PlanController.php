<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller {
    public function index() {
        $pageTitle = "Membership Plans";
        $plans     = Plan::searchable(['name'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function form($id = 0) {
        $pageTitle = 'Add Membership Plan';
        $plan = null;

        if ($id) {
            $plan = Plan::findOrFail($id);
            $pageTitle = 'Edit ' . keyToTitle($plan->name);
        }

        return view('admin.plan.form', compact('pageTitle', 'plan'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'name'           => 'required|string',
            'monthly_price'  => 'required|numeric|gte:0',
            'yearly_price'   => 'required|numeric|gte:0',

            'daily_limit'    => 'required|integer|gt:0',
            'weekly_limit'   => 'required|integer|gt:daily_limit',
            'monthly_limit'  => 'required|integer|gt:weekly_limit',
        ]);

        if ($id) {
            $plan         = Plan::findOrFail($id);
            $notification = "Plan updated successfully";
        } else {
            $plan         = new Plan();
            $notification = "Plan created successfully";
            RequiredConfig::configured('membership_plan');
        }

        $this->savePlan($plan, $request);

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    protected function savePlan($plan, $request) {
        $plan->name                   = $request->name;
        $plan->monthly_price          = $request->monthly_price;
        $plan->yearly_price           = $request->yearly_price;
        $plan->daily_limit            = $request->daily_limit;
        $plan->weekly_limit           = $request->weekly_limit;
        $plan->monthly_limit          = $request->monthly_limit;
        $plan->save();
    }

    public function status($id) {
        return Plan::changeStatus($id);
    }

    public function popular($id) {
        return Plan::changeStatus($id, 'is_popular');
    }
}
