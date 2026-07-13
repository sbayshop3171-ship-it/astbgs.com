@php
    $featureAuthorSection = getContent('featured_author.content', true)->data_values;
    $featuredAuthor = \App\Models\User::where('is_author_featured', Status::YES)
        ->active()
        ->with([
            'products' => function ($query) {
                $query->where('status', Status::YES)->orderByDesc('total_download')->limit(4);
            },
        ])
        ->first();

    $advertise728x90 = getAds('728x90');
    $hasAd728x90 = !empty($advertise728x90);
@endphp

@if (!empty($featuredAuthor->products))
    <section class="featured-theme py-60">
        <div class="container @if ($hasAd728x90) mb-5 @endif">
            <div class="row gy-4">
                <div class="col-xxl-6 col-lg-5 pe-xl-5">
                    <div class="feature-box">
                        <div class="feature-box__content">
                            <div class="inner-content">
                                <h4 class="feature-box__title mb-2 s-highlight" data-s-break="-1" data-s-length="1">
                                    @lang('Featured Author')</h4>
                                <h6 class="feature-box__desc">{{ __($featuredAuthor?->fullname) }}</h6>
                                <a href="{{ route('user.portfolio', $featuredAuthor?->username) }}"
                                    class="btn btn--base btn--sm">@lang('View Portfolio')</a>
                            </div>
                            <img src="{{ frontendImage('featured_author', $featureAuthorSection?->image, '1270x940') }}"
                                alt="" class="feature-box__water-img one">
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-lg-7">
                    <div class="row gy-4">
                        @foreach ($featuredAuthor->products ?? [] as $product)
                            <div class="col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if ($hasAd728x90)
            @php echo $advertise728x90  @endphp
        @endif

    </section>
@endif
