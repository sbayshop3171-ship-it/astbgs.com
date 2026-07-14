@extends('Template::layouts.master')
@section('content')
    @php
        $user = auth()->user();
        $isWalletTopupMode = !isset($plan) && !isset($order);
        $payableAmount = $isWalletTopupMode ? (float) ($walletTopupAmount ?? 0) : (float) (isset($order) ? $order->total : $plan->price);
        $supportsWalletPayment = !$isWalletTopupMode;
        $walletBalance = (float) ($user->wallet_balance ?? 0);
        $walletShortfall = max($payableAmount - $walletBalance, 0);
    @endphp

    <div class="row justify-content-center gy-4">
        <div class="col-xxl-9 col-xl-10 col-lg-11">
            @if (isset($plan) && userActivePlan())
                @php
                    $userPlan = userActivePlan();
                    $planName = $userPlan?->plan?->name ?? '';
                    $durationText = $userPlan->plan_duration == 1 ? __('Monthly') : __('Yearly');
                @endphp
                <h5 class="mb-0">@lang("Your are currently using ':plan - :duration' plan", ['plan' => $planName, 'duration' => $durationText])</h5>
            @endif

            @if ($supportsWalletPayment)
                <div class="card custom--card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0">@lang('Wallet Payment')</h6>
                        <span class="badge badge--base">@lang('Instant checkout')</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="small text-muted mb-1">@lang('Available')</div>
                                    <div class="fw-bold">{{ showAmount($walletBalance) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="small text-muted mb-1">@lang('Needed')</div>
                                    <div class="fw-bold">{{ showAmount($payableAmount) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="small text-muted mb-1">@lang('Shortfall')</div>
                                    <div class="fw-bold @if ($walletShortfall > 0) text--warning @else text--success @endif">
                                        {{ showAmount($walletShortfall) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            @if ($walletShortfall <= 0)
                                <form action="{{ isset($order) ? route('user.orders.wallet.pay', $order->id) : route('user.subscription.wallet.pay', $plan->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn--base w-100">
                                        @lang('Pay from Wallet')
                                    </button>
                                </form>
                            @else
                                <div class="alert alert--warning mb-3" role="alert">
                                    @lang('Your wallet does not have enough balance for this payment.')
                                </div>
                                <a href="{{ route('user.deposit.index', ['amount' => getAmount($walletShortfall)]) }}"
                                    class="btn btn-outline--base w-100">
                                    @lang('Add Money Now')
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($isWalletTopupMode)
                <div class="card custom--card mb-4">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h6 class="mb-1">@lang('Wallet Balance')</h6>
                            <p class="mb-0 text-muted">@lang('Add money now and reuse it for orders or membership payments later.')</p>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">@lang('Available Now')</div>
                            <div class="fw-bold fs-5">{{ showAmount($walletBalance) }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h6 class="mb-0">
                    @if ($isWalletTopupMode)
                        @lang('Add Money with Gateway')
                    @else
                        @lang('Pay with Gateway')
                    @endif
                </h6>
                @if ($isWalletTopupMode)
                    <a href="{{ route('user.transactions', ['balance_type' => \App\Constants\Status::BALANCE_TYPE_WALLET]) }}"
                        class="btn btn-outline--base btn--sm">
                        <i class="las la-list"></i> @lang('Wallet Transactions')
                    </a>
                @endif
            </div>

            <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
                @csrf
                <input type="hidden" name="currency">
                @if (isset($plan))
                    <input type="hidden" name="user_plan" value="{{ $plan->id }}">
                @endif
                @if (isset($order))
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                @endif

                <div class="gateway-card">
                    <div class="row justify-content-center gy-sm-4 gy-3">
                        <div class="col-md-6">
                            <div class="payment-system-list is-scrollable gateway-option-list p-0">
                                @foreach ($gatewayCurrency as $data)
                                    <label for="{{ titleToKey($data->name) }}"
                                        class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                        <div class="payment-item__info">
                                            <span class="payment-item__check"></span>
                                            <span class="payment-item__name">{{ __($data->name) }}</span>
                                        </div>
                                        <div class="payment-item__thumb">
                                            <img class="payment-item__thumb-img"
                                                src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                alt="@lang('payment-thumb')">
                                        </div>
                                        <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden
                                            data-gateway='@json($data)' type="radio" name="gateway"
                                            value="{{ $data->method_code }}" @checked(old('gateway', $loop->first) == $data->method_code)
                                            data-min-amount="{{ showAmount($data->min_amount) }}"
                                            data-max-amount="{{ showAmount($data->max_amount) }}">
                                    </label>
                                @endforeach
                                @if ($gatewayCurrency->count() > 4)
                                    <button type="button" class="payment-item__btn more-gateway-option">
                                        <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                        <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-system-list p-3">
                                <div class="deposit-info">
                                    <div class="deposit-info__title">
                                        <p class="text mb-0">@lang('Amount')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        @if ($isWalletTopupMode)
                                            <div class="deposit-info__input-group input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" step="any" min="0" class="form-control amount"
                                                    name="wallet_topup_amount"
                                                    value="{{ old('wallet_topup_amount', $walletTopupAmount) }}"
                                                    placeholder="@lang('Enter top-up amount')" autocomplete="off">
                                            </div>
                                        @else
                                            <p class="fw-bold mb-0">{{ showAmount($payableAmount) }}</p>
                                            <input type="hidden" class="amount" value="{{ $payableAmount }}" autocomplete="off">
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="deposit-info hideInfo">
                                    <div class="deposit-info__title">
                                        <p class="text has-icon">@lang('Limit')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="gateway-limit">@lang('0.00')</span></p>
                                    </div>
                                </div>
                                <div class="deposit-info hideInfo">
                                    <div class="deposit-info__title">
                                        <p class="text has-icon">@lang('Processing Charge')
                                            <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')"
                                                class="processing-fee-info"><i class="las la-info-circle"></i></span>
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="processing-fee">@lang('0.00')</span> {{ __(gs('cur_text')) }}</p>
                                    </div>
                                </div>

                                <div class="deposit-info total-amount">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('Total')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="final-amount">@lang('0.00')</span> {{ __(gs('cur_text')) }}</p>
                                    </div>
                                </div>

                                <div class="deposit-info gateway-conversion d-none total-amount">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('Conversion')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"></p>
                                    </div>
                                </div>
                                <div class="deposit-info conversion-currency d-none total-amount">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('In') <span class="gateway-currency"></span></p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="in-currency"></span></p>
                                    </div>
                                </div>
                                <div class="d-none crypto-message">
                                    @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                                </div>
                                <button type="submit" class="btn btn--base w-100 mt-3" disabled>
                                    @if ($isWalletTopupMode)
                                        @lang('Add Money')
                                    @else
                                        @lang('Continue with Gateway')
                                    @endif
                                </button>
                                <div class="info-text pt-2">
                                    <p class="text fs-14">
                                        @if ($isWalletTopupMode)
                                            @lang('Your wallet top-up will stay available for future orders and membership payments after payment success.')
                                        @else
                                            @lang('Ensuring your payment completed safely through our secure payment gateways with world-class payment options.')
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            let amount = parseFloat($('.amount').val() || 0);
            let gateway;
            let minAmount = 0;
            let maxAmount = 0;

            $('.amount').on('input', function() {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function() {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                gateway = gatewayElement.data('gateway');
                minAmount = parseFloat(gatewayElement.data('min-amount')) || 0;
                maxAmount = parseFloat(gatewayElement.data('max-amount')) || 0;

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`;
                $(".processing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                calculation();
            }

            $(".more-gateway-option").on("click", function() {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) {
                    return;
                }

                $(".gateway-limit").text(minAmount.toFixed(2) + " - " + maxAmount.toFixed(2));

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (!amount || amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height');

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span> <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ? 8 : 2));
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height');
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            $('.gateway-input').trigger('change');
        })(jQuery);
    </script>
@endpush
