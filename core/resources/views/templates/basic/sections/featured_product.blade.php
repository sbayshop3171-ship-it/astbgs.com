@php
    $featureProductSection = getContent('featured_product.content', true);
    $featuredProducts = \App\Models\Product::catalogPublished()->featured()->with(['author', 'activeOptions'])->limit(4)->get();
@endphp

<section class="featured-theme featured-products py-60">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-xxl-6 col-lg-5 pe-xl-5">
                <h4 class="feature-box__title s-highlight" data-s-break="-1" data-s-length="1">{{ __($featureProductSection?->data_values?->title) }}</h4>
                <p class="feature-box__desc mb-3">{{ __($featureProductSection?->data_values?->subtitle) }}</p>
                <a href="{{ route('products') }}" class="btn btn--sm btn--base btn--link">@lang('View All Items') <span class="icon"><i class="las la-arrow-right"></i></span></a>
                <div class="feature-box mt-4 d-none d-lg-block">
                    <img src="{{ frontendImage('featured_product', $featureProductSection?->data_values?->image, '860x1200') }}" alt="">
                </div>
            </div>

            <div class="col-xxl-6 col-lg-7">
                <div class="row gy-4 home-product-grid">
                    @foreach ($featuredProducts ?? [] as $product)
                        <div class="col-sm-6 col-xsm-6">
                            <x-product :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
