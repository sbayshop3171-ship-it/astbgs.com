@php
    $weeklyBestSelling  = getContent('weekly_best_downloading.content', true);
    $weeklyBestDownloadingProducts = \App\Models\Product::approved()
        ->allActive()
        ->with('author')
        ->whereHas('downloadLogs', function($query) {
            $query->whereDate('created_at', '>=', now()->startOfWeek());
        })
        ->orderBy('total_download','DESC')
        ->limit(10)
        ->get();
@endphp

@if ($weeklyBestDownloadingProducts->count())
    <section class="weekly-best-selling pt-60 pb-120 position-relative">
        <div class="blue-green"></div>
        <div class="blue-violet"></div>
        <div class="container">
            <div class="section-heading style-left flex-between gap-3">
                <div class="section-heading__inner">
                    <h4 class="section-heading__title s-highlight" data-s-break="1" data-s-length="2">{{ __($weeklyBestSelling?->data_values?->title) }}</h4>
                </div>

                @if ($weeklyBestDownloadingProducts->count() > 4)
                    <a href="{{ route('products') }}?sort_by=best_downloading" class="btn btn--sm btn-outline--base  btn--link">@lang('View All Items')  <span class="icon"><i class="las la-arrow-right"></i></span></a>
                @endif
            </div>
            <div class="weekly-best-selling-slider">
                @foreach ($weeklyBestDownloadingProducts as $product)
                    <x-product :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endif
