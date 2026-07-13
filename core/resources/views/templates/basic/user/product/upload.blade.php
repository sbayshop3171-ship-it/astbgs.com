@extends('Template::layouts.frontend')

@section('content')
    <section class="upload-product pt-60 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 col-xl-10 mb-4">
                    <form method="POST" action="{{ route('user.product.save') }}" class="upload-product-item-wrapper"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ request()->category }}" name="category">
                        <input type="hidden" value="{{ request()->subcategory }}" name="subcategory">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Title & Description')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Title')</label>
                                <input type="text" class="form--control " name="title" value="{{ old('title') }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Description')</label>
                                <textarea class="form--control  nicEdit" name="description">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Files')</h6>
                            @php
                                $accept = '.png, .jpg, .jpeg';
                                $uploadTerm = getContent('upload_term.content', true)->data_values;
                            @endphp
                            <div class="form-group">
                                <label class="form--label">@lang('Thumbnail Image')</label>
                                <input type="file" class="form--control " name="thumbnail" accept="{{ $accept }}">
                                <span class="alert-message fs-14">@lang('Supported Files:') {{ $accept }}. @lang('Image will be resized into')
                                    <b>{{ getFileSize('productThumbnail') }}</b> @lang('px')</span>
                            </div>
                            <div class="form-group">
                                <label for="previewImage" class="form--label">@lang('Preview Image')</label>
                                <input type="file" class="form--control " name="preview_image"
                                    accept="{{ $accept }}">
                                <span class="alert-message fs-14">@lang('Supported Files:') {{ $accept }}.
                                    @lang('Image will be resized into')
                                    <b>{{ getFileSize('productPreview') }}</b> @lang('px')</span>
                            </div>
                            <div class="form-group">
                                <label for="mainFile" class="form--label">@lang('Main File')</label>
                                <input type="file" class="form--control " name="main_file" accept=".zip" required>
                                <span class="alert-message fs-14">@lang('ZIP all the files for buyers.')</span>
                            </div>
                            <div class="form-group">
                                <label for="screenshots" class="form--label">
                                    @lang('Screenshots')
                                    <a href="javascript:void(0)" class="common-sidebar__info ms-1" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-title="@lang('Upload a zip file by selecting images only. Please don\'t make any folder.')"><i
                                            class="icon-Info"></i></a>
                                </label>
                                <input type="file" class="form--control " name="screenshots" accept=".zip" />
                                <span class="alert-message fs-14">@lang('Upload a zip file of screenshots')</span>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Product Attributes')</h6>
                            <div class="row">
                                @if ($form)
                                    <x-viser-form identifier="id" :identifierValue="$form->id" />
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="demo_url" class="form--label">@lang('Demo Url')</label>
                                            <input type="url" id="demo_url" class="form--control " value="{{ old('demo_url') }}"
                                                name="demo_url">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Tag & Support')</h6>
                            <div class="form-group select2-parent position-relative">
                                <label for="tags" class="form--label">@lang('Tags')</label>
                                <small class="ms-2 mt-2  ">@lang('Separate multiple keywords by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                <select name="tags[]" class="form-control form--control select2 select2-auto-tokenize" id="tags"  multiple="multiple" required>
                                    @foreach (old('tags', []) as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($isFree)
                            <input type="hidden" name="is_free" value="1">
                        @endif

                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Message to the Reviewer')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Your Message')</label>
                                <textarea name="message" class="form--control ">{{ old('message', '') }}</textarea>
                            </div>
                            <div class="form-group">
                                <div class="form--check mt-2">
                                    <input class="form-check-input" type="checkbox" id="medium" required>
                                    <label class="form-check-label" for="medium">{{ __($uploadTerm->details) }}</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--base btn--md w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
                <div class="col-xxl-4 col-xl-4">
                    <form action="{{ route('user.product.upload') }}" method="GET"
                        class="upload-product-item-wrapper">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Switch Category')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Category')</label>
                                <select class="select form--control  category select2" name="category" required>
                                    <option value="" disabled>@lang('Select One')</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option data-subcategories="{{ $category->subcategories }}"
                                            value="{{ $category->id }}" @selected(request()->category == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Subcategory')</label>
                                <select name="subcategory" class="select form--control  select2" required>
                                    <option value="" disabled>@lang('Select One')</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn--md  btn--base"><i class="las la-sync"></i> @lang('Switch')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/nicEdit.js') }}"></script>
@endpush

@push('style')
    <style>
        .select2 .selection {
            display: block !important;
        }

        .select2-container--default .select2-selection--multiple,
        .select2-dropdown {
            min-height: 45px;
        }

        .select2-container--default .select2-results>.select2-results__options {
            background: #fff;
        }
    </style>
@endpush

@push('script')
    <script>
        bkLib.onDomLoaded(function() {
            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });
        });

        $('.select2').each(function(index, element) {
            $(element).select2();
        });

        (function($) {
            "use strict";
            $('.select2-auto-tokenize').select2({
                dropdownParent: $('.select2-parent'),
                tags: true,
                tokenSeparators: [',']
            });

            $('.category').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = '';

                $.each(subcategories, function(index, subcategory) {
                    html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=subcategory]').html(html);
            });

            $(document).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain', function() {
                $('.nicEdit-main').focus();
            });

            // let subCategoryId = JSON.parse('<?= json_encode(request()->subcategory ?? null) ?>');
            let subCategoryId = {!! json_encode(request()->subcategory) !!};

            let subcategories = $('[name=category]').find(':selected').data('subcategories');
            refreshSubCategories();

            $('.category').on('change', function() {
                subcategories = $(this).find(':selected').data('subcategories');
                refreshSubCategories();
            });


            function refreshSubCategories() {
                let html = '';
                $.each(subcategories, function(index, subcategory) {
                    html +=
                        `<option ${subCategoryId == subcategory.id ? 'selected' : ''} value="${subcategory.id}">${subcategory.name}</option>`;
                });
                $('[name=subcategory]').html(html);
            }

        })(jQuery);
    </script>
@endpush
