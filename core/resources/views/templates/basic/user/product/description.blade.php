@php
    $isOrderProduct = $product->isAdminOrderProduct();
    $previewImageUrl = getImage(getFilePath('productPreview') . '/' . productFilePath($product, 'preview_image'), getFileSize('productPreview'));
    $galleryImages = collect([$previewImageUrl]);

    foreach ($product->screenshots() as $screenshotPath) {
        $galleryImages->push(getImage($screenshotPath));
    }

    $galleryImages = $galleryImages->filter()->unique()->values();
    $mainGalleryImage = $galleryImages->first() ?: $previewImageUrl;
@endphp

<div id="screenshotsGallery" class="hidden">
    @foreach ($galleryImages as $galleryImage)
        <a href="{{ $galleryImage }}">@lang('Image')</a>
    @endforeach
</div>

@if ($isOrderProduct)
    <div class="order-product-landing">
        <div class="order-product-gallery-card">
            <div class="order-product-card-head">
                <div class="order-product-section-head">
                    <span class="order-product-section-kicker">@lang('Preview')</span>
                    <h5>@lang('Service Snapshot')</h5>
                </div>
                <span class="order-product-card-pill">{{ $galleryImages->count() }} @lang('visuals')</span>
            </div>

            <div class="order-product-main-image">
                <img src="{{ $mainGalleryImage }}" alt="@lang('Product Image')" id="orderProductMainImage">
            </div>

            @if ($galleryImages->count() > 1)
                <div class="order-product-thumbs">
                    @foreach ($galleryImages as $galleryIndex => $galleryImage)
                        <button type="button"
                            class="order-product-thumb {{ $galleryIndex === 0 ? 'is-active' : '' }}"
                            data-image="{{ $galleryImage }}">
                            <img src="{{ $galleryImage }}" alt="@lang('Gallery Image')">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="order-product-copy-card">
            <div class="order-product-card-head">
                <div class="order-product-section-head">
                    <span class="order-product-section-kicker">@lang('About')</span>
                    <h5>@lang('About This Order Product')</h5>
                </div>
                <span class="order-product-card-pill">@lang('Secure checkout ready')</span>
            </div>
            <div class="product-details-item mb-0">
                @php echo html_entity_decode($product->description); @endphp
            </div>
        </div>

        <div class="order-product-copy-card">
            @php
                $relatedProducts = \App\Models\Product::catalogPublished()
                    ->where('id', '!=', $product->id)
                    ->where('category_id', $product->category_id)
                    ->latest('id')
                    ->limit(6)
                    ->get();
            @endphp

            <div class="order-product-card-head">
                <div class="order-product-section-head">
                    <span class="order-product-section-kicker">@lang('More')</span>
                    <h6>@lang('More items from our catalog')</h6>
                </div>
                <span class="order-product-card-pill">{{ $relatedProducts->count() }} @lang('items')</span>
            </div>

            <div class="order-product-related-grid">
                @forelse ($relatedProducts as $otherProduct)
                    <a href="{{ route('product.details', $otherProduct->slug) }}" class="order-product-related-card"
                        title="{{ __($otherProduct->title) }}">
                        <span class="order-product-related-card__media">
                            <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($otherProduct, 'thumbnail')) }}"
                                alt="{{ __($otherProduct->title) }}" />
                        </span>
                        <span class="order-product-related-card__body">
                            <span class="order-product-related-card__meta">
                                {{ __($otherProduct->subcategory?->name ?? $otherProduct->category?->name ?? __('Catalog')) }}
                            </span>
                            <strong>{{ \Illuminate\Support\Str::limit(__($otherProduct->title), 56) }}</strong>
                            <span class="order-product-related-card__price">{{ $otherProduct->catalogPriceLabel }}</span>
                        </span>
                    </a>
                @empty
                    <div class="order-product-empty">@lang('More products from this category will appear here soon.')</div>
                @endforelse
            </div>
        </div>
    </div>
@else
    <div class="product-details__inner">
        <div class="product-details__thumb">
            <img src="{{ $previewImageUrl }}" alt="@lang('Product Image')" />
            <div class="product-details__buttons">
                @if ($product->demo_url)
                    <a href="{{ $product->demo_url }}" target="_blank" class="btn btn--base">@lang('Live Preview')</a>
                @endif
                @if ($galleryImages->count() > 1)
                    <button type="button" id="showScreenshots" class="btn btn-outline--light">@lang('Screenshots')</button>
                @endif
            </div>
            @if ($product->isTrending())
                @php
                    $trendingIcon = public_path('assets/images/trending.svg');
                @endphp
                <span class="icon">
                    {!! is_file($trendingIcon) ? file_get_contents($trendingIcon) : '' !!}
                </span>
            @endif
        </div>
        <div class="product-details__content">
            <div class="product-details-item">
                @php echo html_entity_decode($product->description); @endphp
            </div>
            <div class="product-details-item mb-3">
                <div class="product-details-item__title flex-between">
                    @if ($product->managed_by_admin)
                        <h6 class="mb-0">@lang('More items from our catalog')</h6>
                        <a href="{{ route('products') }}" class="text--base text-decoration-underline">
                            @lang('View all products')
                        </a>
                    @else
                        <h6 class="mb-0">@lang('More items by') {{ $product?->author?->fullname }}</h6>
                        <a href="{{ route('user.profile', $product->author->username) }}"
                            class="text--base text-decoration-underline">
                            @lang('View author profile')
                        </a>
                    @endif
                </div>
                <div class="more-product-thumbs">
                    @php
                        $relatedProducts = $product->managed_by_admin
                            ? \App\Models\Product::catalogPublished()->where('id', '!=', $product->id)->where('category_id', $product->category_id)->latest('id')->limit(8)->get()
                            : $product->author->products()->approved()->where('id', '!=', $product->id)->orderBy('id', 'desc')->limit(8)->get();
                    @endphp
                    @foreach ($relatedProducts as $otherProduct)
                        <div class="more-product-thumbs__item">
                            <a href="{{ route('product.details', $otherProduct->slug) }}"
                                title="{{ __($otherProduct->title) }}">
                                <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($otherProduct, 'thumbnail')) }}"
                                    alt="" />
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
