@extends('Template::layouts.frontend')

@section('content')
    @php $isOrderProduct = $product->isAdminOrderProduct(); @endphp

    <section class="product-details pt-60 pb-120 {{ $isOrderProduct ? 'order-product-page' : '' }}">
        <div class="container order-product-page__container">
            @include('Template::user.product.top')
            <div class="row gy-4 {{ $isOrderProduct ? 'order-product-layout' : '' }}">
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
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(22, 163, 74, 0.10) 0%, rgba(22, 163, 74, 0) 34%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.10) 0%, rgba(245, 158, 11, 0) 32%),
                linear-gradient(180deg, #fafaf8 0%, #f4f5f1 100%);
        }

        .order-product-page::before,
        .order-product-page::after {
            content: "";
            position: absolute;
            pointer-events: none;
            border-radius: 999px;
            opacity: 0.6;
            filter: blur(12px);
        }

        .order-product-page::before {
            width: 260px;
            height: 260px;
            top: -90px;
            left: -90px;
            background: rgba(250, 204, 21, 0.14);
        }

        .order-product-page::after {
            width: 320px;
            height: 320px;
            right: -160px;
            bottom: 120px;
            background: rgba(22, 163, 74, 0.12);
        }

        .order-product-page__container {
            position: relative;
            z-index: 1;
        }

        .order-product-layout {
            align-items: flex-start;
        }

        .order-product-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.72fr);
            gap: 24px;
            padding: 30px;
            margin-bottom: 28px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 28px;
            background:
                linear-gradient(135deg, rgba(17, 24, 39, 0.98) 0%, rgba(26, 35, 50, 0.96) 58%, rgba(12, 97, 70, 0.92) 100%);
            box-shadow: 0 34px 90px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .order-product-hero::before,
        .order-product-hero::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .order-product-hero::before {
            width: 240px;
            height: 240px;
            top: -110px;
            right: -50px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.35) 0%, rgba(250, 204, 21, 0) 72%);
        }

        .order-product-hero::after {
            width: 200px;
            height: 200px;
            left: -80px;
            bottom: -130px;
            background: radial-gradient(circle, rgba(74, 222, 128, 0.24) 0%, rgba(74, 222, 128, 0) 74%);
        }

        .order-product-hero__content,
        .order-product-hero__aside {
            position: relative;
            z-index: 1;
        }

        .order-product-hero__eyebrow,
        .order-product-title-kicker,
        .order-purchase-card__eyebrow,
        .order-product-section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #facc15;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .order-product-hero__eyebrow::before,
        .order-purchase-card__eyebrow::before,
        .order-product-title-kicker::before,
        .order-product-section-kicker::before {
            content: "";
            width: 30px;
            height: 1px;
            background: currentColor;
            opacity: 0.72;
        }

        .order-product-hero__title {
            margin: 18px 0 14px;
            color: #fff;
            font-size: clamp(2rem, 3.5vw, 3.1rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.05;
        }

        .order-product-hero__summary {
            max-width: 720px;
            margin: 0;
            color: rgba(255, 255, 255, 0.76);
            font-size: 1.02rem;
            line-height: 1.72;
        }

        .order-product-hero__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .order-product-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            font-size: 0.92rem;
        }

        .order-product-chip strong {
            font-weight: 600;
            color: #fff;
        }

        .order-product-hero__stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .order-product-stat,
        .order-product-hero__aside {
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
        }

        .order-product-stat {
            min-height: 112px;
            padding: 18px 20px;
            border-radius: 22px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }

        .order-product-stat__label {
            display: block;
            margin-bottom: 12px;
            color: rgba(255, 255, 255, 0.62);
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .order-product-stat strong {
            display: block;
            color: #fff;
            font-size: 1.18rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .order-product-hero__aside {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 24px;
            padding: 24px;
            border-radius: 24px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .order-product-hero__badge {
            align-self: flex-start;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(74, 222, 128, 0.16);
            color: #d1fae5;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .order-product-hero__aside h6 {
            margin: 0 0 10px;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .order-product-hero__aside p {
            margin: 0;
            color: rgba(255, 255, 255, 0.74);
            line-height: 1.7;
        }

        .order-product-hero__aside .social-share {
            display: inline-flex;
        }

        .order-product-hero__aside .social-share__button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-weight: 600;
            transition: 0.2s ease-in-out;
        }

        .order-product-hero__aside .social-share__button:hover {
            background: rgba(255, 255, 255, 0.16);
        }

        .order-product-landing {
            display: grid;
            gap: 26px;
        }

        .order-product-gallery-card,
        .order-product-copy-card,
        .order-product-page .common-sidebar__item {
            position: relative;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 26px;
            padding: 24px;
            box-shadow:
                0 18px 48px rgba(15, 23, 42, 0.08),
                0 2px 0 rgba(255, 255, 255, 0.9) inset;
            overflow: hidden;
        }

        .order-product-gallery-card::before,
        .order-product-copy-card::before,
        .order-product-page .common-sidebar__item::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 1px;
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.45), rgba(34, 197, 94, 0.32), rgba(245, 158, 11, 0));
        }

        .order-product-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .order-product-card-pill {
            padding: 9px 14px;
            border-radius: 999px;
            background: #eef7f1;
            color: #15803d;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .order-product-main-image {
            border-radius: 22px;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(250, 204, 21, 0.18) 0%, rgba(250, 204, 21, 0) 32%),
                linear-gradient(180deg, #f9fbfd 0%, #edf1f5 100%);
            border: 1px solid rgba(15, 23, 42, 0.07);
        }

        .order-product-main-image img {
            width: 100%;
            height: 100%;
            max-height: 560px;
            object-fit: contain;
            display: block;
        }

        .order-product-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .order-product-thumb {
            padding: 0;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
            transition: 0.2s ease-in-out;
        }

        .order-product-thumb.is-active {
            border-color: rgba(22, 163, 74, 0.55);
            transform: translateY(-2px);
            box-shadow: 0 18px 28px rgba(22, 163, 74, 0.18);
        }

        .order-product-thumb img {
            width: 100%;
            height: 92px;
            object-fit: cover;
            display: block;
        }

        .order-product-section-head {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 0;
        }

        .order-product-section-head h5,
        .order-product-section-head h6 {
            margin: 0;
            color: hsl(var(--heading-color));
            font-size: 1.9rem;
            line-height: 1.18;
        }

        .order-product-copy-card .product-details-item {
            margin-top: 18px;
        }

        .order-product-copy-card .product-details-item > *:last-child {
            margin-bottom: 0;
        }

        .order-product-copy-card .product-details-item p {
            color: hsl(var(--body-color));
            font-size: 1rem;
            line-height: 1.85;
        }

        .order-product-related-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .order-product-related-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            background: linear-gradient(180deg, #fff 0%, #fbfbf9 100%);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.05);
            transition: 0.2s ease-in-out;
        }

        .order-product-related-card:hover {
            transform: translateY(-3px);
            border-color: rgba(22, 163, 74, 0.22);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
        }

        .order-product-related-card__media {
            width: 86px;
            min-width: 86px;
            height: 86px;
            border-radius: 18px;
            overflow: hidden;
            background: #f4f6f8;
        }

        .order-product-related-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .order-product-related-card__body {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .order-product-related-card__meta {
            color: #15803d;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .order-product-related-card__body strong {
            color: hsl(var(--heading-color));
            font-size: 1rem;
            line-height: 1.45;
        }

        .order-product-related-card__price {
            color: #111827;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .order-product-empty {
            padding: 18px 20px;
            border-radius: 20px;
            background: #f7faf8;
            color: hsl(var(--body-color));
            border: 1px dashed rgba(15, 23, 42, 0.12);
        }

        .order-product-sidebar-column .common-sidebar {
            display: grid;
            gap: 18px;
        }

        .order-product-page .common-sidebar__item {
            margin-bottom: 0 !important;
        }

        .order-product-page .common-sidebar__content {
            padding: 0;
            background: transparent;
            border: 0;
            box-shadow: none;
        }

        .order-purchase-card__hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .order-purchase-card .catalog-price-heading {
            min-height: 0;
            margin: 12px 0 10px;
            color: hsl(var(--heading-color));
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .order-purchase-card__lead {
            color: hsl(var(--body-color));
            line-height: 1.7;
        }

        .order-purchase-steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .order-purchase-step {
            padding: 14px;
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, 0.07);
            background: linear-gradient(180deg, #fdfdfc 0%, #f3f7f3 100%);
        }

        .order-purchase-step span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            margin-bottom: 12px;
            border-radius: 50%;
            background: #111827;
            color: #fff;
            font-weight: 700;
        }

        .order-purchase-step p {
            margin: 0;
            color: hsl(var(--heading-color));
            font-size: 0.88rem;
            line-height: 1.55;
        }

        .order-product-form .form-group {
            margin-bottom: 18px;
        }

        .order-product-form .form-label {
            margin-bottom: 10px;
            color: hsl(var(--heading-color));
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .order-product-form .form-control {
            min-height: 56px;
            border: 1px solid rgba(15, 23, 42, 0.11);
            border-radius: 16px;
            background: #fafbf8;
            box-shadow: none;
        }

        .order-product-form textarea.form-control {
            min-height: 126px;
            padding-top: 15px;
            resize: vertical;
        }

        .order-product-form .form-control:focus {
            border-color: rgba(22, 163, 74, 0.42);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.10);
        }

        .order-option-summary {
            border: 1px solid rgba(22, 163, 74, 0.12) !important;
            border-radius: 18px !important;
            background: linear-gradient(180deg, rgba(241, 249, 243, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
        }

        .order-option-summary .small {
            color: hsl(var(--heading-color));
            font-size: 0.88rem;
        }

        .order-option-summary .small:last-child {
            margin-bottom: 0 !important;
        }

        .order-product-page .catalog-submit-button,
        .order-product-page .btn-outline--base,
        .order-product-page .btn-outline--light {
            min-height: 56px;
            border-radius: 16px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .order-product-page .catalog-submit-button {
            box-shadow: 0 18px 28px rgba(22, 163, 74, 0.18);
        }

        .order-product-page .catalog-submit-button[disabled] {
            box-shadow: none;
            opacity: 0.75;
        }

        .order-product-page .btn-outline--base,
        .order-product-page .btn-outline--light {
            background: #fff;
        }

        .order-product-page .common-sidebar__button {
            margin-top: 14px !important;
        }

        .order-product-page .badge {
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .order-product-page .common-sidebar__title {
            margin-bottom: 18px;
            color: hsl(var(--heading-color));
            font-size: 1.35rem;
            font-weight: 700;
        }

        .order-product-info-card .product-info {
            margin: 0;
        }

        .product-info--order .product-info__item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .product-info--order .product-info__item:first-child {
            padding-top: 0;
        }

        .product-info--order .product-info__item:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .product-info--order .product-info__title {
            color: rgba(17, 24, 39, 0.66);
            font-size: 0.88rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .product-info--order .product-info__content {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
            min-width: 0;
            text-align: right;
        }

        .product-info--order .product-info__content span,
        .product-info--order .product-info__content a {
            color: hsl(var(--heading-color));
            font-weight: 600;
            word-break: break-word;
        }

        .product-info__tags {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .product-info__tags a {
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef7f1;
            color: #15803d !important;
            font-size: 0.85rem;
            font-weight: 700;
            transition: 0.2s ease-in-out;
        }

        .product-info__tags a:hover {
            background: #dcfce7;
        }

        .order-product-page .script-share .social-share__icons {
            min-width: min(400px, calc(100vw - 34px));
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.16);
        }

        .order-product-page .social-share .form-control,
        .order-product-page .social-share .input-group-text {
            background-color: #f7faf7 !important;
        }

        @media (min-width: 992px) {
            .order-product-sidebar-column .common-sidebar {
                position: sticky;
                top: 110px;
            }
        }

        @media (max-width: 1199px) {
            .order-product-hero {
                grid-template-columns: 1fr;
            }

            .order-product-hero__aside {
                gap: 18px;
            }
        }

        @media (max-width: 991px) {
            .order-product-page {
                padding-top: 40px;
            }

            .order-product-hero {
                padding: 24px 22px;
                margin-bottom: 24px;
                border-radius: 24px;
            }

            .order-product-hero__stats,
            .order-purchase-steps,
            .order-product-related-grid {
                grid-template-columns: 1fr;
            }

            .order-product-page .common-sidebar__item,
            .order-product-gallery-card,
            .order-product-copy-card {
                padding: 22px;
            }
        }

        @media (max-width: 767px) {
            .order-product-hero {
                padding: 20px 18px;
                border-radius: 22px;
            }

            .order-product-hero__title {
                font-size: 2rem;
            }

            .order-product-chip {
                width: 100%;
                justify-content: space-between;
            }

            .order-product-stat {
                min-height: 0;
            }

            .order-product-card-head,
            .product-info--order .product-info__item {
                flex-direction: column;
                align-items: flex-start;
            }

            .product-info--order .product-info__content,
            .product-info__tags {
                justify-content: flex-start;
                text-align: left;
            }

            .order-product-gallery-card,
            .order-product-copy-card,
            .order-product-page .common-sidebar__item {
                padding: 16px;
                border-radius: 18px;
            }

            .order-product-main-image img {
                max-height: 320px;
            }

            .order-product-related-card {
                align-items: flex-start;
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
