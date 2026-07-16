@extends('Template::layouts.frontend')

@section('content')
    @php $isOrderProduct = $product->isAdminOrderProduct(); @endphp

    <section class="product-details pt-60 pb-120 {{ $isOrderProduct ? 'order-product-page' : '' }}">
        <div class="container">
            @include('Template::user.product.top')
            <div class="row gy-4 product-detail-layout">
                <div class="col-lg-8 product-detail-main-col">
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

        .product-detail-layout,
        .product-detail-main-col,
        .product-detail-sidebar-col,
        .product-details__inner,
        .product-details__content,
        .product-details-top,
        .product-details-top__inner,
        .product-details-top__right,
        .order-product-topbar,
        .order-product-topbar__share {
            min-width: 0;
        }

        .product-details__inner,
        .order-product-landing {
            display: grid;
            gap: 24px;
        }

        .product-details__thumb,
        .order-product-page .common-sidebar__item,
        .order-product-gallery-card,
        .order-product-copy-card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            box-shadow: none;
        }

        .product-details__thumb {
            overflow: hidden;
        }

        .product-details__thumb>img {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: contain;
            object-position: center;
            padding: clamp(14px, 2vw, 20px);
            background: linear-gradient(180deg, #f8fafc 0%, #eef3f8 100%);
            border-radius: 18px 18px 0 0;
        }

        .product-details__buttons {
            padding: clamp(16px, 2vw, 24px);
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            background: transparent;
            gap: 14px;
            flex-wrap: wrap;
        }

        .product-details__buttons .btn {
            flex: 1 1 220px;
            max-width: 100%;
            border-radius: 12px;
        }

        .product-details-item:first-child {
            margin-top: 0;
        }

        .product-details__title,
        .product-details-item__title,
        .order-product-section-head h5,
        .order-product-section-head h6 {
            overflow-wrap: anywhere;
        }

        .product-details-top__inner,
        .order-product-topbar {
            align-items: flex-start !important;
        }

        .product-details-top__right,
        .order-product-topbar__share {
            flex-wrap: wrap;
            gap: 12px;
        }

        .order-product-badges .badge {
            white-space: normal;
            line-height: 1.35;
        }

        .order-product-page .common-sidebar__item,
        .order-product-gallery-card,
        .order-product-copy-card {
            padding: clamp(18px, 2.2vw, 24px);
            box-shadow: none;
        }

        .order-product-main-image {
            display: grid;
            place-items: center;
            min-height: clamp(280px, 42vw, 520px);
            aspect-ratio: 16 / 11;
            padding: clamp(12px, 2vw, 18px);
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fafc 0%, #eef3f8 100%);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .order-product-main-image img {
            width: 100%;
            height: 100%;
            max-height: none;
            object-fit: contain;
            object-position: center;
            display: block;
        }

        .order-product-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(78px, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .order-product-thumb {
            display: grid;
            place-items: center;
            aspect-ratio: 1;
            padding: 10px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fafc 0%, #eef3f8 100%);
            transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
            box-shadow: none;
        }

        .order-product-thumb:hover {
            border-color: rgba(15, 23, 42, 0.14);
            background: #fff;
            transform: translateY(-1px);
        }

        .order-product-thumb.is-active {
            border-color: hsl(var(--base) / .55);
            background: #fff;
            box-shadow: none;
        }

        .order-product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .order-product-section-head {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .order-product-section-kicker {
            color: #16a34a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .order-purchase-card .catalog-price-heading {
            min-height: 0;
        }

        .order-purchase-card .form-group,
        .order-purchase-card .common-sidebar__button,
        .order-purchase-card .catalog-cart-actions,
        .order-purchase-card .catalog-action-btn,
        .order-purchase-card .premium-qty-selector,
        .order-purchase-card .form-control {
            width: 100%;
        }

        .order-purchase-card textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .more-product-thumbs {
            gap: 12px;
        }

        .more-product-thumbs__item {
            width: min(84px, 100%);
            max-width: 84px;
        }

        .more-product-thumbs__item a {
            width: 100%;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            padding: 8px;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: linear-gradient(180deg, #f8fafc 0%, #eef3f8 100%);
            overflow: hidden;
        }

        .more-product-thumbs__item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        @media (max-width: 991px) {
            .product-details-top__inner,
            .order-product-topbar {
                flex-direction: column;
                align-items: stretch !important;
            }

            .product-details-top__right,
            .order-product-topbar__share {
                width: 100%;
                justify-content: flex-start;
            }

            .order-product-main-image {
                min-height: clamp(240px, 58vw, 420px);
            }
        }

        @media (max-width: 767px) {
            .product-details__inner,
            .order-product-landing {
                gap: 18px;
            }

            .order-product-gallery-card,
            .order-product-copy-card {
                padding: 16px;
                border-radius: 14px;
            }

            .product-details__thumb>img,
            .order-product-main-image {
                aspect-ratio: 4 / 3;
            }

            .product-details__thumb>img {
                padding: 12px;
            }

            .product-details__buttons {
                display: grid;
                grid-template-columns: 1fr;
            }

            .product-details__buttons .btn {
                width: 100%;
                flex-basis: auto;
            }

            .more-product-thumbs {
                gap: 10px;
            }

            .more-product-thumbs__item {
                max-width: 72px;
            }

            .order-product-thumbs {
                grid-template-columns: repeat(auto-fit, minmax(64px, 1fr));
                gap: 8px;
            }

            .order-product-thumb {
                padding: 8px;
                border-radius: 12px;
            }

            .order-product-main-image {
                min-height: clamp(220px, 78vw, 340px);
            }
        }

        @media (max-width: 424px) {
            .product-details__thumb,
            .order-product-page .common-sidebar__item,
            .order-product-gallery-card,
            .order-product-copy-card,
            .order-product-main-image {
                border-radius: 16px;
            }

            .product-details__thumb>img {
                border-radius: 14px 14px 0 0;
            }

            .product-details__buttons {
                padding: 14px;
                gap: 10px;
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
