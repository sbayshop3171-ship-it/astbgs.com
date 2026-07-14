@extends('Template::layouts.frontend')

@section('content')
    @php $isOrderProduct = $product->isAdminOrderProduct(); @endphp

    <section class="product-details pt-60 pb-120 {{ $isOrderProduct ? 'order-product-page' : '' }}">
        <div class="container">
            @include('Template::user.product.top')
            <div class="row gy-4">
                <div class="col-lg-8">
                    @include('Template::user.product.description')
                    @php
                        echo getAds('728x90');
                    @endphp
                </div>
                @include('Template::partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#showScreenshots').on('click', function(event) {
                event.preventDefault();

                $('#screenshotsGallery').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {
                        enabled: true
                    }
                }).magnificPopup('open');
            });

            $(document).on('click', '.order-product-thumb', function() {
                const image = $(this).data('image');
                $('#orderProductMainImage').attr('src', image);
                $('.order-product-thumb').removeClass('is-active');
                $(this).addClass('is-active');
            });

            $('.download-button').on('click', function() {
                setTimeout(() => {
                    $(this).not('[disabled]').addClass('disabled');
                }, 2000);
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .order-product-page {
            background: transparent;
        }

        .order-product-landing {
            display: grid;
            gap: 24px;
        }

        .order-product-page .common-sidebar__item,
        .order-product-gallery-card,
        .order-product-copy-card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            padding: 22px;
            box-shadow: none;
        }

        .order-product-main-image {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .order-product-main-image img {
            width: 100%;
            height: 100%;
            max-height: 520px;
            object-fit: cover;
            display: block;
        }

        .order-product-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(84px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .order-product-thumb {
            padding: 0;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            transition: .2s ease-in-out;
            box-shadow: none;
        }

        .order-product-thumb.is-active {
            border-color: #16a34a;
            box-shadow: none;
        }

        .order-product-thumb img {
            width: 100%;
            height: 84px;
            object-fit: cover;
            display: block;
        }

        .order-product-section-head {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .order-product-section-kicker {
            color: #16a34a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .order-purchase-card .catalog-price-heading {
            min-height: 52px;
        }

        @media (max-width: 767px) {
            .order-product-gallery-card,
            .order-product-copy-card {
                padding: 16px;
                border-radius: 14px;
            }

            .order-product-main-image img {
                max-height: 320px;
            }
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/magnific-popup.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.magnific-popup.min.js') }}"></script>
@endpush
