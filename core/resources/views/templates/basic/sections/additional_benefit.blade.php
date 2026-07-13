@php
    $benefit = getContent('additional_benefit.content', true)?->data_values;
    $benefits = getContent('additional_benefit.element');
    $advertise1120x170 = getAds('1120x170');
    $hasAd1120x170 = !empty($advertise1120x170);
@endphp

<section class="additional-benefit py-120">
    <div class="container">
        <div class="row gy-4 flex-wrap-revese align-items-center">
            <div class="col-lg-6">
                <div class="additional-benefit__thumb">
                    <img src="{{ frontendImage('additional_benefit', $benefit?->image, '1270x940') }}"
                        alt="@lang('Image')">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="additional-benefit__content">
                    <div class="section-heading style-left">
                        <h4 class="section-heading__title s-highlight" data-s-break="-1" data-s-length="1">
                            {{ __($benefit?->title) }}</h4>
                        <p class="section-heading__desc">{{ __($benefit?->subtitle) }}</p>
                    </div>

                    @foreach ($benefits as $benefitElement)
                        <div class="benefit-item d-flex flex-wrap">
                            <div class="benefit-item__icon flex-center">
                                <img src="{{ frontendImage('additional_benefit', $benefitElement?->data_values?->image, '60x50') }}"
                                    alt="@lang('Image')">
                            </div>
                            <div class="benefit-item__content">
                                <h6 class="benefit-item__title">{{ __($benefitElement?->data_values?->title) }}</h6>
                                <p class="benefit-item__desc">{{ __($benefitElement?->data_values?->subtitle) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@if ($hasAd1120x170)
    @php echo $advertise1120x170  @endphp
@endif
