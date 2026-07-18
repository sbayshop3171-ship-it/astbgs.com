@extends('Template::layouts.frontend')

@section('content')
    <main class="home-mobile-shop">
        @include('Template::sections.banner')

        @if (isset($sections->secs) && $sections->secs != null)
            @foreach (json_decode($sections->secs) as $sec)
                @include('Template::sections.' . $sec)
            @endforeach
        @endif
    </main>

    @include('Template::user.product.add_to_collection')
@endsection

@pushOnce('style')
    <style>
        @media (max-width: 767px) {
            .home-mobile-shop {
                background: #fff;
            }

            .home-mobile-shop .container {
                max-width: 100%;
                padding-left: 16px;
                padding-right: 16px;
            }

            .home-mobile-shop .category,
            .home-mobile-shop .featured-products,
            .home-mobile-shop .latest-template,
            .home-mobile-shop .browse-best-selling,
            .home-mobile-shop .pricing-area,
            .home-mobile-shop .additional-benefit {
                padding-top: 44px !important;
                padding-bottom: 44px !important;
            }

            .home-mobile-shop .banner {
                min-height: 520px;
                height: auto;
                padding: 72px 0 56px;
                background-attachment: scroll !important;
                background-position: center top !important;
            }

            .home-mobile-shop .banner::before {
                background: rgba(0, 0, 0, .66);
            }

            .home-mobile-shop .banner-wrapper {
                align-items: center;
            }

            .home-mobile-shop .banner-content {
                width: 100%;
                max-width: 390px;
            }

            .home-mobile-shop .banner-content__title {
                font-size: 1.48rem;
                line-height: 1.28;
                margin-bottom: 12px;
            }

            .home-mobile-shop .banner-content__desc {
                font-size: .96rem;
                line-height: 1.55;
                margin-bottom: 18px;
            }

            .home-mobile-shop .search-box {
                height: auto;
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 8px;
                padding: 8px;
                border-radius: 14px;
                background: #fff;
            }

            .home-mobile-shop .search-box .form--control {
                min-width: 0;
                height: 44px;
                padding: 0 12px;
                border: 0;
                border-radius: 10px;
                box-shadow: none;
            }

            .home-mobile-shop .search-box .form--control::placeholder {
                font-size: .85rem;
            }

            .home-mobile-shop .search-box__btn {
                position: static;
                height: 44px;
                min-width: 88px;
                padding: 0 14px;
                border-radius: 10px;
                justify-content: center;
            }

            .home-mobile-shop .banner .btn-wrapper {
                gap: 8px !important;
                margin-top: 18px !important;
            }

            .home-mobile-shop .banner .btn-wrapper .btn {
                min-height: 34px;
                padding: 7px 11px;
                border-radius: 8px;
                font-size: .76rem;
            }

            .home-mobile-shop .section-heading {
                margin-bottom: 20px;
            }

            .home-mobile-shop .section-heading__title,
            .home-mobile-shop .feature-box__title {
                font-size: 1.36rem;
                line-height: 1.24;
                letter-spacing: 0;
            }

            .home-mobile-shop .section-heading__desc,
            .home-mobile-shop .feature-box__desc {
                font-size: .94rem;
                line-height: 1.55;
            }

            .home-mobile-shop .section-heading.style-left {
                text-align: left;
            }

            .home-mobile-shop .category .section-heading.flex-between {
                display: flex;
                flex-wrap: nowrap !important;
                align-items: center;
                gap: 12px !important;
            }

            .home-mobile-shop .category .section-heading__inner {
                min-width: 0;
                flex: 1 1 auto;
            }

            .home-mobile-shop .category .section-heading__title {
                font-size: 1.28rem;
            }

            .home-mobile-shop .category .section-heading .btn {
                flex: 0 0 auto;
                min-height: 36px;
                padding: 8px 10px;
                border-radius: 8px;
                font-size: .76rem;
                white-space: nowrap;
            }

            .home-mobile-shop .home-category-grid {
                --bs-gutter-y: 14px;
            }

            .home-mobile-shop .popular-category-item {
                min-height: 144px;
                border-radius: 8px;
                background-image: linear-gradient(90deg, hsl(var(--base) / .06), #fff 56%);
            }

            .home-mobile-shop .popular-category-item__title {
                min-width: 43%;
                padding: 18px 8px 18px 20px;
            }

            .home-mobile-shop .popular-category-item__title h5 {
                width: auto;
                max-width: 100%;
                font-size: 1rem;
                line-height: 1.24;
                display: -webkit-box;
                overflow: hidden;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .home-mobile-shop .popular-category-item__title span {
                margin-top: 2px;
                font-size: .9rem;
            }

            .home-mobile-shop .popular-category-item__content {
                width: 57%;
            }

            .home-mobile-shop .popular-category-item:hover .popular-category-item__content {
                width: 57%;
            }

            .home-mobile-shop .featured-products > .container > .row {
                row-gap: 24px;
            }

            .home-mobile-shop .featured-products .btn--link,
            .home-mobile-shop .latest-template .btn--link,
            .home-mobile-shop .browse-best-selling .btn--link {
                min-height: 38px;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: .8rem;
            }

            .home-mobile-shop .home-product-grid {
                --bs-gutter-x: 10px;
                --bs-gutter-y: 12px;
            }

            .home-mobile-shop .home-product-grid > [class*="col"] {
                width: 50%;
                flex: 0 0 auto;
            }

            .home-mobile-shop .home-product-grid .product-card {
                padding: 7px;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
            }

            .home-mobile-shop .home-product-grid .product-card__thumb {
                aspect-ratio: 4 / 3;
                border-radius: 8px;
                background: #f8fafc;
            }

            .home-mobile-shop .home-product-grid .product-card__thumb .link {
                padding: 5px;
            }

            .home-mobile-shop .collection-list {
                top: 7px;
                right: 7px;
                bottom: auto;
                width: auto;
                gap: 6px;
                visibility: visible;
                opacity: 1;
                justify-content: flex-end;
            }

            .home-mobile-shop .collection-list.list-style {
                display: none;
            }

            .home-mobile-shop .collection-list__button {
                width: 30px;
                height: 30px;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                border: 1px solid hsl(var(--base) / .42);
                background: rgba(255, 255, 255, .98);
                color: hsl(var(--base)) !important;
                line-height: 1;
                text-align: center;
                box-shadow: 0 8px 18px rgba(15, 23, 42, .2), 0 0 0 2px rgba(255, 255, 255, .72);
            }

            .home-mobile-shop .collection-list__button i,
            .home-mobile-shop .collection-list__button i::before {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: hsl(var(--base)) !important;
                line-height: 1;
            }

            .home-mobile-shop .collection-list__button:hover,
            .home-mobile-shop .collection-list__button:focus,
            .home-mobile-shop .collection-list__button.wishlisted,
            .home-mobile-shop .collection-list__button.collected {
                border-color: hsl(var(--base));
                background: hsl(var(--base));
                color: #fff !important;
            }

            .home-mobile-shop .collection-list__button:hover i,
            .home-mobile-shop .collection-list__button:hover i::before,
            .home-mobile-shop .collection-list__button:focus i,
            .home-mobile-shop .collection-list__button:focus i::before,
            .home-mobile-shop .collection-list__button.wishlisted i,
            .home-mobile-shop .collection-list__button.wishlisted i::before,
            .home-mobile-shop .collection-list__button.collected i,
            .home-mobile-shop .collection-list__button.collected i::before {
                color: #fff !important;
            }

            .home-mobile-shop .home-product-grid .product-card__content {
                margin-top: 8px;
            }

            .home-mobile-shop .home-product-grid .product-card__content-inner {
                display: block;
                margin-bottom: 6px;
            }

            .home-mobile-shop .home-product-grid .product-card__title {
                margin-bottom: 0;
            }

            .home-mobile-shop .home-product-grid .product-card__title .link {
                font-size: .76rem;
                line-height: 1.26;
                -webkit-line-clamp: 2;
            }

            .home-mobile-shop .home-product-grid .product-card__author {
                gap: 3px;
                margin-top: 3px;
            }

            .home-mobile-shop .home-product-grid .product-card__author .link {
                display: none;
            }

            .home-mobile-shop .home-product-grid .product-card__price,
            .home-mobile-shop .home-product-grid .product-card__sales {
                font-size: .68rem;
                line-height: 1.25;
            }

            .home-mobile-shop .home-product-grid .product-card__price {
                display: -webkit-box;
                overflow: hidden;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .home-mobile-shop .home-product-grid .product-card__price::before {
                width: 7px;
                height: 7px;
                margin-right: 4px;
                transform: translateY(-1px);
            }

            .home-mobile-shop .home-product-grid .product-card__content > .flex-between {
                gap: 6px;
            }

            .home-mobile-shop .home-product-grid .product-card__rating {
                display: none;
            }

            .home-mobile-shop .rating-list__item {
                padding-right: 1px;
                font-size: .72rem;
            }

            .home-mobile-shop .home-product-grid .product-card__actions {
                gap: 6px !important;
            }

            .home-mobile-shop .home-product-grid .premium-cart-form {
                gap: 6px;
            }

            .home-mobile-shop .home-product-grid .premium-cart-select {
                min-height: 32px;
                height: 32px;
                border-radius: 7px;
                font-size: .68rem;
                padding: 0 7px;
                text-overflow: ellipsis;
            }

            .home-mobile-shop .home-product-grid .premium-cart-option-summary {
                padding: 6px;
                border-radius: 7px;
                gap: 5px;
            }

            .home-mobile-shop .home-product-grid .premium-cart-option-summary__price,
            .home-mobile-shop .home-product-grid .premium-cart-option-summary__meta {
                font-size: .64rem;
            }

            .home-mobile-shop .home-product-grid .premium-cart-row {
                grid-template-columns: 54px minmax(0, 1fr);
                gap: 6px;
            }

            .home-mobile-shop .home-product-grid .premium-qty-selector {
                min-height: 32px;
                border-radius: 7px;
            }

            .home-mobile-shop .home-product-grid .premium-qty-selector__btn {
                width: 18px;
                height: 30px;
                font-size: .72rem;
            }

            .home-mobile-shop .home-product-grid .premium-qty-selector__input {
                font-size: .76rem;
            }

            .home-mobile-shop .home-product-grid .catalog-action-btn {
                min-height: 32px;
                padding: 6px 8px;
                border-radius: 7px;
                font-size: .7rem;
                gap: 4px;
                white-space: nowrap;
            }

            .home-mobile-shop .home-product-grid .catalog-action-btn i {
                font-size: .78rem;
            }

            .home-mobile-shop .latest-template > .container > .d-flex {
                display: block !important;
            }

            .home-mobile-shop .latest-template .section-heading {
                margin-bottom: 14px;
            }

            .home-mobile-shop .latest-template .view-all-btn {
                text-align: left !important;
                margin-bottom: 16px;
            }

            .home-mobile-shop .custom--tab {
                display: flex;
                flex-wrap: nowrap;
                gap: 8px;
                overflow-x: auto;
                overflow-y: hidden;
                padding-bottom: 4px;
                margin-bottom: 18px !important;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .home-mobile-shop .custom--tab::-webkit-scrollbar {
                display: none;
            }

            .home-mobile-shop .custom--tab .nav-item {
                flex: 0 0 auto;
                margin-right: 0;
            }

            .home-mobile-shop .custom--tab .nav-item .nav-link {
                min-height: 34px;
                padding: 7px 11px !important;
                border-radius: 8px;
                white-space: nowrap;
            }

            .home-mobile-shop .browse-best-selling {
                background: #f8fafc;
            }

            .home-mobile-shop .browse-best-selling .row {
                row-gap: 24px;
            }

            .home-mobile-shop .content-list__item {
                border-left-width: 5px;
                border-radius: 8px;
                padding: 9px 10px;
                font-size: .84rem;
                line-height: 1.45;
            }

            .home-mobile-shop .content-list__icon {
                width: 32px;
                height: 32px;
                font-size: .85rem;
            }

            .home-mobile-shop .thumb-wrapper__bottom {
                text-align: left;
                margin-bottom: 14px;
            }

            .home-mobile-shop .best-selling-thumb,
            .home-mobile-shop .additional-benefit__thumb {
                border-radius: 8px;
                overflow: hidden;
            }

            .home-mobile-shop .pricing-area .section-heading {
                margin-bottom: 18px;
            }

            .home-mobile-shop .pricing__tabs .custom--tab {
                width: 100%;
                max-width: 260px;
                justify-content: center;
                margin-bottom: 22px !important;
            }

            .home-mobile-shop .pricing {
                padding: 22px 16px;
                border-radius: 8px;
            }

            .home-mobile-shop .pricing__card {
                padding: 6px;
                border-radius: 8px;
            }

            .home-mobile-shop .pricing .plan-name {
                font-size: 1rem;
            }

            .home-mobile-shop .pricing__title {
                font-size: 1.55rem;
            }

            .home-mobile-shop .pricing__bottom ul {
                margin-bottom: 20px;
            }

            .home-mobile-shop .pricing_plan-more-info-block {
                display: grid;
                padding: 18px;
                border-radius: 8px;
                gap: 14px;
            }

            .home-mobile-shop .pricing_plan-additional-title-2 {
                font-size: 1rem;
            }

            .home-mobile-shop .pricing_plan-basic-text {
                font-size: .86rem;
            }

            .home-mobile-shop .additional-benefit::before,
            .home-mobile-shop .additional-benefit::after {
                display: none;
            }

            .home-mobile-shop .additional-benefit__content {
                padding-left: 0;
            }

            .home-mobile-shop .benefit-item {
                flex-wrap: nowrap !important;
                align-items: flex-start;
                gap: 12px;
                padding: 14px;
                margin-bottom: 12px;
            }

            .home-mobile-shop .benefit-item__icon {
                width: 44px;
                height: 44px;
                flex: 0 0 44px;
            }

            .home-mobile-shop .benefit-item__icon img {
                width: 26px;
                height: 26px;
            }

            .home-mobile-shop .benefit-item__content {
                width: auto;
                padding-left: 0;
                min-width: 0;
            }

            .home-mobile-shop .benefit-item__title {
                margin-bottom: 4px;
                font-size: .95rem;
            }

            .home-mobile-shop .benefit-item__desc {
                font-size: .82rem;
                line-height: 1.48;
            }
        }

        @media (max-width: 374px) {
            .home-mobile-shop .home-product-grid > [class*="col"] {
                width: 100%;
            }

            .home-mobile-shop .product-card__thumb {
                aspect-ratio: 16 / 10;
            }
        }
    </style>
@endPushOnce
