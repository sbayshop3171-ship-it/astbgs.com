@php
    $activeOptions = $product->relationLoaded('activeOptions') ? $product->activeOptions : $product->activeOptions()->get();
    $hasOptions = $activeOptions->isNotEmpty();
    $hasPurchased = auth()->check() ? auth()->user()->hasPurchasedProduct($product->id) : false;
    $isOrderProduct = $product->isAdminOrderProduct();
    $initialQuantity = old('quantity', 1);
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
                    @lang($isOrderProduct ? 'Choose your option and quantity, then add to cart or buy now.' : 'Secure cart and checkout flow')
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
            data-cart-option-required="{{ $hasOptions ? 'true' : 'false' }}"
            data-cart-price-placeholder="{{ $product->catalogPriceLabel }}"
            data-cart-price-empty-label="{{ __($isOrderProduct && $hasOptions ? 'Select an option to see price' : $product->catalogPriceLabel) }}">
            @csrf
            <input type="hidden" name="redirect_to" value="cart">

            @if ($hasOptions)
                <div class="form-group">
                    <label class="form-label">@lang($isOrderProduct ? 'Service Option' : 'Choose an option')</label>
                    <select name="product_option_id" class="form-control catalog-option-select premium-cart-select" data-cart-option-select>
                        <option value="">@lang('Select One')</option>
                        @foreach ($activeOptions as $option)
                            <option value="{{ $option->id }}"
                                data-price="{{ showAmount($option->price) }}"
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
                        data-cart-quantity-input>
                    <button type="button" class="premium-qty-selector__btn" data-quantity-control="increase" aria-label="@lang('Increase quantity')">
                        <i class="las la-plus"></i>
                    </button>
                </div>
            </div>

            <div class="catalog-cart-actions">
                <button type="submit"
                    class="catalog-action-btn catalog-action-btn--primary"
                    data-cart-submit
                    data-cart-intent="cart"
                    data-idle-label="{{ __('Add to Cart') }}"
                    data-loading-label="{{ __('Adding...') }}"
                    data-success-label="{{ __('Added!') }}">
                    <span class="catalog-action-btn__icon" data-cart-submit-icon>
                        <i class="las la-shopping-cart"></i>
                    </span>
                    <span class="catalog-action-btn__spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span data-cart-submit-text>{{ __('Add to Cart') }}</span>
                </button>

                <button type="submit"
                    class="catalog-action-btn catalog-action-btn--secondary"
                    data-cart-submit
                    data-cart-intent="checkout"
                    data-idle-label="{{ __('Buy Now') }}"
                    data-loading-label="{{ __('Processing...') }}"
                    data-success-label="{{ __('Redirecting...') }}">
                    <span class="catalog-action-btn__icon" data-cart-submit-icon>
                        <i class="las la-bolt"></i>
                    </span>
                    <span class="catalog-action-btn__spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span data-cart-submit-text>{{ __('Buy Now') }}</span>
                </button>
            </div>
        </form>

        <div class="common-sidebar__button mt-3">
            <a href="{{ route('cart.index') }}" class="catalog-action-btn catalog-action-btn--light catalog-action-btn--block">@lang('View Cart')</a>
        </div>

        @auth
            <div class="common-sidebar__button mt-2">
                <a href="{{ route('user.orders.index') }}" class="catalog-action-btn catalog-action-btn--light catalog-action-btn--block">@lang('My Orders')</a>
            </div>
        @else
            <p class="small text-muted mt-3 mb-0">@lang('You can add items to cart now and sign in at checkout.')</p>
        @endauth
    </div>
</div>
