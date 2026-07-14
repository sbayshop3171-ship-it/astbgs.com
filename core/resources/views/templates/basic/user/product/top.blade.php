<div class="product-details-top">
    @php $isOrderProduct = $product->isAdminOrderProduct(); @endphp
    @if ($product->my_product)
        <div class="mb-3">
            @if ($product->status == Status::PRODUCT_DOWN)
                <x-alert type="danger"
                    route="{{ route('user.author.hidden.items', ['status' => Status::PRODUCT_DOWN]) }}">
                    <strong>{{ __($product->title) }}</strong> @lang('is down.')
                </x-alert>
            @endif

            @if ($product->status == Status::PRODUCT_PENDING)
                <x-alert type="warning"
                    route="{{ route('user.author.hidden.items', ['status' => Status::PRODUCT_PENDING]) }}">
                    <strong>{{ __($product->title) }}</strong> @lang('is under review.')
                </x-alert>
            @endif

            @if ($product->status == Status::PRODUCT_SOFT_REJECTED)
                <x-alert type="danger"
                    route="{{ route('user.author.hidden.items', ['status' => Status::PRODUCT_SOFT_REJECTED]) }}">
                    <strong>{{ __($product->title) }}</strong> @lang('is soft rejected.')
                </x-alert>
            @endif
        </div>
    @endif

    @if ($isOrderProduct)
        @php
            $activeOptions = $product->relationLoaded('activeOptions') ? $product->activeOptions : $product->activeOptions()->get();
            $summaryText = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($product->description))));
            $summaryText = \Illuminate\Support\Str::limit($summaryText, 185);
        @endphp

        <div class="order-product-hero">
            <div class="order-product-hero__content">
                <span class="order-product-hero__eyebrow">@lang('Premium Support Service')</span>
                <h1 class="product-details__title order-product-hero__title">{{ __($product->title) }}</h1>
                @if ($summaryText)
                    <p class="order-product-hero__summary">{{ $summaryText }}</p>
                @endif

                <div class="order-product-hero__chips">
                    <span class="order-product-chip">
                        <strong>@lang('Type')</strong>
                        <span>{{ __($product->productTypeLabel) }}</span>
                    </span>
                    <span class="order-product-chip">
                        <strong>@lang('Category')</strong>
                        <span>{{ __($product->category?->name) }}</span>
                    </span>
                    <span class="order-product-chip">
                        <strong>@lang('Subcategory')</strong>
                        <span>{{ __($product->subcategory?->name) }}</span>
                    </span>
                    <span class="order-product-chip">
                        <strong>@lang('Availability')</strong>
                        <span>{{ __(ucfirst($product->availability_status ?? 'available')) }}</span>
                    </span>
                </div>

                <div class="order-product-hero__stats">
                    <div class="order-product-stat">
                        <span class="order-product-stat__label">@lang('Service Options')</span>
                        <strong>{{ $activeOptions->count() ?: 1 }}</strong>
                    </div>
                    <div class="order-product-stat">
                        <span class="order-product-stat__label">@lang('Price Range')</span>
                        <strong>{{ $product->catalogPriceLabel }}</strong>
                    </div>
                    <div class="order-product-stat">
                        <span class="order-product-stat__label">@lang('Last Updated')</span>
                        <strong>{{ showDateTime($product->last_updated, 'd M Y') }}</strong>
                    </div>
                </div>
            </div>

            <div class="order-product-hero__aside">
                <span class="order-product-hero__badge">{{ __(ucfirst($product->availability_status ?? 'available')) }}</span>
                <div>
                    <h6>@lang('Built for smooth, secure service orders')</h6>
                    <p>@lang('Pick the right service option, add a quick note, and move to checkout with a cleaner premium flow.')</p>
                </div>
                <div class="order-product-hero__actions">
                    @include('Template::user.product.social_share')
                </div>
            </div>
        </div>
    @else
        <h5 class="product-details__title">{{ __($product->title) }}</h5>
        <div class="product-details-top__inner flex-between gap-3 align-items-center">
            <ul class="custom-tab">
                <li class="custom-tab__item {{ menuActive('product.details') }}">
                    <a href="{{ route('product.details', $product->slug) }}" class="custom-tab__link">@lang('Description')</a>
                </li>

                @if ($product->total_review)
                    <li class="custom-tab__item {{ menuActive('product.reviews') }}">
                        <a href="{{ route('product.reviews', $product->slug) }}" class="custom-tab__link">
                            @lang('Reviews')
                            @if ($product?->total_review >= gs('min_reviews'))
                                @php echo displayRating($product->avg_rating);  @endphp {{ $product->avg_rating }}
                            @endif <span class="notification">{{ $product->total_review }}</span>
                        </a>
                    </li>
                @endif

                <li class="custom-tab__item {{ menuActive('product.comments') }}">
                    <a href="{{ route('product.comments', $product->slug) }}" class="custom-tab__link">
                        @lang('Comments')
                        <span class="notification">{{ $product?->comments_count }}</span>
                    </a>
                </li>
                @if (gs('changelog'))
                    @if ($product->status == Status::PRODUCT_APPROVED || $product->my_product)
                        @if (count($product->changelogs) > 0 && $product->product_updated == Status::PRODUCT_NO_UPDATE)
                            <li class="custom-tab__item {{ menuActive('product.changelog') }}">
                                <a href="{{ route('product.changelog', $product->slug) }}" class="custom-tab__link">
                                    @lang('Changelog')
                                </a>
                            </li>
                        @endif
                    @endif
                @endif

                @if (auth()->id() == $product->user_id)
                    <li class="custom-tab__item {{ menuActive('user.product.activities') }}">
                        <a href="{{ route('user.product.activities', $product->slug) }}" class="custom-tab__link">
                            @lang('Activity Log')
                        </a>
                    </li>
                @endif
            </ul>
            @if ($product->status == Status::PRODUCT_APPROVED)
                <div class="product-details-top__right flex-align">
                    @if ($product?->total_review >= gs('min_reviews'))
                        <div class="rating-list" data-bs-toggle="tooltip" title="@lang('Total Rating')">
                            @php echo displayRating($product?->avg_rating);  @endphp
                            <span class="rating-list__text"> ({{ $product?->total_review }})</span>
                        </div>
                    @endif
                    @if (auth()->check())
                        <span class="sales d-block d-lg-none">@lang(str()->plural('Download', $product->total_download)) {{ $product->total_download }}</span>
                    @endif
                    @include('Template::user.product.social_share')
                </div>
            @endif
        </div>
    @endif
</div>
