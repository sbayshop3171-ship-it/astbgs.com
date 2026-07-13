@php
    $cta = getContent('cta.content', true);
    $ctaElement = getContent('cta.element', false, 6);
@endphp
<section class="cta">
    <div class="cta__inner">
        <img src="{{ asset($activeTemplateTrue . 'images/cta-line-shape.png') }}" alt="@lang('Image')"
            class="cta__line-shape">
        @foreach ($ctaElement as $key => $item)
            <img class="technology-{{ $key + 1 }}"
                src="{{ frontendImage('cta', $item?->data_values?->image, '90x90') }}" alt="img">
        @endforeach

        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-md-8">
                    <div class="cta-content pt-120 text-center">
                        <div class="section-heading text-center">
                            <h3 class="section-heading__title s-highlight" data-s-break="1" data-s-length="1">{{ __($cta?->data_values?->title) }}</h3>
                            <p class="section-heading__desc">{{ __($cta?->data_values?->subtitle) }}</p>
                            <a href="{{ route('user.register') }}"
                                class="btn btn--base mt-4">{{ __($cta?->data_values?->button_name) }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
