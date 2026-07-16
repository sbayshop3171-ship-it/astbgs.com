@php
    $isOrderProduct = $product->isAdminOrderProduct();
    $activeOptions = $product->relationLoaded('activeOptions') ? $product->activeOptions : $product->activeOptions()->get();
    $hasOptions = $activeOptions->isNotEmpty();
    $initialQuantity = old('quantity', 1);
@endphp
<div class="product-card h-100">
    <div class="product-card__thumb">
        <a href="{{ route('product.details', $product->slug) }}" class="link" title="{{ __($product->title) }}">
            <img src="{{ getImage(getFilePath('productInlinePreview') . productFilePath($product, 'inline_preview_image'), getFileSize('productInlinePreview')) }}" alt="@lang('Product Image')">
        </a>
        @if ($product->isTrending())
            @php
                $trendingIcon = public_path('assets/images/trending.svg');
            @endphp
            <span class="icon">
                {!! is_file($trendingIcon) ? file_get_contents($trendingIcon) : '' !!}
            </span>
        @endif
        <div class="collection-list">
            <x-product-save :product="$product" />
        </div>
    </div>
    <div class="product-card__content">
        <div class="product-card__content-inner">
            <div class="product-card__top w-100">
                <div class="product-card-title-wrapper">
                    <h6 class="product-card__title">
                        <a href="{{ route('product.details', $product->slug) }}" class="link border-effect">
                            {{ __($product->title) }}
                        </a>
                    </h6>
                    <div class="product-card__author">
                        @if ($product->managed_by_admin)
                            <span class="link">@lang('By') {{ __(gs('site_name')) }}</span>
                            <span class="product-card__price">{{ $product->catalogPriceLabel }}</span>
                        @else
                            <a href="{{ route('user.profile', $product->author->username) }}" class="link">@lang('By') {{ __($product->author->fullname) }}</a>
                            @if ($product->is_free)
                                <span class="product-card__price">@lang('Free')</span>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
            <div class="collection-list list-style">
                <x-product-save :product="$product" />
            </div>
        </div>
        <div class="flex-between align-items-end gap-3">
            <div class="product-card__rating">
                @if (!$isOrderProduct && ($product->total_review ?? 0) >= gs('min_reviews'))
                    <div class="rating-list">
                        @php echo displayRating($product->avg_rating); @endphp
                    </div>
                @endif
                @if (!$isOrderProduct)
                    <div class="mt-1">
                        <i class="las la-download"></i>
                        <span class="product-card__sales">{{ $product->total_download }} {{ __(str()->plural('Download', $product->total_download)) }}</span>
                    </div>
                @endif
            </div>
            <div class="d-flex flex-column align-items-stretch gap-2 product-card__actions">
                @if ($product->managed_by_admin)
                    <form action="{{ route('cart.add', $product->slug) }}" method="POST"
                        class="premium-cart-form premium-cart-form--card"
                        data-ajax-cart-form
                        data-cart-option-required="{{ $hasOptions ? 'true' : 'false' }}"
                        data-cart-price-placeholder="{{ $product->catalogPriceLabel }}"
                        data-cart-price-empty-label="{{ __($hasOptions ? 'Select an option' : $product->catalogPriceLabel) }}">
                        @csrf
                        <input type="hidden" name="redirect_to" value="cart">

                        @if ($hasOptions)
                            <div class="premium-cart-card__option">
                                <select name="product_option_id" class="form-control form-control-sm catalog-option-select premium-cart-select" data-cart-option-select>
                                    <option value="">@lang('Select Option')</option>
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

                            <div class="premium-cart-option-summary option-meta-box d-none">
                                <span class="premium-cart-option-summary__price selected-price">{{ $product->catalogPriceLabel }}</span>
                                <span class="premium-cart-option-summary__meta option-range-text d-none">
                                    @lang('Range') <span class="selected-range"></span>
                                </span>
                                <span class="premium-cart-option-summary__meta option-note-text d-none">
                                    <span class="selected-note"></span>
                                </span>
                            </div>
                        @endif

                        @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_OPTION_REQUEST)
                            <div class="requested-amount-group d-none">
                                <input type="number" step="any" min="0" name="requested_amount"
                                    class="form-control form-control-sm requested-amount-input"
                                    value="{{ old('requested_amount') }}"
                                    placeholder="@lang('Requested amount')">
                            </div>
                        @endif

                        <div class="premium-cart-row">
                            <div class="premium-qty-selector" data-cart-qty-wrapper>
                                <button type="button" class="premium-qty-selector__btn" data-quantity-control="decrease" aria-label="@lang('Decrease quantity')">
                                    <i class="las la-minus"></i>
                                </button>
                                <input type="number" min="1" max="99" name="quantity"
                                    class="premium-qty-selector__input"
                                    value="{{ $initialQuantity }}"
                                    data-cart-quantity-input>
                                <button type="button" class="premium-qty-selector__btn" data-quantity-control="increase" aria-label="@lang('Increase quantity')">
                                    <i class="las la-plus"></i>
                                </button>
                            </div>
                            <div class="catalog-cart-actions catalog-card-actions catalog-cart-actions--single">
                                <button type="submit"
                                    class="catalog-action-btn catalog-action-btn--primary"
                                    data-cart-submit
                                    data-cart-intent="cart"
                                    data-idle-label="{{ __('Cart') }}"
                                    data-loading-label="{{ __('Adding...') }}"
                                    data-success-label="{{ __('Added!') }}">
                                    <span class="catalog-action-btn__icon" data-cart-submit-icon>
                                        <i class="las la-shopping-cart"></i>
                                    </span>
                                    <span class="catalog-action-btn__spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                                    <span data-cart-submit-text>{{ __('Cart') }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <a href="{{ route('product.details', $product->slug) }}" class="catalog-action-btn catalog-action-btn--secondary catalog-action-btn--block catalog-view-btn">
                        @lang('View Details')
                    </a>
                @endif

                @if (!$isOrderProduct)
                    @php $hasDemo = !empty($product->demo_url); @endphp
                    <a href="{{ $hasDemo ? $product->demo_url : 'javascript:void(0)' }}" class="catalog-action-btn catalog-action-btn--light catalog-action-btn--block {{ $hasDemo ? '' : 'disabled' }}" target="{{ $hasDemo ? '_blank' : '_self' }}"><i class="las la-external-link-alt"></i> @lang('Live Preview')
                    </a>
                @endif

                @if ($product->managed_by_admin)
                    <a href="{{ route('product.details', $product->slug) }}" class="catalog-action-btn catalog-action-btn--light catalog-action-btn--block catalog-view-btn">@lang('View Details')</a>
                @endif
            </div>
        </div>
    </div>
</div>
