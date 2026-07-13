@php
    $bestSelling = getContent('marketplace.content', true);
    $bestSellingElement = getContent('marketplace.element');
@endphp
<section class="browse-best-selling py-120 overflow-hidden">
    <div class="container">
        <div>
            <div class="row gy-4 align-items-center">
                <div class="col-lg-6 pe-lg-5">
                    <div class="section-heading style-left">
                        <h4 class="section-heading__title s-highlight" data-s-break="1" data-s-length="1">
                            {{ __($bestSelling?->data_values?->title) }}</h4>
                        <p class="section-heading__desc">{{ __($bestSelling?->data_values?->subtitle) }}</p>
                    </div>
                    <div class="best-selling-content">

                        <ul class="content-list">
                            @foreach ($bestSellingElement as $item)
                                <li class="content-list__item">
                                    <span class="content-list__icon">
                                        <i class="las la-check"></i>
                                    </span>
                                    {{ __($item?->data_values?->content) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="thumb-wrapper">
                        <div class="thumb-wrapper__bottom">
                            <a href="{{ route('products') }}?sort_by=best_downloading"
                                class="btn btn-outline--base btn--sm  btn--link">@lang('View All Items')
                                <span class="icon"><i class="las la-arrow-right"></i></span>
                            </a>
                        </div>
                        <div class="best-selling-thumb">
                            <img src="{{ frontendImage('marketplace', $bestSelling?->data_values?->image, '1270x940') }}"
                                alt="@lang('Image')">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .content-list__item {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0));
            padding: 10px 20px;
            border-radius: 12px;
            display: flex;
            gap: 10px;
            border-left: 8px solid hsl(var(--base)/.7);
            color: hsl(var(--black)/.7);
            font-weight: 500;
            align-items: center;
        }

        .content-list__icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: hsl(var(--white)/.6);
            box-shadow: 0 0 5px hsl(var(--black)/.2);
            flex-shrink: 0;
            font-size: 18px;
            color: hsl(var(--base));
        }
        @media (max-width: 991px) {
            .content-list__item {
                background: hsl(var(--white)/.5);
            }
        }

        @media (max-width: 424px) {
            .content-list__item {
                padding: 5px 10px;
                font-size: 14px;
            }

            .content-list__icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
        }

        .content-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .thumb-wrapper__bottom {
            text-align: end;
            margin-bottom: 24px;
        }
    </style>
@endpush
