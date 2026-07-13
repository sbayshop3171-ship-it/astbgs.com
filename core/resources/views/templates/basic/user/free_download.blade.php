@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">{{ __($pageTitle) }}</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm"
                    placeholder="Search..." />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($downloadedItems->count() == 0)
                        <x-empty-list title="No purchase data found" />
                    @else
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Product | Date')</th>
                                            <th>@lang('Author')</th>
                                            <th>@lang('Category')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($downloadedItems ?? [] as $product)
                                            <tr>
                                                <td>
                                                    <div class="table-product flex-align">
                                                        <div class="table-product__thumb">
                                                            <x-product-thumbnail :product="$product" />
                                                        </div>
                                                        @if (isset($product))
                                                            <div class="table-product__content">
                                                                <a href="{{ route('product.details', $product->slug) }}"
                                                                    class="table-product__name">
                                                                    {{ __(strLimit($product->title, 20)) }}
                                                                </a>
                                                                {{ showDateTime($product->created_at) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('user.profile', $product->author?->username) }}"
                                                        class="link">
                                                        {{ __($product->author->fullname) }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ __($product->category?->name) }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                                                        <a href="{{ route('user.product.download', $product->slug) }}"
                                                            class="btn btn-outline--base btn--sm">
                                                            <i class="la la-download"></i> @lang('Download')
                                                        </a>
                                                        <button class="btn btn-outline--warning btn--sm review_button"
                                                            data-review="{{ optional($product->reviews->first())->review }}"
                                                            data-rating="{{ optional($product->reviews->first())->rating }}"
                                                            data-category_id="{{ optional($product->reviews->first())->review_category_id }}"
                                                            data-product_id="{{ $product->id }}">
                                                            <i class="la la-star"></i> @lang('Review')
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($downloadedItems?->hasPages())
                            <div class="card-footer">
                                <div class="py-2">
                                    {{ paginateLinks($downloadedItems) }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @lang('Review this Item')
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <label class="form-label me-2" for="rating">@lang('Your Rating')</label>
                                    <div id="star"></div>
                                    <input type="hidden" name="rating" required>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('Rating Category')</label>
                                    <select name="review_category" class="form--control" required>
                                        <option value="">@lang('Select a category')</option>
                                        @foreach ($reviewCategories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="review" class="form-label">@lang('Review')</label>
                                    <textarea name="review" id="review" class="form--control" placeholder="@lang('Your Review')" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--md w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/jquery.raty.css') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.raty.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            // Handle ordering changes
            $('#order_by').on('change', function() {
                const orderBy = $(this).val();
                const url = location.toString().replace(location.search, "");
                window.location.href = `${url}?order_by=${orderBy}`;
            });

            let initRaty = function(score) {
                $('#star').raty({
                    starHalf: "{{ asset('assets/images/star-half.png') }}",
                    starOff: "{{ asset('assets/images/star-off.png') }}",
                    starOn: "{{ asset('assets/images/star-on.png') }}",
                    score: score || 0,
                    click: function(score, e) {
                        $('[name="rating"]').val(score);
                    }
                });
            };

            let modal = $('#reviewModal');

            $('.review_button').on('click', function() {
                let data = $(this).data();

                let route = `{{ route('user.author.free.review.store', ':id') }}`.replace(':id', data
                    .product_id);
                modal.find('form').attr('action', route);

                if (data.review) {
                    $('[name="review"]').val(data.review);
                    $('[name="rating"]').val(data.rating);
                    $('[name="review_category"]').val(data.category_id);
                } else {
                    $('[name="review"]').val('');
                    $('[name="rating"]').val('');
                    $('[name="review_category"]').val('');
                }

                initRaty(data.rating || 0);

                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
                $('[name="rating"]').val('');
                $('#star').raty('destroy');
                initRaty(0);
                $('#star').html('')
            });

        })(jQuery);
    </script>
@endpush
