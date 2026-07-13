<div class="container">
    <div class="section-heading style-center">
        <h4 class="section-heading__title s-highlight" data-s-break="1" data-s-length="1">{{ __($content?->heading ?? 'Membership Plans') }}</h4>
        <p class="section-heading__desc">{{ __($content?->subheading ?? 'Choose the right plan when subscriptions are available.') }}</p>
    </div>

    @if ($plans->isEmpty())
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center py-5">
                    <h5 class="mb-2">@lang('No membership plans available right now')</h5>
                    <p class="mb-0 text-muted">@lang('Plans will appear here once they are configured from the admin panel.')</p>
                </div>
            </div>
        </div>
    @else
    <div class="pricing__tabs pb-2">
        <ul class="nav nav-pills custom--tab pricing-tabs" id="pricing-tab" role="tablist">
            <li class="nav-item-background"></li>
            <li class="nav-item" role="presentation">
                <button class="nav-link pricing__tabs-btn active" id="monthly-tab-btn" data-bs-toggle="pill"
                    data-bs-target="#monthlyTab" type="button" role="tab" aria-controls="monthlyTab"
                    aria-selected="true">
                   @lang('Monthly')
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link pricing__tabs-btn" id="yearly-tab-btn" data-bs-toggle="pill"
                    data-bs-target="#yearlyTab" type="button" role="tab" aria-controls="yearlyTab"
                    aria-selected="false">
                    @lang('Yearly')
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="pricing-tab-content">

        {{-- Monthly Tab --}}
        <div class="tab-pane fade show active" id="monthlyTab" role="tabpanel" aria-labelledby="monthly-tab-btn">
            <div class="row gy-4 justify-content-center">
                @foreach ($plans as $plan)
                    @php
                        $price = $plan->monthly_price;
                        $userPlan = userActivePlan();
                        $isActive = $userPlan && activePlan($plan->id, $userPlan->plan_duration, 'monthly');
                        $isActiveMonthly = $isActive;
                        $isActiveYearly = $userPlan && activePlan($plan->id, $userPlan->plan_duration, 'yearly');
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing__card">
                            <div class="pricing {{ $plan->is_popular ? 'popular' : '' }}">
                                <div class="pricing__top">
                                    <span class="plan-name">{{ $plan->name }}</span>
                                    <h2 class="pricing__title">
                                        {{ gs('cur_sym') . showAmount($price, currencyFormat: false) }}<span><sub>/
                                                @lang('Monthly')</sub></span>
                                    </h2>
                                </div>
                                <p class="text">@lang('Save') {{ showAmount($plan->save_amount) }}
                                    @lang('with yearly plan')</p>

                                <div class="pricing__bottom">
                                    <ul>
                                        <li><i class="las la-check"></i>
                                            @lang('Daily') {{ $plan->daily_limit }} @lang('Item Downloads')
                                        </li>
                                        <li><i class="las la-check"></i>
                                            @lang('Weekly') {{ $plan->weekly_limit }} @lang('Item Downloads')
                                        </li>
                                        <li><i class="las la-check"></i>
                                            @lang('Monthly') {{ $plan->monthly_limit }} @lang('Item Downloads')
                                        </li>
                                    </ul>

                                    <div class="pricing__button">
                                        @guest
                                            <a class="btn btn--base"
                                                href="{{ route('user.login') }}">@lang('Get Started')</a>
                                        @else
                                            <div class="plan-action-buttons"
                                                data-active-monthly="{{ $isActiveMonthly ? 'true' : 'false' }}"
                                                data-active-yearly="{{ $isActiveYearly ? 'true' : 'false' }}">
                                                <form action="{{ route('user.plan.subscribe') }}" method="POST"
                                                    class="plan-form">
                                                    @csrf
                                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                    <input type="hidden" name="plan_type" value="monthly"
                                                        class="plan-type-input">
                                                    <button type="submit"
                                                        class="btn btn--base plan-purchase-btn">@lang('Get Started')</button>
                                                </form>
                                                <button class="btn btn--success plan-current-btn d-none"
                                                    disabled>{{ __('Current Plan') }}</button>
                                            </div>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Yearly Tab --}}
        <div class="tab-pane fade" id="yearlyTab" role="tabpanel" aria-labelledby="yearly-tab-btn">
            <div class="row gy-4 justify-content-center">
                @foreach ($plans as $plan)
                    @php
                        $price = $plan->yearly_price;
                        $userPlan = userActivePlan();
                        $isActive = $userPlan && activePlan($plan->id, $userPlan->plan_duration, 'yearly');
                        $isActiveMonthly = $userPlan && activePlan($plan->id, $userPlan->plan_duration, 'monthly');
                        $isActiveYearly = $isActive;
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing__card">
                            <div class="pricing {{ $plan->is_popular ? 'popular' : '' }}">
                                <div class="pricing__top">
                                    <span class="plan-name">{{ $plan->name }}</span>
                                    <h2 class="pricing__title">
                                        {{ gs('cur_sym') . showAmount($price, currencyFormat: false) }}<span><sub>/
                                                @lang('Yearly')</sub></span>
                                    </h2>
                                </div>
                                <p class="text">@lang('Saving') {{ showAmount($plan->save_amount) }}
                                    @lang('compared to monthly')</p>

                                <div class="pricing__bottom">
                                    <ul>
                                        <li><i class="las la-check"></i>
                                            @lang('Daily') {{ $plan->daily_limit }} @lang('Item Downloads')
                                        </li>
                                        <li><i class="las la-check"></i>
                                            @lang('Weekly') {{ $plan->weekly_limit }} @lang('Item Downloads')
                                        </li>
                                        <li><i class="las la-check"></i>
                                            @lang('Monthly') {{ $plan->monthly_limit }} @lang('Item Downloads')
                                        </li>
                                    </ul>

                                    <div class="pricing__button">
                                        @guest
                                            <a class="btn btn--base"
                                                href="{{ route('user.login') }}">@lang('Get Started')</a>
                                        @else
                                            <div class="plan-action-buttons"
                                                data-active-monthly="{{ $isActiveMonthly ? 'true' : 'false' }}"
                                                data-active-yearly="{{ $isActiveYearly ? 'true' : 'false' }}">
                                                <form action="{{ route('user.plan.subscribe') }}" method="POST"
                                                    class="plan-form">
                                                    @csrf
                                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                    <input type="hidden" name="plan_type" value="yearly"
                                                        class="plan-type-input">
                                                    <button type="submit"
                                                        class="btn btn--base plan-purchase-btn">@lang('Get Started')</button>
                                                </form>
                                                <button class="btn btn--success plan-current-btn d-none"
                                                    disabled>{{ __('Current Plan') }}</button>
                                            </div>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Extra Info Section --}}
    <div class="col-12 pt-5">
        <div class="pricing_plan-more-info-block">
            <div class="pricing_plan-additional-plans-inner">
                <div class="pricing_plan-additional-title-2">{{ __($content?->question ?? 'Need a custom setup?') }}</div>
                <div class="pricing_plan-basic-text">{{ __($content?->content ?? 'Contact us for plan setup or pricing assistance.') }}</div>
            </div>
            <a href="{{ route('contact') }}" class="btn btn--white btn--sm">@lang('Contact Us')</a>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            if (!$(".pricing-tabs .nav-link").length) {
                return;
            }

            function moveTabHighlight() {
                var $activeBtn = $(".pricing-tabs .nav-link.active");
                var position = $activeBtn.parent().position();
                var width = $activeBtn.parent().outerWidth();

                $(".nav-item-background").css({
                    left: position.left,
                    width: width,
                });
            }

            function updateButtons(type) {
                $(".plan-action-buttons").each(function() {
                    var isActive = $(this).data("active-" + type) === true || $(this).data("active-" + type) ===
                        "true";
                    const $form = $(this).find("form");
                    const $submitBtn = $(this).find(".plan-purchase-btn");
                    const $currentBtn = $(this).find(".plan-current-btn");

                    $form.find(".plan-type-input").val(type);

                    if (isActive) {
                        $submitBtn.addClass("d-none");
                        $currentBtn.removeClass("d-none");
                    } else {
                        $submitBtn.removeClass("d-none");
                        $currentBtn.addClass("d-none");
                    }
                });
            }

            $(document).ready(function() {
                moveTabHighlight();
                updateButtons('monthly');
            });

            $(".pricing-tabs button").on("click", function() {
                setTimeout(() => {
                    moveTabHighlight();

                    const selectedTab = $(this).attr("data-bs-target");
                    const type = selectedTab.includes("yearly") ? "yearly" : "monthly";

                    updateButtons(type);
                }, 10);
            });

            $(window).on("resize", moveTabHighlight);
        })(jQuery);
    </script>
@endpush
