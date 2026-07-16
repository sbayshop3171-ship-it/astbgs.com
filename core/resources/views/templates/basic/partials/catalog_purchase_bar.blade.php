@php
    $activeOptions = $product->relationLoaded('activeOptions') ? $product->activeOptions : $product->activeOptions()->get();
    $hasOptions = $activeOptions->isNotEmpty();
    $hasPurchased = auth()->check() ? auth()->user()->hasPurchasedProduct($product->id) : false;
    $isOrderProduct = $product->isAdminOrderProduct();
    $existingCartItem = !$hasOptions ? \App\Lib\CatalogCart::findForProduct($product->id) : null;
    $initialQuantity = old('quantity', $existingCartItem['quantity'] ?? 1);
    $defaultCartLabel = $existingCartItem ? 'Update Cart' : 'Add to Cart';
    $availabilityClasses = [
        \App\Constants\Status::PRODUCT_AVAILABILITY_AVAILABLE => 'success',
        \App\Constants\Status::PRODUCT_AVAILABILITY_LIMITED => 'warning',
        \App\Constants\Status::PRODUCT_AVAILABILITY_UNAVAILABLE => 'danger',
    ];
    $availabilityClass = $availabilityClasses[$product->availability_status] ?? 'secondary';
@endphp

<div class="common-sidebar__item">
    <div class="common-sidebar__content {{ $isOrderProduct ? 'order-purchase-card' : '' }}">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h4 class="mb-1 catalog-price-heading">
                    @if ($isOrderProduct && $hasOptions)
                        @lang('Select an option to see price')
                    @else
                        {{ $product->catalogPriceLabel }}
                    @endif
                </h4>
                <p class="mb-0 text-muted small">
                    @lang($isOrderProduct ? 'Simple service checkout for this product' : 'Secure cart and checkout flow')
                </p>
            </div>
            <span class="badge badge--{{ $availabilityClass }}">{{ __(ucfirst($product->availability_status)) }}</span>
        </div>

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

        <form action="{{ route('cart.add', $product->slug) }}" method="POST"
            id="catalog-purchase-form-{{ $product->id }}"
            class="premium-cart-form premium-cart-form--sidebar"
            data-ajax-cart-form
            data-cart-default-label="{{ __($defaultCartLabel) }}"
            data-cart-updated-label="{{ __('Update Cart') }}"
            data-cart-added-label="{{ __('Added!') }}"
            data-cart-key="{{ $existingCartItem['cart_key'] ?? '' }}"
            data-cart-item-url="{{ isset($existingCartItem['cart_key']) ? route('cart.item.sync', $existingCartItem['cart_key']) : '' }}"
            data-cart-option-required="{{ $hasOptions ? 'true' : 'false' }}"
            data-cart-price-placeholder="{{ __($isOrderProduct && $hasOptions ? 'Select an option' : $product->catalogPriceLabel) }}"
            data-sticky-price-target="#catalog-sticky-price-{{ $product->id }}"
            data-cart-label-pending="{{ __('Adding...') }}"
            data-cart-label-updating="{{ __('Updating...') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="cart">

            @if ($hasOptions)
                <div class="form-group">
                    <label class="form-label">@lang($isOrderProduct ? 'Service Option' : 'Choose an option')</label>
                    <select name="product_option_id" class="form-control catalog-option-select premium-cart-select" data-cart-option-select>
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

                <div class="border rounded p-3 mb-3 d-none option-meta-box">
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

            <div class="form-group premium-cart-quantity-group">
                <label class="form-label">@lang('Quantity')</label>
                <div class="premium-qty-selector premium-qty-selector--sidebar" data-cart-qty-wrapper>
                    <button type="button" class="premium-qty-selector__btn" data-quantity-control="decrease" aria-label="@lang('Decrease quantity')">
                        <i class="las la-minus"></i>
                    </button>
                    <input type="number" min="1" max="99" name="quantity" class="premium-qty-selector__input"
                        value="{{ $initialQuantity }}"
                        data-cart-quantity-input
                        data-cart-quantity-group="catalog-product-{{ $product->id }}">
                    <button type="button" class="premium-qty-selector__btn" data-quantity-control="increase" aria-label="@lang('Increase quantity')">
                        <i class="las la-plus"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="premium-cart-submit premium-cart-submit--sidebar catalog-submit-button" data-cart-submit>
                <span class="premium-cart-submit__state premium-cart-submit__state--default">
                    <i class="las la-shopping-bag"></i>
                    <span data-cart-submit-text>{{ __($defaultCartLabel) }}</span>
                </span>
                <span class="premium-cart-submit__state premium-cart-submit__state--loading">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span data-cart-submit-loading>{{ __('Adding...') }}</span>
                </span>
                <span class="premium-cart-submit__state premium-cart-submit__state--success">
                    <i class="las la-check-circle"></i>
                    <span>@lang('Added!')</span>
                </span>
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

<div class="catalog-sticky-cart d-lg-none" data-sticky-cart data-target-form="catalog-purchase-form-{{ $product->id }}">
    <div class="catalog-sticky-cart__meta">
        <span class="catalog-sticky-cart__label">@lang('Quick Cart')</span>
        <strong class="catalog-sticky-cart__price" id="catalog-sticky-price-{{ $product->id }}">
            @if ($isOrderProduct && $hasOptions)
                @lang('Select an option')
            @else
                {{ $product->catalogPriceLabel }}
            @endif
        </strong>
    </div>

    <div class="catalog-sticky-cart__actions">
        <div class="premium-qty-selector premium-qty-selector--sticky" data-cart-qty-wrapper>
            <button type="button" class="premium-qty-selector__btn" data-quantity-control="decrease" aria-label="@lang('Decrease quantity')">
                <i class="las la-minus"></i>
            </button>
            <input type="number" min="1" max="99" class="premium-qty-selector__input"
                value="{{ $initialQuantity }}"
                data-cart-quantity-input
                data-cart-quantity-group="catalog-product-{{ $product->id }}">
            <button type="button" class="premium-qty-selector__btn" data-quantity-control="increase" aria-label="@lang('Increase quantity')">
                <i class="las la-plus"></i>
            </button>
        </div>

        <button type="submit"
            form="catalog-purchase-form-{{ $product->id }}"
            class="premium-cart-submit premium-cart-submit--sticky"
            data-cart-submit>
            <span class="premium-cart-submit__state premium-cart-submit__state--default">
                <i class="las la-shopping-bag"></i>
                <span data-cart-submit-text>{{ __($defaultCartLabel) }}</span>
            </span>
            <span class="premium-cart-submit__state premium-cart-submit__state--loading">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span data-cart-submit-loading>{{ __('Adding...') }}</span>
            </span>
            <span class="premium-cart-submit__state premium-cart-submit__state--success">
                <i class="las la-check-circle"></i>
                <span>@lang('Added!')</span>
            </span>
        </button>
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
            const stickyPrice = $('#catalog-sticky-price-{{ $product->id }}');

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
                if (stickyPrice.length) {
                    stickyPrice.text(hasValue ? price : @json($isOrderProduct && $hasOptions ? __('Select an option') : $product->catalogPriceLabel));
                }
                @if ($isOrderProduct && $hasOptions)
                if (priceHeading.length) {
                    priceHeading.text(hasValue ? price : @json(__('Select an option to see price')));
                }
                @endif

                form.attr('data-cart-option-required', hasValue ? 'false' : 'true');
                submitButton.toggleClass('is-attention', !hasValue);

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
