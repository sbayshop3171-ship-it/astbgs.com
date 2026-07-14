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
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <h5 class="product-details__title mb-2">{{ __($product->title) }}</h5>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge--dark">@lang('Type'): {{ __($product->productTypeLabel) }}</span>
                    <span class="badge badge--secondary">@lang('Category'): {{ __($product->category?->name) }}</span>
                    <span class="badge badge--secondary">@lang('Subcategory'): {{ __($product->subcategory?->name) }}</span>
                    <span class="badge badge--success">@lang('Available'): {{ __(ucfirst($product->availability_status ?? 'available')) }}</span>
                </div>
            </div>
            <div class="product-details-top__right flex-align">
                @include('Template::user.product.social_share')
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
