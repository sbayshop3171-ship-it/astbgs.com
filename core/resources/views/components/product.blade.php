@php $isOrderProduct = $product->isAdminOrderProduct(); @endphp
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
        <div class="flex-between align-items-end">
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
            <div class="d-flex flex-column align-items-end gap-2">
                @if ($product->managed_by_admin)
                    @if ($product->hasActiveOptions())
                        <a href="{{ route('product.details', $product->slug) }}" class="btn btn--base btn--sm mt-1">
                            @lang('Select Options')
                        </a>
                    @else
                        <form action="{{ route('cart.add', $product->slug) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect_to" value="{{ $isOrderProduct ? 'checkout' : 'cart' }}">
                            <button type="submit" class="btn btn--base btn--sm mt-1">@lang($isOrderProduct ? 'Checkout' : 'Buy Now')</button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('product.details', $product->slug) }}" class="btn btn-outline--base btn--sm mt-1">
                        @lang('View Details')
                    </a>
                @endif

                @if (!$isOrderProduct)
                    @php $hasDemo = !empty($product->demo_url); @endphp
                    <a href="{{ $hasDemo ? $product->demo_url : 'javascript:void(0)' }}" class="btn btn-outline--light btn--sm {{ $hasDemo ? '' : 'disabled' }}" target="{{ $hasDemo ? '_blank' : '_self' }}"><i class="las la-external-link-alt"></i> @lang('Live Preview')
                    </a>
                @elseif ($product->managed_by_admin)
                    <a href="{{ route('product.details', $product->slug) }}" class="btn btn-outline--light btn--sm">@lang('View Details')</a>
                @endif
            </div>
        </div>
    </div>
</div>
