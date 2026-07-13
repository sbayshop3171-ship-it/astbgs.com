@extends('admin.layouts.app')

@section('panel')
    <div class="row g-4">
        <div class="col-md-6">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card full-view">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6">
                                    <h5 class="card-title mb-0">@lang('Total Plan History')</h5>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <button class="exit-btn">
                                            <i class="fullscreen-open las la-compress" onclick="openFullscreen();"></i>
                                            <i class="fullscreen-close las la-compress-arrows-alt" onclick="closeFullscreen();"></i>
                                        </button>
                                        <select class="widget_select" name="invest_time">
                                            <option value="week">@lang('Current Week')</option>
                                            <option value="month">@lang('Current Month')</option>
                                            <option value="year" selected>@lang('Current Year')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center pb-0 px-0">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <p>@lang('This') <span class="time_type"></span> @lang('history')</p>
                                </div>
                                <div class="col-md-4">
                                    <h3><span>{{ gs('cur_sym') }}</span><span class="total_purchase"></span></h3>
                                </div>
                                <div class="col-md-4">
                                    <p class="up_down">

                                    </p>
                                </div>
                            </div>
                            <div class="my_invest_canvas"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">@lang('Profit/Loss Statistics by Plan')</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div class="chart-info">
                                    <a class="chart-info-toggle" href="#">
                                        <img class="chart-info-img" src="{{ asset('assets/images/collapse.svg') }}" alt="image">
                                    </a>
                                    <div class="chart-info-content">
                                        <ul class="chart-info-list">
                                            @foreach ($displayData as $planName => $data)
                                                <li class="chart-info-list-item">
                                                    <i class="fas fa-plane planPointInterest me-2"></i>
                                                    {{ __($planName) }} →
                                                    <span class="{{ $data['type'] === '+' ? 'text--success' : 'text--danger' }}">
                                                        {{ showAmount($data['net']) }}
                                                        ({{ $data['margin'] }}%)
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-sm-6">
                                            <h5 class="card-title mb-0">@lang('Plan Profit /Loss')</h5>
                                        </div>
                                        <div class="col-sm-6 text-sm-end">
                                            <select class="widget_select" id="plan_statistics_time" name="invest_interest_time">
                                                <option value="all">@lang('All Time')</option>
                                                <option value="week">@lang('Current Week')</option>
                                                <option value="month">@lang('Current Month')</option>
                                                <option value="year">@lang('Current Year')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="interest-scheme">
                                <div class="interest-scheme__content">
                                    <p class="mb-0">@lang('Received')</p>
                                    <h5 class="mb-1 text-success planReceivedAmount"></h5>

                                </div>
                                <div class="interest-scheme__content text-sm-center">
                                    <p class="mb-0 font-12">@lang('Paid')</p>
                                    <h5 class="mb-1 text--warning counter planPaidAmount"></h5>

                                </div>
                                <div class="interest-scheme__content text-sm-end">
                                    <p class="mb-0 font-12"><span class="text--success profit-area">@lang('Profit')</span><span class="text--danger loss-area">@lang('Loss')</span></p>
                                    <h5 class="mb-1 profitLossAmount"></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="row align-items-center g-2">
                                <div class="col-sm-6">
                                    <h5 class="card-title mb-0">@lang('Invest & Commission')</h5>
                                </div>
                                @if (isset($firstInvestYear->date))
                                    <div class="col-sm-6">
                                        <div class="pair-option">
                                            <select class="widget_select" name="plan_id">
                                                <option value="0">
                                                    @lang('All')
                                                </option>
                                                @foreach ($plans as $plan)
                                                    <option value="{{ $plan->id }}">
                                                        {{ __($plan->name) }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <select class="widget_select" name="invest_interest_year">
                                                @for ($i = $firstInvestYear->date; $i <= date('Y'); $i++)
                                                    <option value="{{ $i }}" @if (date('Y') == $i) selected @endif>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <select class="widget_select" name="invest_interest_month">
                                                <option value="01" @if (date('m') == '01') selected @endif>
                                                    @lang('January')</option>
                                                <option value="02" @if (date('m') == '02') selected @endif>
                                                    @lang('February')</option>
                                                <option value="03" @if (date('m') == '03') selected @endif>
                                                    @lang('March')</option>
                                                <option value="04" @if (date('m') == '04') selected @endif>
                                                    @lang('April')</option>
                                                <option value="05" @if (date('m') == '05') selected @endif>
                                                    @lang('May')</option>
                                                <option value="06" @if (date('m') == '06') selected @endif>
                                                    @lang('June')</option>
                                                <option value="07" @if (date('m') == '07') selected @endif>
                                                    @lang('July')</option>
                                                <option value="08" @if (date('m') == '08') selected @endif>
                                                    @lang('August')</option>
                                                <option value="09" @if (date('m') == '09') selected @endif>
                                                    @lang('September')</option>
                                                <option value="10" @if (date('m') == '10') selected @endif>
                                                    @lang('October')</option>
                                                <option value="11" @if (date('m') == '11') selected @endif>
                                                    @lang('November')</option>
                                                <option value="12" @if (date('m') == '12') selected @endif>
                                                    @lang('December')</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas class="chartjs-chart" id="chartjs-boundary-area-chart" height="80"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center ">
                                <h5 class="card-title mb-0">@lang('Recent Plan History')</h5>
                                <a href="{{ route('admin.report.plan.history') }}" class="text--primary mb-0">@lang('View All History')</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="plan-list d-flex flex-wrap flex-xxl-column gap-3 gap-xxl-0">
                                @foreach ($recentHistories as $history)
                                    <div class="plan-item-two">
                                        <div class="plan-info plan-inner-div">
                                            <div class="plan-name fw-bold">{{ $history->plan->name }}</div>
                                            <p>
                                                @if ($history->history_type == '+')
                                                    <small class="text--success"> @lang('Received')</small>
                                                @else
                                                    <small class="text--danger"> @lang('Paid')</small>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="plan-start plan-inner-div">
                                            <p class="plan-label">@lang('Remark')</p>
                                            <p class="plan-value date">
                                                {{ __(keyToTitle($history->remark)) }}
                                        </div>
                                        <div class="plan-end plan-inner-div">
                                            <p class="plan-label">@lang('Action at')</p>
                                            <p class="plan-value date">
                                                {{ showDateTime($history->created_at, 'd M, y h:i A') }}</p>
                                        </div>
                                        <div class="plan-amount plan-inner-div text-end">
                                            <p class="plan-label">@lang('Amount')</p>
                                            {{ showAmount($history->amount) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .widget_select {
            padding: 3px 3px;
            font-size: 13px;
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script>
        "use strict";
        (function($) {

            $('[name=invest_time]').on('change', function() {
                let time = $(this).val();
                var url = "{{ route('admin.plan.report.statistics') }}";
                $.get(url, {
                    time: time
                }, function(response) {
                    $('.time_type').text(time);
                    $('.total_purchase').text(response.total_purchase.toFixed(2));

                    let upDown = `<small>Previous ${time} invest was zero</small>`;
                    if (response.invest_diff != 0) {
                        if (response.up_down == 'up') {
                            var className = 'success'
                        } else {
                            var className = 'danger';
                        }
                        upDown =
                            `<span class="badge badge-${className}-inverse font-16">${response.invest_diff}%<i class="las la-arrow-${response.up_down}"></i></span>`;
                    }

                    $('.up_down').html(upDown);
                    $('.my_invest_canvas').html(
                        '<canvas height="150" id="invest_chart" class="chartjs-chart mt-4"></canvas>'
                    )
                    var ctx = document.getElementById('invest_chart');
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(response.invests),
                            datasets: [{
                                data: Object.values(response.invests),
                                backgroundColor: [
                                    @for ($i = 0; $i < 365; $i++)
                                        '#6c5ce7',
                                    @endfor

                                ],
                                borderColor: [
                                    'rgba(231, 80, 90, 0.75)'
                                ],
                                borderWidth: 0,

                            }]
                        },
                        options: {
                            aspectRatio: 1,
                            responsive: true,
                            maintainAspectRatio: true,
                            elements: {
                                line: {
                                    tension: 0 // disables bezier curves
                                }
                            },
                            scales: {
                                xAxes: [{
                                    display: false
                                }],
                                yAxes: [{
                                    ticks: {
                                        suggestedMin: 0, // Set a minimum value
                                    },
                                    display: false
                                }]
                            },
                            legend: {
                                display: false,
                            },
                            tooltips: {
                                callbacks: {
                                    label: (tooltipItem, data) => data.datasets[0].data[
                                        tooltipItem.index] + ' {{ gs('cur_text') }}'
                                }
                            }
                        }
                    });
                });
            }).change();


            $('[name=invest_interest_time]').on('change', function() {
                let time = $(this).val();
                var url = "{{ route('admin.plan.report.profit.loss') }}";
                $.get(url, {
                    time: time
                }, function(response) {
                    $('.planReceivedAmount').text(`${response.received_amount}`);
                    $('.planPaidAmount').text(`${response.paid_amount}`);
                    $('.profitLossAmount').text(`${response.profit_loss}`);
                    $('.profitLossAmount').addClass(`${response.profit_loss_class}`);
                    if (response.profit_loss_class == 'text--success') {
                        $('.profit-area').removeClass('d-none');
                        $('.loss-area').addClass('d-none');
                    } else {
                        $('.profit-area').addClass('d-none');
                        $('.loss-area').removeClass('d-none');
                    }
                });
            }).change();



            var boundaryAreaChart = null;

            $('[name=invest_interest_year]').on('change', function() {
                let year = $(this).val();
                let planId = $('[name=plan_id]').val();
                let month = $('[name=invest_interest_month]').val();
                let url = "{{ route('admin.plan.report.commission.chart') }}";

                $.get(url, {
                    year: year,
                    month: month,
                    plan_id: planId
                }, function(response) {
                    if (boundaryAreaChart) {
                        boundaryAreaChart.destroy();
                    }

                    var boundaryAreaID = document.getElementById("chartjs-boundary-area-chart")
                        .getContext('2d');
                    boundaryAreaChart = new Chart(boundaryAreaID, {
                        type: 'line',
                        data: {
                            labels: response.keys,
                            datasets: [{
                                    backgroundColor: "rgba(110, 129, 220,0.2)",
                                    borderColor: "#6e81dc",
                                    pointBorderColor: "#6e81dc",
                                    pointBackgroundColor: "#6e81dc",
                                    pointBorderWidth: 0,
                                    data: response.invests,
                                    label: 'Invests',
                                    fill: 'start'
                                },
                                {
                                    backgroundColor: "rgba(252, 193, 0,0.2)",
                                    borderColor: "#fcc100",
                                    pointBorderColor: "#fcc100",
                                    pointBackgroundColor: "#fcc100",
                                    pointBorderWidth: 0,
                                    data: response.commissions,
                                    label: 'Commissions',
                                    fill: 'start'
                                }
                            ]
                        },
                        options: {
                            title: {
                                text: 'fill: start',
                                display: false
                            },
                            maintainAspectRatio: true,
                            spanGaps: true,
                            elements: {
                                point: {
                                    radius: 0,
                                }
                            },
                            plugins: {
                                filler: {
                                    propagate: false
                                }
                            },
                            legend: {
                                display: true
                            },
                            scales: {
                                x: {
                                    display: true,
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 0
                                    },
                                    grid: {
                                        color: '#dcdde1',
                                        lineWidth: 1,
                                        borderDash: [1]
                                    }
                                },
                                y: {
                                    display: true,
                                    grid: {
                                        color: '#dcdde1',
                                        lineWidth: 1,
                                        borderDash: [1],
                                        drawBorder: true,
                                        borderColor: '#dcdde1'
                                    }
                                }
                            }
                        }
                    });
                });
            }).trigger('change');

            $('[name=invest_interest_month]').on('change', function() {
                $('[name=invest_interest_year]').trigger('change');
            });
            $('[name=plan_id]').on('change', function() {
                $('[name=invest_interest_year]').trigger('change');
            });


            let chartToggle = $('.chart-info-toggle');
            let chartContent = $(".chart-info-content");
            if (chartToggle || chartContent) {
                chartToggle.each(function() {
                    $(this).on("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).siblings().toggleClass("is-open");
                    });
                });
                chartContent.each(function() {
                    $(this).on("click", function(e) {
                        e.stopPropagation();
                    });
                });
                $(document).on("click", function() {
                    chartContent.removeClass("is-open");
                });
            }

            $('.exit-btn').on('click', function() {
                $(this).toggleClass('active');
            });

        })(jQuery);

        var elems = document.querySelector(".full-view");

        function openFullscreen() {
            if (elems.requestFullscreen) {
                elems.requestFullscreen();
            } else if (elems.mozRequestFullScreen) {
                elems.mozRequestFullScreen();
            } else if (elems.webkitRequestFullscreen) {
                elems.webkitRequestFullscreen();
            } else if (elems.msRequestFullscreen) {
                elems.msRequestFullscreen();
            }
        }

        function closeFullscreen() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    </script>
@endpush

@push('style')
    <style>
        .plan-item-two {
            width: 100%;
            background-color: #fff;
            border: 1px solid #dfdfdf;
            padding: 15px;
            position: relative;
        }

        .plan-desc {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
        }

        .plan-item-two .plan-inner-div {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .plan-value {
            text-align: right;
        }

        .plan-item-two .plan-label {
            font-weight: 600;
        }

        .interest-scheme {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .chart-container {
            overflow: hidden;
        }

        .chart-info {
            position: relative;
            isolation: isolate;
        }

        .chart-info-toggle {
            display: inline-block;
        }

        .chart-info-img {
            width: 30px;
            transform: rotate(180deg);
            filter: invert(0.62) sepia(1) saturate(4.5) hue-rotate(199deg);
        }

        .chart-info-content {
            position: absolute;
            top: 30px;
            left: 0;
            border-radius: 3px;
            background: #fff;
            transform: translateX(-100%);
            transition: all 0.3s ease;
        }

        .chart-info-content.is-open {
            transform: translateX(0);
            box-shadow: 0 0 1.5rem rgba(18, 38, 63, 0.1);
        }

        .chart-info-list-item {
            display: flex;
            padding: 5px 15px;
            align-items: center;
        }

        .chart-info-list-item:first-child {
            padding-top: 10px;
        }

        .chart-info-list-item:last-child {
            padding-bottom: 10px;
        }

        .investments-scheme-arrow {
            display: none;
        }

        .investments-scheme {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .investments-scheme-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .progress-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 5px;
            margin-bottom: 5px;
        }

        .exit-btn {
            padding: 0;
            font-size: 30px;
            line-height: 1;
            color: #5b6e88;
            background: transparent;
            border: none;
            transition: all .3s ease;
        }

        .exit-btn:hover {
            color: #4634ff;
        }

        .exit-btn .fullscreen-close {
            margin-left: -25px;
            transition: all 0.3s;
            display: none;
        }

        .exit-btn.active .fullscreen-open {
            display: none;
        }

        .exit-btn.active .fullscreen-close {
            display: block;
        }

        @media screen and (min-width: 576px) {
            .interest-scheme {
                justify-content: space-between;
                flex-direction: row;
                gap: 1.5rem;
            }

            .pair-option {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 5px;
            }

            .investments-scheme-item {
                text-align: center;
            }

            .investments-scheme-group {
                width: 100%;
                flex-direction: row;
                justify-content: space-around;
            }

            .investments-scheme-arrow {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-around;
            }
        }

        @media screen and (min-width: 768px) {
            .interest-scheme {
                gap: .5rem;
            }
        }

        @media screen and (min-width: 1200px) {
            .plan-name {
                font-size: 14px;
                font-weight: 500 !important;
            }

            .plan-item-two .plan-inner-div {
                gap: 5px;
            }

            .plan-desc {
                justify-content: flex-start;
                gap: 5px;
                font-size: 14px;
                line-height: 1.2;
            }

            .plan-item-two {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        @media screen and (min-width: 1366px) {
            .plan-item-two {
                flex-direction: row;
                align-items: flex-start;
                gap: 5px;
                padding: 21px 15px;
            }

            .plan-item-two .plan-inner-div {
                flex-wrap: nowrap;
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
                flex-shrink: 0;
            }

            .plan-value {
                font-size: 12px;
                text-align: left;
            }

            .plan-info {
                width: 40%;
            }

            .plan-start {
                width: 20%;
            }

            .plan-end {
                width: 20%;
            }

            .plan-amount {
                width: 20%;
            }

            .chart-info-toggle {
                display: none;
            }

            .chart-info-content {
                position: unset;
                transform: translateX(0);
            }

            .chart-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .chart-info {
                flex-shrink: 0;
            }

            .investments-scheme-group {
                text-align: center;
            }
        }

        @media (min-width: 1400px) {
            .plan-item-two:not(:last-child) {
                border-bottom: 0;
            }

            .plan-item-two {
                padding: 15px;
            }

            .card-container {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .deposit-amount {
                font-size: 18px;
            }
        }

        @media (min-width: 1900px) {
            .card-container {
                padding-top: 10px;
                padding-bottom: 10px;
                gap: 1rem;
            }

            .card-gap {
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .chart-area--fixed {
                max-width: 350px;
            }

            .plan-item-two {
                padding: 14px 15px;
            }
        }
    </style>
@endpush
