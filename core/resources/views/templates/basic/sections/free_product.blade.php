@php
    $freeProduct = getContent('free_product.content', true);
    $freeProducts = \App\Models\Product::approved()
        ->allActive()
        ->with('author')
        ->withCount(['downloadLogs as total_download'])
        ->groupBy('products.id')
        ->orderBy('total_download', 'DESC')
        ->where('is_free', Status::ENABLE)
        ->limit(10)
        ->get();
@endphp
@if (!$freeProducts->isEmpty())
    <section class="browse-best-selling py-120 overflow-hidden">
        <div class="container">
            <div class="section-heading style-left flex-between gap-3">
                <div class="section-heading__inner">
                    <h4 class="section-heading__title s-highlight" data-s-break="-2" data-s-length="2">{{ __($freeProduct?->data_values?->title) }}</h4>
                </div>
                <a href="{{ route('free.products') }}" class="btn btn-outline--base btn--sm  btn--link">@lang('View All Items')
                    <span class="icon"><i class="las la-arrow-right"></i></span></a>
            </div>
            <div class="browse-best-selling-slider">
                @foreach ($freeProducts as $product)
                    <x-product :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endif
