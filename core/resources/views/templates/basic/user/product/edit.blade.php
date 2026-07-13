@extends('Template::layouts.frontend')
@section('content')
    <section class="upload-product pt-60 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 col-xl-10 mb-4">
                    <form method="POST" class="upload-product-item-wrapper" enctype="multipart/form-data"
                        action="{{ route('user.product.save', $product->id) }}">
                        @csrf
                        <input type="hidden" name="subcategory" value="{{ $product->subcategory_id }}">
                        <input type="hidden" value="{{ $product->category_id }}" name="category">

                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Title & Description')</h6>
                            <div class="form-group">
                                <label class="form--label text-dark">@lang('Title')</label>
                                <input type="text" class="form--control " name="title" value="{{ $product?->title }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="form--label text-dark">@lang('Description')</label>
                                <textarea class="form--control  nicEdit" id="description" name="description">@php echo($product?->description) @endphp</textarea>
                            </div>
                        </div>

                        <div class="upload-product-item">
                            @php $accept = '.png, .jpg, .jpeg' @endphp
                            <h6 class="upload-product-item__title">@lang('Files')</h6>
                            <div class="form-group">
                                <label class="form--label required">@lang('Thumbnail Image')</label>
                                <input type="file" class="form--control " name="thumbnail" accept="{{ $accept }}">
                                <span class="alert-message fs-14">@lang('Supported Files:') {{ $accept }}. @lang('Image will be resized into')
                                    <b>{{ getFileSize('productThumbnail') }}</b> @lang('px')</b></span>
                            </div>
                            <div class="form-group">
                                <label for="previewImage" class="form--label required">@lang('Preview Image')</label>
                                <input type="file" class="form--control " name="preview_image"
                                    accept="{{ $accept }}">
                                <span class="alert-message fs-14">@lang('Supported Files:') {{ $accept }}. @lang('Image will be resized into')
                                    <b>{{ getFileSize('productPreview') }}</b> @lang('px')</b></span>
                            </div>
                            <div class="form-group">
                                <label for="mainFile" class="form--label required">@lang('Main File')</label>
                                <input type="file" class="form--control " name="main_file" accept=".zip">
                                <span class="alert-message fs-14">@lang('ZIP all the files for buyers')</span>
                            </div>
                            <div class="form-group">
                                <label for="screenshots" class="form--label required">@lang('Screenshots')</label>
                                <input type="file" class="form--control " name="screenshots" accept=".zip" />
                                <span class="alert-message fs-14">@lang('Upload a zip file of screenshots')</span>
                            </div>
                        </div>

                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Product Attributes')</h6>
                            @if ($form)
                                <x-viser-form identifier="id" :identifierValue="$form->id" :isFrontend="true" :editData="$product->attribute_info" />
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="demo_url" class="form--label">@lang('Demo URL')</label>
                                        <input type="url" class="form--control " name="demo_url"
                                            value="{{ $product?->demo_url ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Tag & Support')</h6>
                            <div class="form-group select2-parent position-relative">
                                <label for="Category" class="form--label">@lang('Tags')</label>
                                <select name="tags[]" class="form--control form--control-md select2 select2-auto-tokenize"
                                    multiple="multiple" required>
                                    @foreach ($product->tags ?? [] as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="itemSupport" class="form--label">@lang('Item Will be Support?')</label>
                                <select id="itemSupport" class="select form--control select2"
                                    data-minimum-results-for-search="-1">
                                    <option value="yes">@lang('Yes')</option>
                                    <option value="no">@lang('No')</option>
                                </select>
                            </div>
                        </div>


                        @if (gs('changelog'))
                            <div class="upload-product-item">
                                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                    <div>
                                        <h6 class="upload-product-item__title mb-1">@lang('Changelog')</h6>
                                        <p class="form--label mb-0">@lang('Detail the changes for each version of your product')</p>
                                    </div>
                                    <button type="button" id="add-changelog" class="btn btn--sm btn-outline--base">
                                        <span class="d-inline-flex align-items-center gap-1 text-nowrap">
                                            <i class="fas fa-plus"></i>
                                            <span>@lang('Add New Changelog')</span>
                                        </span>
                                    </button>
                                </div>

                                <div id="changelog-container">
                                    @if (old('changelog'))
                                        @foreach (old('changelog') as $key => $change)
                                            <div class="changelog-item">
                                                <div class="form-group">
                                                    <label class="form--label text-dark">@lang('Changelog Heading')</label>
                                                    <input type="text" name="changelog[{{ $key }}][heading]"
                                                        class="form--control " value="{{ $change['heading'] }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form--label text-dark">@lang('Changelog Description')</label>
                                                    <textarea class="form--control  nicEdit" name="changelog[{{ $key }}][description]">{{ $change['description'] }}</textarea>
                                                </div>
                                                <button type="button"
                                                    class="remove-changelog btn--sm mb-2 btn btn--danger"> <i
                                                        class="lar la-trash-alt"></i> @lang('Remove')</button>
                                            </div>
                                        @endforeach
                                    @elseif(!empty($product->changelogs))
                                        @foreach ($product->changelogs as $key => $change)
                                            <div class="changelog-item">
                                                <div class="form-group">
                                                    <label class="form--label text-dark">@lang('Changelog Heading')</label>
                                                    <input type="text" name="changelog[{{ $key }}][heading]"
                                                        class="form--control " value="{{ $change->heading }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form--label text-dark">@lang('Changelog Description')</label>
                                                    <textarea class="form--control  nicEdit" name="changelog[{{ $key }}][description]">{{ $change->description }}</textarea>
                                                </div>
                                                <button type="button"
                                                    class="remove-changelog btn btn--sm mb-2 btn--danger"> <i
                                                        class="lar la-trash-alt"></i> @lang('Remove')</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        @endif

                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Message to the Reviewer')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Your Message')</label>
                                <textarea name="message" class="form--control ">{{ old('message', '') }}</textarea>
                            </div>

                            @if (!isset($product))
                                <div class="form-group">
                                    @php
                                        $uploadTerm = getContent('upload_term.content', true)?->data_values ?? null;
                                    @endphp

                                    <div class="form--check mt-2">
                                        <input class="form-check-input" type="checkbox" id="medium" required>
                                        <label class="form-check-label"
                                            for="medium">{{ __($uploadTerm?->details) }}</label>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group mb-0 text-end">
                                <button type="submit" class="btn btn--base btn--md w-100">@lang('Submit')</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xxl-4 col-xl-4">
                    <form action="{{ route('user.product.upload') }}" method="GET"
                        class="upload-product-item-wrapper">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Category & Subcategory')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Category')</label>
                                <input type="text" name="" id=""
                                    value="{{ $product?->category?->name }}" disabled class="form--control">
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Subcategory')</label>
                                <input type="text" name="" id=""
                                    value="{{ $product?->subcategory?->name }}" disabled class="form--control">
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

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}" />
@endpush

@push('style')
    <style>
        #changelog-container:has(.changelog-item) {
            margin-top: 24px
        }

        .changelog-item:not(:last-child) {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px dashed hsl(var(--black)/0.15);
        }

        .select2-container--default .select2-selection--multiple,
        .select2-dropdown {
            min-height: 45px;
        }

        .select2-container--default .select2-results>.select2-results__options {
            background: #fff !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.select2').each(function(index, element) {
                $(element).select2();
            })

            $('.select2-auto-tokenize').select2({
                dropdownParent: $('.select2-parent'),
                tags: true,
                tokenSeparators: [',']
            });

            $('.select2-predefined').each(function() {
                $(this).select2({
                    dropdownParent: $(this).closest('.select2-pre-parent'),
                    placeholder: 'Select',
                    allowClear: true,
                });
            });

            $('.category').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = '';

                $.each(subcategories, function(index, subcategory) {
                    html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=subcategory]').html(html);
            });

            let curSym = `{{ gs('cur_sym') }}`;

            $('[name=price], [name=price_cl]')
                .on('input', function() {
                    let price = $(this).val() * 1;
                    let sellerFee = $(this).closest('.priceGroup').data('seller_fee') * 1;
                    let totalPrice = price + sellerFee;
                    $(this).closest('.priceGroup').find('.totalPrice').text(curSym + totalPrice.toFixed(2));
                }).trigger('input');

            function initializeNicEditors() {
                $(".nicEdit").each(function(index) {
                    $(this).attr("id", "nicEditor" + index);
                    new nicEditor({
                        fullPanel: true
                    }).panelInstance('nicEditor' + index, {
                        hasPanel: true
                    });
                });
            }

            bkLib.onDomLoaded(initializeNicEditors);

            document.addEventListener('DOMContentLoaded', function() {
                let changelogIndex = document.querySelectorAll('.changelog-item').length;

                document.getElementById('add-changelog').addEventListener('click', function() {
                    changelogIndex++;
                    const changelogContainer = document.getElementById('changelog-container');
                    const newChangelog = document.createElement('div');
                    newChangelog.classList.add('changelog-item');

                    newChangelog.innerHTML = `
                        <div class="form-group">
                            <label class="form--label text-dark">@lang('Changelog Heading')</label>
                            <input type="text" name="changelog[${changelogIndex}][heading]" class="form--control ">
                        </div>
                        <div class="form-group">
                            <label class="form--label text-dark">@lang('Changelog Description')</label>
                            <textarea class="form--control  nicEdit" id="nicEditor${changelogIndex}" name="changelog[${changelogIndex}][description]"></textarea>
                        </div>
                        <button type="button" class="remove-changelog btn btn--sm btn--danger">
                            <span class="d-inline-flex align-items-center gap-1 text-nowrap"><i class="fa fa-times"></i>
                                <span><i class="lar la-trash-alt"></i> @lang('Remove')</span>
                            </span>
                        </button>
                    `;

                    changelogContainer.insertBefore(newChangelog, changelogContainer.firstChild);

                    new nicEditor({
                        fullPanel: true
                    }).panelInstance(`nicEditor${changelogIndex}`, {
                        hasPanel: true
                    });

                    newChangelog.querySelector('.remove-changelog').addEventListener('click',
                        function() {
                            changelogContainer.removeChild(newChangelog);
                        });
                });

                document.querySelectorAll('.remove-changelog').forEach(function(button) {
                    button.addEventListener('click', function() {
                        button.closest('.changelog-item').remove();
                    });
                });
            });

            $(document).on('mouseover', '.nicEdit-main,.nicEdit-panelContain', function() {
                $(this).focus();
            });

        })(jQuery);
    </script>
@endpush
