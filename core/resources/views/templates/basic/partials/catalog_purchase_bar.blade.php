@php
    $activeOptions = $product->relationLoaded('activeOptions') ? $product->activeOptions : $product->activeOptions()->get();
    $hasOptions = $activeOptions->isNotEmpty();
    $hasPurchased = auth()->check() ? auth()->user()->hasPurchasedProduct($product->id) : false;
    $isOrderProduct = $product->isAdminOrderProduct();
    $availabilityClasses = [
        \App\Constants\Status::PRODUCT_AVAILABILITY_AVAILABLE => 'success',
        \App\Constants\Status::PRODUCT_AVAILABILITY_LIMITED => 'warning',
        \App\Constants\Status::PRODUCT_AVAILABILITY_UNAVAILABLE => 'danger',
    ];
    $availabilityClass = $availabilityClasses[$product->availability_status] ?? 'secondary';
@endphp

<div class="common-sidebar__item">
    <div class="common-sidebar__content {{ $isOrderProduct ? 'order-purchase-card' : '' }}">
        @if ($isOrderProduct)
            <div class="order-purchase-card__hero">
                <div>
                    <span class="order-purchase-card__eyebrow">@lang('Service Checkout')</span>
                    <h4 class="catalog-price-heading">
                        @if ($hasOptions)
                            @lang('Select an option to see price')
                        @else
                            {{ $product->catalogPriceLabel }}
                        @endif
                    </h4>
                    <p class="mb-0 order-purchase-card__lead">
                        @lang('Select the service plan that fits this request, then continue with a polished checkout flow.')
                    </p>
                </div>
                <span class="badge badge--{{ $availabilityClass }}">{{ __(ucfirst($product->availability_status)) }}</span>
            </div>

            <div class="order-purchase-steps">
                <div class="order-purchase-step">
                    <span>1</span>
                    <p>@lang('Choose a service option that matches the order')</p>
                </div>
                <div class="order-purchase-step">
                    <span>2</span>
                    <p>@lang('Add quantity and a short note if needed')</p>
                </div>
                <div class="order-purchase-step">
                    <span>3</span>
                    <p>@lang('Continue to secure checkout and confirm the request')</p>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h4 class="mb-1 catalog-price-heading">
                        {{ $product->catalogPriceLabel }}
                    </h4>
                    <p class="mb-0 text-muted small">
                        @lang('Secure cart and checkout flow')
                    </p>
                </div>
                <span class="badge badge--{{ $availabilityClass }}">{{ __(ucfirst($product->availability_status)) }}</span>
            </div>
        @endif

        @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_DOWNLOADABLE)
            <div class="alert alert--info mb-3 py-2 px-3">
                <small>@lang('Downloads unlock after successful payment and order completion.')</small>
            </div>
        @endif

        @if ($hasPurchased)
            <div class="alert alert--success mb-3 py-2 px-3">
                <small>@lang('You already purchased this item. Your latest files are available from My Orders.')</small>
            </div>
        @endif

        <form action="{{ route('cart.add', $product->slug) }}" method="POST" id="catalog-purchase-form-{{ $product->id }}"
            class="{{ $isOrderProduct ? 'order-product-form' : '' }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $isOrderProduct ? 'checkout' : 'cart' }}">

            @if ($hasOptions)
                <div class="form-group">
                    <label class="form-label">@lang($isOrderProduct ? 'Service Option' : 'Choose an option')</label>
                    <select name="product_option_id" class="form-control catalog-option-select" required>
                        <option value="">@lang('Select One')</option>
                        @foreach ($activeOptions as $option)
                            <option value="{{ $option->id }}" data-price="{{ showAmount($option->price) }}"
                                data-note="{{ e($option->availability_note ?? '') }}"
                                data-min="{{ $option->min_amount }}"
                                data-max="{{ $option->max_amount }}">
                                {{ __($option->name) }} - {{ showAmount($option->price) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="border rounded p-3 mb-3 d-none option-meta-box {{ $isOrderProduct ? 'order-option-summary' : '' }}">
                    <div class="small mb-2"><strong>@lang('Price:')</strong> <span class="selected-price">{{ $product->catalogPriceLabel }}</span></div>
                    <div class="small mb-2"><strong>@lang('Pricing Type:')</strong> <span class="selected-pricing-type">@lang('Fixed Price')</span></div>
                    <div class="small mb-2 d-none option-range-text"><strong>@lang('Allowed Range:')</strong> <span class="selected-range"></span></div>
                    <div class="small text-muted d-none option-note-text"><strong>@lang('Availability Note:')</strong> <span class="selected-note"></span></div>
                </div>
            @endif

            @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_OPTION_REQUEST)
                <div class="form-group requested-amount-group d-none">
                    <label class="form-label">@lang('Requested Amount')</label>
                    <input type="number" step="any" min="0" name="requested_amount" class="form-control requested-amount-input"
                        value="{{ old('requested_amount') }}" placeholder="@lang('Enter amount within the allowed range')">
                    <div class="small text-muted mt-1">@lang('Use this only when the selected option allows a minimum-to-maximum range.')</div>
                </div>

                <div class="form-group">
                    <label class="form-label">@lang('About Your Order')</label>
                    <textarea name="request_note" class="form-control" rows="4" placeholder="@lang('Add any simple note or requirement for this order')">{{ old('request_note') }}</textarea>
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">@lang('Quantity')</label>
                <input type="number" min="1" max="99" name="quantity" class="form-control" value="{{ old('quantity', 1) }}">
            </div>

            <button type="submit" class="btn btn--base w-100 catalog-submit-button" @disabled($isOrderProduct && $hasOptions)>
                @lang($isOrderProduct ? 'Continue to Checkout' : ($hasOptions ? 'Add to Cart' : $product->catalogActionLabel))
            </button>
        </form>

        <div class="common-sidebar__button mt-3">
            <a href="{{ route('cart.index') }}" class="btn btn-outline--base w-100">@lang('View Cart')</a>
        </div>

        @auth
            <div class="common-sidebar__button mt-2">
                <a href="{{ route('user.orders.index') }}" class="btn btn-outline--light w-100">@lang('My Orders')</a>
            </div>
        @else
            <p class="small text-muted mt-3 mb-0">@lang('You can add items to cart now and sign in at checkout.')</p>
        @endauth
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            const form = $('#catalog-purchase-form-{{ $product->id }}');
            if (!form.length) {
                return;
            }

            const optionSelect = form.find('.catalog-option-select');
            const optionMetaBox = form.find('.option-meta-box');
            const priceHeading = form.find('.catalog-price-heading');
            const selectedPrice = form.find('.selected-price');
            const selectedPricingType = form.find('.selected-pricing-type');
            const optionRangeText = form.find('.option-range-text');
            const selectedRange = form.find('.selected-range');
            const optionNoteText = form.find('.option-note-text');
            const selectedNote = form.find('.selected-note');
            const requestedAmountGroup = form.find('.requested-amount-group');
            const requestedAmountInput = form.find('.requested-amount-input');
            const submitButton = form.find('.catalog-submit-button');

            function toggleOptionDetails() {
                if (!optionSelect.length) {
                    return;
                }

                const option = optionSelect.find('option:selected');
                const hasValue = !!option.val();
                const price = option.data('price') || @json($product->catalogPriceLabel);
                const min = option.data('min');
                const max = option.data('max');
                const note = option.data('note');
                const hasRange = (min !== undefined && min !== '') || (max !== undefined && max !== '');
                const minText = min !== undefined && min !== '' ? min : '0';
                const maxText = max !== undefined && max !== '' ? max : 'Any';

                optionMetaBox.toggleClass('d-none', !hasValue);
                selectedPrice.text(price);
                selectedPricingType.text(hasRange ? @json(__('Min-Max Range')) : @json(__('Fixed Price')));
                @if ($isOrderProduct && $hasOptions)
                if (priceHeading.length) {
                    priceHeading.text(hasValue ? price : @json(__('Select an option to see price')));
                }
                @endif
                @if ($isOrderProduct && $hasOptions)
                    submitButton.prop('disabled', !hasValue);
                @else
                    submitButton.prop('disabled', false);
                @endif

                optionRangeText.toggleClass('d-none', !hasValue || !hasRange);
                if (hasRange) {
                    selectedRange.text(`${minText} - ${maxText}`);
                }

                optionNoteText.toggleClass('d-none', !hasValue || !note);
                if (note) {
                    selectedNote.text(note);
                }

                @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_OPTION_REQUEST)
                    requestedAmountGroup.toggleClass('d-none', !hasValue || !hasRange);
                    if (hasRange) {
                        requestedAmountInput.attr('placeholder', `${minText} - ${maxText}`);
                        if (min !== undefined && min !== '') {
                            requestedAmountInput.attr('min', min);
                        } else {
                            requestedAmountInput.removeAttr('min');
                        }

                        if (max !== undefined && max !== '') {
                            requestedAmountInput.attr('max', max);
                        } else {
                            requestedAmountInput.removeAttr('max');
                        }
                    } else {
                        requestedAmountInput.val('').removeAttr('min').removeAttr('max').attr('placeholder', @json(__('Fixed amount')));
                    }
                @endif
            }

            optionSelect.on('change', toggleOptionDetails);
            toggleOptionDetails();
        })(jQuery);
    </script>
@endpush
