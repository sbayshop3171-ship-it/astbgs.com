@extends('Template::layouts.frontend')
@section('content')
    <section class="upload-product pt-60 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-10">
                    <form action="{{ route('user.product.upload') }}" class="upload-product-item-wrapper">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Select Category')</h6>
                            @if ($categories->isEmpty())
                                <div class="alert alert-info mb-4">
                                    @lang('No active category or subcategory is available yet. Please add them from the admin panel before uploading a product.')
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="form--label">@lang('Category')</label>
                                <select class="select form--control  category select2" name="category"
                                    required @disabled($categories->isEmpty())>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subcategories }}"
                                            value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Subcategory')</label>
                                <select name="subcategory" class="select form--control  select2" required @disabled($categories->isEmpty())>
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>
                            @if (gs('free_item'))
                                <div class="form-group d-flex">
                                    <div>
                                        <label class="form--label mb-0 me-2">@lang('Would you like to offer this product for free?')</label>
                                    </div>
                                    <div class="custom-switch-div">
                                        <div class="custom-switch">
                                            <input type="checkbox" name="is_free" id="is_free" value="1"
                                                class="custom-switch-input">
                                            <label class="custom-switch-label" for="is_free">
                                                <span class="custom-switch-inner"></span>
                                                <span class="custom-switch-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="btn btn--md  btn--base" @disabled($categories->isEmpty())>@lang('Next') <i class="las la-chevron-circle-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .custom-switch {
            position: relative;
            display: inline-block;
            width: 30px;
            height: 15px;
        }

        .custom-switch-input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .custom-switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 15px;
        }

        .custom-switch-switch {
            position: absolute;
            content: "";
            height: 11px;
            width: 11px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .custom-switch-input:checked+.custom-switch-label {
            background-color: #28a745;
        }

        .custom-switch-input:checked+.custom-switch-label .custom-switch-switch {
            transform: translateX(15px);
        }

        .custom-switch-div {
            padding-top: 6px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.select2').each(function(index, element) {
                $(element).select2();
            });

            if (!$('.category option:selected').length) {
                return;
            }
        
            $('.category').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = '';

                $.each(subcategories, function(index, subcategory) {
                    html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=subcategory]').html(html);
            });
        })(jQuery);
    </script>
@endpush
