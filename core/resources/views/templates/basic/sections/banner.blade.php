@php
    $banner = getContent('banner.content', true)?->data_values;
@endphp
<section class="banner bg-img bg_fixed"
    data-background-image="{{ frontendImage('banner', $banner?->background_image, '2400x960') }}">
    <div class="container">
        <div class="banner-wrapper d-flex justify-content-center">
            <div class="banner-content text-center">
                <h1 class="banner-content__title  s-highlight" data-s-break="-3" data-s-length="1">
                    {{ __($banner?->title) }}</h1>
                <p class="banner-content__desc">{{ __($banner?->subtitle) }}</p>
                <form action="{{ route('products') }}" class="search-box">
                    <input type="text" class="form--control animated-placeholder" name="search"
                        placeholder="{{ __($banner->search_placeholder) }}">
                    <button type="submit" class="btn btn--base search-box__btn">
                        <span class="icon"><i class="icon-Search"></i></span>
                        @lang('Search')
                    </button>
                </form>
                <div class="btn-wrapper mt-4 d-flex gap-3 flex-wrap justify-content-center">
                    <a href="{{ route('user.register') }}"
                        class="btn btn--sm btn--gray">{{ __($banner?->button_one) }}</a>
                    <a href="{{ route('products') }}" class="btn btn--sm btn--gray">{{ __($banner?->button_two) }}</a>
                    <a href="{{ route('user.login') }}"
                        class="btn btn--sm btn--gray">{{ __($banner?->button_three) }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
