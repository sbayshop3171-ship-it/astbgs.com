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
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            function setLocalItem(key, value) {
                localStorage.setItem(key, value);
            }

            function toggleSidebar() {
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    $('body').addClass('toggle-sidebar');
                } else {
                    $('body').removeClass('toggle-sidebar');
                }
                iconChange();
            }
            toggleSidebar();

            $('.filter-btn').on('click', function() {
                $(this).toggleClass('filter_visible');
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    setLocalItem('product_filter_btn', 'visible');
                } else {
                    setLocalItem('product_filter_btn', 'hidden');
                }
                iconChange();
            });

            function iconChange() {
                if (window.innerWidth <= 991) {
                    $(".filter-btn").find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                } else {
                    const productFilterBtn = localStorage.getItem('product_filter_btn');
                    if (productFilterBtn == 'hidden') {
                        $(".filter-btn").find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                    } else {
                        $(".filter-btn").find(`i`).removeClass(`icon-Filter`).addClass(`fas fa-times`);
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
            });

            $('.rating-filter, .date-filter').on('change', 'input[type="radio"]', function() {
                fetchFilteredProducts();
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
