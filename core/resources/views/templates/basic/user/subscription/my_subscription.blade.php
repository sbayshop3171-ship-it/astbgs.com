@extends('Template::layouts.master')
@section('content')
    <div class="subscription-container">
        <div class="subscription-header">
            <h6>@lang('Plan Details')</h6>
        </div>

        <div class="subscription-details">
            <div class="detail-item">
                <div class="detail-label">@lang('Plan')</div>
                <div class="detail-value plan-name">
                    {{ __($userPlan->plan?->name ?? '') }}
                    @if ($userPlan->plan->is_popular)
                        <span class="badge badge--base">@lang('Popular')</span>
                    @endif
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">@lang('Duration')</div>
                <div class="detail-value">{{ $userPlan->plan_duration == 1 ? __('Monthly') : __('Yearly') }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">@lang('Price')</div>
                <div class="detail-value">
                    {{ showAmount($userPlan->price) }}/{{ $userPlan->plan_duration == 1 ? __('month') : __('year') }}
                </div>
            </div>
        </div>
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="detail-item h-100">
                    <div class="detail-label">@lang('Created At')</div>
                    <div class="detail-value">
                        {{ showDateTime($userPlan->created_at) }}
                        <br>{{ diffForHumans($userPlan->created_at) }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-item limit-wrapper h-100">
                    <div class="limit">
                        <div class="top">@lang('Daily Limit')</div>
                        <div class="bottom">{{ $userPlan->daily_limit }} {{ str()->plural('item', $userPlan->daily_limit) }}</div>
                    </div>
                    <div class="limit">
                        <div class="top">@lang('Weekly Limit')</div>
                        <div class="bottom">{{ $userPlan->weekly_limit }} {{ str()->plural('item', $userPlan->weekly_limit) }}</div>
                    </div>
                    <div class="limit">
                        <div class="top">@lang('Monthly Limit')</div>
                        <div class="bottom">{{ $userPlan->monthly_limit }} {{ str()->plural('item', $userPlan->monthly_limit) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-item h-100">
                    <div class="detail-label">@lang('Expired At')</div>
                    <div class="detail-value">{{ showDateTime($userPlan->expired_at) }}</div>

                    <div class="expiry-status">
                        <div class="days-left {{ $uses['status_class'] }}">
                            {{ $uses['time_left'] }}
                        </div>
                    </div>

                    <div class="progress-container">
                        <div class="progress-bar" style="width: {{ $uses['progress_percent'] }}%; background-color: text--{{ $uses['status_class'] }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="limits-section">
        <div class="limits-header">
            <h6>@lang('Usage Limits')</h6>
        </div>
        <div class="limits-content">
            <div class="limit-item">
                <div class="limit-title">@lang('Daily Limit')</div>
                <div class="limit-usage">
                    <div class="limit-chart" style="background: conic-gradient(hsl(var(--base)) 0% {{ $uses['daily_percent'] }}%, #e9ecef {{ $uses['daily_percent'] }}% 100%)">
                        <span class="limit-percentage">{{ $uses['daily_percent'] }}%</span>
                    </div>
                </div>
                <div class="limit-details">{{ $uses['today_uses'] }} @lang('of')
                    {{ $userPlan->daily_limit }}
                    @lang('downloads')
                </div>
            </div>

            <div class="limit-item">
                <div class="limit-title">@lang('Weekly Limit')</div>
                <div class="limit-usage">
                    <div class="limit-chart" style="background: conic-gradient(hsl(var(--base)) 0% {{ $uses['weekly_percent'] }}%, #e9ecef {{ $uses['weekly_percent'] }}% 100%)">
                        <span class="limit-percentage">{{ $uses['weekly_percent'] }}%</span>
                    </div>
                </div>
                <div class="limit-details">{{ $uses['weekly_uses'] }} @lang('of')
                    {{ $userPlan->weekly_limit }}
                    @lang('downloads')
                </div>
            </div>
            <div class="limit-item">
                <div class="limit-title">@lang('Monthly Limit')</div>
                <div class="limit-usage">
                    <div class="limit-chart" style="background: conic-gradient(hsl(var(--base)) 0% {{ $uses['monthly_percent'] }}%, #e9ecef {{ $uses['monthly_percent'] }}% 100%)">
                        <span class="limit-percentage">{{ $uses['monthly_percent'] }}%</span>
                    </div>
                </div>
                <div class="limit-details">{{ $uses['monthly_uses'] }} @lang('of')
                    {{ $userPlan->monthly_limit }}
                    @lang('downloads')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        :root {
            --secondary-color: #6c757d;
            --warning-color: hsl(var(--warning));
            --dark-color: #343a40;
            --border-radius: 8px;
        }

        .subscription-container {
            margin: 0 auto;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            padding: 20px;
        }

        .subscription-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            background: rgb(248 249 250);
            border-radius: var(--border-radius);
            padding: 15px;
            transition: all 0.3s ease;
        }

        .detail-label {
            color: var(--secondary-color);
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
        }

        .plan-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .expiry-status {
            display: flex;
            align-items: center;
            margin-top: 8px;
        }

        .days-left {
            font-weight: bold;
            color: hsl(var(--base));
        }

        .days-left.warning {
            color: hsl(var(--warning));
        }

        .progress-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 15px;
            margin-top: 10px;
            height: 8px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: hsl(var(--base));
            border-radius: 15px;
            width: 75%;
        }

        .subscription-footer {
            background: #f8f9fa;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
        }

        .limits-section {
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .limits-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .limit-item {
            text-align: center;
            padding: 15px;
            border-radius: var(--border-radius);
            background: #f8f9fa;
        }

        .limit-title {
            font-size: 14px;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .limit-usage {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .limit-chart {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(hsl(var(--base)) 0% 70%,
                    #e9ecef 70% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .limit-chart::before {
            content: "";
            position: absolute;
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
        }

        .limit-percentage {
            position: relative;
            font-weight: bold;
            font-size: 18px;
        }

        .limit-details {
            margin-top: 10px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .subscription-details {
                grid-template-columns: 1fr;
            }

            .limits-content {
                grid-template-columns: 1fr;
            }
        }

        .limit-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .limit-wrapper .limit {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .limit-wrapper .top {
            font-size: 16px;
            font-weight: 500;
        }

        .limit-wrapper .bottom {
            font-size: 20px;
            font-weight: 800;
        }

        .limit-wrapper .limit:not(:last-child) {
            padding-right: 25px;
            border-right: 3px solid #c3c3c3;
        }

        @media screen and (max-width:440px),
        screen and (min-width:768px) and (max-width:1399px) {
            .limit-wrapper {
                flex-direction: column;
            }

            .limit-wrapper .limit {
                flex-direction: row;
                justify-content: space-between;
                width: 100%;
            }

            .limit-wrapper .bottom {
                font-size: 16px;
                font-weight: 600px;
            }

            .limit-wrapper .limit:not(:last-child) {
                padding-right: 0px;
                border-right: none;
                padding-bottom: 15px;
                margin-bottom: 15px;
                border-bottom: 3px solid #c3c3c3;
            }
        }
    </style>
@endpush
