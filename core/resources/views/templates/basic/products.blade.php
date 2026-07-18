@extends('Template::layouts.frontend')
@section('content')
    @php
        $advertise728x90 = getAds('728x90');
        $hasAd728x90 = !empty($advertise728x90);
    @endphp
    <section class="product pt-60 pb-60">
        <div class="container">
            @include('Template::user.product.products_top')

            <div class="product__inner">
                @include('Template::user.product.products_sidebar')
                <button type="button" class="product-filter-backdrop" aria-label="@lang('Close filters')"></button>

                <div class="product-body">
                    <div id="spinner-overlay" class="spinner-overlay">
                        <div class="spinner-overlay__content">
                            <div class="spinner"></div>
                        </div>
                    </div>
                    @if ($products->count())
                        <div id="product-content">
                            <div class="row gy-4 product-grid-row">
                                @foreach ($products as $index => $product)
                                    <div class="col-xl-4 col-sm-6 col-xsm-6">
                                        <x-product :product="$product" />
                                    </div>
                                    @if ($hasAd728x90 && ($index + 1) % 12 == 0)
                                        <div class="col-12">
                                            @php echo $advertise728x90; @endphp
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="card custom--card">
                            <div class="card-body">
                                <x-empty-list title="No items found" />
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($products->hasPages())
                <div class="pt-4 pagination-wrapper">
                    {{ paginateLinks($products) }}
                </div>
            @endif
        </div>
    </section>
    @if ($products)
        @include('Template::user.product.add_to_collection')
    @endif

    @php
        if (Route::is('category.products')) {
            $url = route('category.products', ['category' => request()->category, 'subcategory' => request()->subcategory]);
        }else{
            $url = route('products');
        }
    @endphp
@endsection

@push('style')
    <style>
        .select2-container:has(.select2-selection--single) {
            width: auto !important;
        }

        .product-body {
            position: relative;
        }

        .spinner-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99;
            display: none;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 5px solid #ddd;
            border-top-color: hsl(var(--base));
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .product-card.loading {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .spinner-overlay__content {
            height: 100%;
            display: flex;
            justify-content: center;
            margin-top: 250px;
        }

        .product-grid-row {
            justify-content: flex-start;
            align-items: stretch;
        }

        .product-filter-backdrop {
            display: none;
        }

        @media (max-width: 767px) {
            body.mobile-filter-open {
                overflow: hidden;
            }

            .product {
                padding-top: 34px !important;
                padding-bottom: 42px !important;
            }

            .product-top {
                align-items: flex-start;
                gap: 10px !important;
                margin-bottom: 18px;
            }

            .product-top__right {
                flex: 1 1 100%;
                min-width: 0;
                overflow-x: auto;
                padding-bottom: 2px;
                scrollbar-width: none;
            }

            .product-top__right::-webkit-scrollbar {
                display: none;
            }

            .product-top .filter-button-list {
                flex-wrap: nowrap;
            }

            .product-top .sort-options .filter-button-list__button {
                min-height: 36px;
                padding: 8px 10px;
                white-space: nowrap;
            }

            .product .product-grid-row {
                --bs-gutter-x: 10px;
                --bs-gutter-y: 12px;
            }

            .product .product-grid-row > [class*="col"],
            .product .product-body.list-view .product-grid-row > [class*="col"],
            .toggle-sidebar .product .product-body .product-grid-row > [class*="col"] {
                width: 50% !important;
                max-width: 50%;
                flex: 0 0 auto;
            }

            .product .product-card,
            .product .product-body.list-view .product-card {
                display: flex;
                flex-direction: column;
                height: 100%;
                padding: 7px;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
            }

            .product .product-card__thumb,
            .product .product-body.list-view .product-card__thumb {
                width: 100%;
                max-width: none;
                flex: 0 0 auto;
                aspect-ratio: 4 / 3;
                border-radius: 8px;
                background: #f8fafc;
            }

            .product .product-card__thumb .link,
            .product .product-body.list-view .product-card__thumb .link {
                padding: 5px;
            }

            .product .product-card__content,
            .product .product-body.list-view .product-card__content {
                width: 100%;
                flex: 1 1 auto;
                padding-left: 0;
                margin-top: 8px;
            }

            .product .product-card__content-inner,
            .product .product-body.list-view .product-card__content-inner {
                display: block;
                margin-bottom: 6px;
            }

            .product .product-card__title {
                margin-bottom: 0;
            }

            .product .product-card__title .link {
                font-size: .76rem;
                line-height: 1.26;
                -webkit-line-clamp: 2;
            }

            .product .product-card__author {
                gap: 3px;
                margin-top: 3px;
            }

            .product .product-card__author .link {
                display: none;
            }

            .product .product-card__price,
            .product .product-card__sales {
                font-size: .68rem;
                line-height: 1.25;
            }

            .product .product-card__price {
                display: -webkit-box;
                overflow: hidden;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .product .product-card__price::before {
                width: 7px;
                height: 7px;
                margin-right: 4px;
                transform: translateY(-1px);
            }

            .product .product-card__content > .flex-between,
            .product .product-body.list-view .product-card__content > .flex-between {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .product .product-card__rating {
                display: none;
            }

            .product .collection-list {
                top: 7px;
                right: 7px;
                bottom: auto;
                width: auto;
                gap: 6px;
                visibility: visible;
                opacity: 1;
                justify-content: flex-end;
            }

            .product .product-body.list-view .product-card__thumb .collection-list {
                display: flex;
            }

            .product .collection-list.list-style {
                display: none;
            }

            .product .product-card__actions {
                gap: 6px !important;
            }

            .product .premium-cart-form {
                gap: 6px;
            }

            .product .premium-cart-select {
                min-height: 32px;
                height: 32px;
                border-radius: 7px;
                font-size: .68rem;
                padding: 0 7px;
                text-overflow: ellipsis;
            }

            .product .premium-cart-option-summary {
                padding: 6px;
                border-radius: 7px;
                gap: 5px;
            }

            .product .premium-cart-option-summary__price,
            .product .premium-cart-option-summary__meta {
                font-size: .64rem;
            }

            .product .premium-cart-row {
                grid-template-columns: 54px minmax(0, 1fr) !important;
                gap: 6px;
            }

            .product .premium-qty-selector {
                min-height: 32px;
                border-radius: 7px;
            }

            .product .premium-qty-selector__btn {
                width: 18px;
                height: 30px;
                font-size: .72rem;
            }

            .product .premium-qty-selector__input {
                font-size: .76rem;
            }

            .product .catalog-action-btn {
                min-height: 32px;
                padding: 6px 8px;
                border-radius: 7px;
                font-size: .7rem;
                gap: 4px;
                white-space: nowrap;
            }

            .product .catalog-action-btn i {
                font-size: .78rem;
            }
        }

        @media (max-width: 991px) {
            body.mobile-filter-open {
                overflow: hidden;
            }

            body.mobile-filter-open .cookies-card {
                opacity: 0;
                pointer-events: none;
            }

            .product-filter-backdrop {
                position: fixed;
                inset: 0;
                z-index: 99990;
                display: block;
                pointer-events: none;
                border: 0;
                background: rgba(15, 23, 42, .48);
                opacity: 0;
                transition: opacity .22s ease;
            }

            body.mobile-filter-open .product-filter-backdrop {
                pointer-events: auto;
                opacity: 1;
            }

            .product-sidebar {
                left: 0;
                right: 0;
                top: auto;
                bottom: 0;
                width: 100%;
                max-height: min(82vh, 680px);
                height: auto;
                padding: 22px 16px calc(22px + env(safe-area-inset-bottom));
                border-radius: 18px 18px 0 0;
                box-shadow: 0 -18px 42px rgba(15, 23, 42, .18);
                transform: translateY(108%);
                transition: transform .24s ease;
            }

            .product-sidebar.show,
            body.mobile-filter-open .product-sidebar {
                transform: translateY(0);
            }

            .product-sidebar::before {
                content: "";
                position: absolute;
                top: 8px;
                left: 50%;
                width: 44px;
                height: 4px;
                border-radius: 999px;
                background: #d7dce5;
                transform: translateX(-50%);
            }

            .product-sidebar .close-sidebar {
                position: absolute;
                top: 14px;
                right: 14px;
                width: 34px;
                height: 34px;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                border: 1px solid #e5e7eb;
                border-radius: 50%;
                background: #fff;
                color: #111827;
                z-index: 2;
            }

            .product-sidebar__item-wrappper {
                max-height: calc(82vh - 54px);
                overflow-y: auto;
                padding-top: 22px;
                padding-right: 2px;
                -webkit-overflow-scrolling: touch;
            }

            .product-sidebar__item {
                margin-bottom: 12px;
            }

            .product-sidebar__title {
                padding-bottom: 10px;
                font-size: .98rem;
            }

            .product-sidebar__content {
                margin-bottom: 18px;
            }

            .product-sidebar .text-list__item {
                min-height: 34px;
                margin-bottom: 8px;
                padding: 7px 8px;
                border-radius: 8px;
                background: #f8fafc;
            }

            .product-sidebar .text-list__item.active {
                background: hsl(var(--base) / .1);
            }
        }

        @media (max-width: 359px) {
            .product .product-grid-row > [class*="col"],
            .product .product-body.list-view .product-grid-row > [class*="col"],
            .toggle-sidebar .product .product-body .product-grid-row > [class*="col"] {
                width: 100% !important;
                max-width: 100%;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            function setLocalItem(key, value) {
                localStorage.setItem(key, value);
            }

            const $filterButton = $('.filter-btn');
            const $productSidebar = $('.product-sidebar');

            function isMobileFilter() {
                return window.innerWidth <= 991;
            }

            function openMobileFilter() {
                $('body').removeClass('toggle-sidebar scroll-hide-sm').addClass('mobile-filter-open');
                $('.sidebar-overlay').removeClass('show');
                $productSidebar.addClass('show');
                $filterButton.attr('aria-expanded', 'true');
            }

            function closeMobileFilter() {
                $('body').removeClass('mobile-filter-open toggle-sidebar scroll-hide-sm');
                $('.sidebar-overlay').removeClass('show');
                $productSidebar.removeClass('show');
                $filterButton.removeClass('filter_visible').attr('aria-expanded', 'false');
            }

            function toggleSidebar() {
                closeMobileFilter();
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (!isMobileFilter() && productFilterBtn == 'hidden') {
                    $('body').addClass('toggle-sidebar');
                } else {
                    $('body').removeClass('toggle-sidebar');
                }
                iconChange();
            }
            toggleSidebar();

            $filterButton.on('click', function() {
                if (isMobileFilter()) {
                    if ($('body').hasClass('mobile-filter-open')) {
                        closeMobileFilter();
                    } else {
                        openMobileFilter();
                    }
                    iconChange();
                    return;
                }

                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    setLocalItem('product_filter_btn', 'visible');
                } else {
                    setLocalItem('product_filter_btn', 'hidden');
                }
                toggleSidebar();
            });

            $('.product-filter-backdrop, .close-sidebar').on('click', function() {
                closeMobileFilter();
                iconChange();
            });

            $(document).on('keyup', function(event) {
                if (event.key === 'Escape') {
                    closeMobileFilter();
                    iconChange();
                }
            });

            function iconChange() {
                if (window.innerWidth <= 991) {
                    $filterButton.find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                } else {
                    const productFilterBtn = localStorage.getItem('product_filter_btn');
                    if (productFilterBtn == 'hidden') {
                        $filterButton.find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                    } else {
                        $filterButton.find(`i`).removeClass(`icon-Filter`).addClass(`fas fa-times`);
                    }
                }
            }

            const productViewType = localStorage.getItem('product_view_type') || 'grid-view';
            $('.view-buttons__btn.grid-view-btn').removeClass('text--base');
            if (productViewType == 'grid-view') {
                $('.view-buttons__btn.grid-view-btn').addClass('text--base');
            } else {
                $('.view-buttons__btn.list-view-btn').addClass('text--base');
            }

            $('.product-body').addClass(productViewType);
            $('.list-view-btn').on('click', function() {
                setLocalItem('product_view_type', 'list-view');
            });
            $('.grid-view-btn').on('click', function() {
                setLocalItem('product_view_type', 'grid-view');
            });

            // -------- Start Filter -------- //
            function fetchFilteredProducts(customUrl = null) {
                const filters = {
                    search: $('.search-filter').val(),
                    category: $('.text-list--category .text-list__item.active').data('category'),
                    rating: $('.rating-filter input[name="rating"]:checked').val(),
                    date_range: $('.date-filter input[name="date_range"]:checked').val(),
                    sort_by: $('.sort-btn.active').data('sort') || getUrlParameter('sort_by') || 'new_item'
                };

                const cleanFilters = Object.fromEntries(
                    Object.entries(filters).filter(([_, v]) => v !== undefined && v !== '')
                );

                const queryString = new URLSearchParams(cleanFilters).toString();

                const url = customUrl || `{{ $url }}?${queryString}`;

                showSpinner();

                $.ajax({
                    url,
                    type: 'GET',
                    success: function(response) {
                        $('#product-content').html(response.html);
                        if (typeof window.refreshPremiumCartForms === 'function') {
                            window.refreshPremiumCartForms($('#product-content'));
                        }
                        if (response.pagination) {
                            $('.pagination-wrapper').html(response.pagination).hide();
                        }
                        if (!customUrl) {
                            history.pushState(null, null, url);
                        }
                    },
                    complete: function() {
                        hideSpinner();
                    },
                    error: function() {
                        alert('Failed to load products.');
                        $('.pagination-wrapper').show();
                    }
                });

                $('html, body').animate({
                    scrollTop: $('#product-content').offset().top
                }, 500);
            }

            function showSpinner() {
                $('#spinner-overlay').show();
                $('#product-content .product-card').addClass('loading');
                $('.pagination-wrapper').hide();
            }

            function hideSpinner() {
                $('#spinner-overlay').hide();
                $('#product-content .product-card').removeClass('loading');
            }


            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            const debounce = (func, delay) => {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            };

            $(window).on('resize', debounce(function() {
                toggleSidebar();
            }, 150));

            $(document).ready(function() {
                const urlParams = new URLSearchParams(window.location.search);

                if (urlParams.has('category')) {
                    $(`.text-list--category .text-list__item[data-category="${urlParams.get('category')}"]`)
                        .addClass('active');
                }

                if (urlParams.has('rating')) {
                    $(`.rating-filter input[value="${urlParams.get('rating')}"]`).prop('checked', true);
                }

                if (urlParams.has('date_range')) {
                    $(`.date-filter input[value="${urlParams.get('date_range')}"]`).prop('checked', true);
                }

                if (urlParams.has('sort_by')) {
                    $(`.sort-btn[data-sort="${urlParams.get('sort_by')}"]`).addClass('active');
                }

                if (urlParams.has('search')) {
                    $('.search-filter').val(urlParams.get('search'));
                }

                if (window.location.search) {
                    $('.pagination-wrapper').hide();
                }
            });

            // Event Listeners
            $('.sort-options').on('click', '.sort-btn', function() {
                $('.sort-btn').removeClass('active');
                $(this).addClass('active');
                fetchFilteredProducts();
            });

            $('.search-filter').on('input', debounce(function() {
                fetchFilteredProducts();
            }, 500));

            $('.text-list--category').on('click', '.text-list__item', function() {
                $('.text-list__item').removeClass('active');
                $(this).addClass('active');
                fetchFilteredProducts();
                closeMobileFilter();
            });

            $('.rating-filter, .date-filter').on('change', 'input[type="radio"]', function() {
                fetchFilteredProducts();
                closeMobileFilter();
            });

            $(document).on('click', '.pagination-wrapper a, #product-content .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                fetchFilteredProducts(url);
            });

            window.addEventListener('popstate', function() {
                fetchFilteredProducts(window.location.href);
            });

        })(jQuery);
    </script>
@endpush
