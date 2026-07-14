@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ $product->exists ? route('admin.catalog.products.update', $product->id) : route('admin.catalog.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="alert alert--info mb-4">
                    <div class="small">
                        <strong>@lang('Download Product:')</strong> @lang('Upload files and publish to show it immediately on category pages.')
                        <br>
                        <strong>@lang('Order Product:')</strong> @lang('Add pricing options and request fields. Leave downloadable files empty for service-style orders.')
                    </div>
                </div>
                <div class="card mb-4 type-guide downloadable-guide">
                    <div class="card-body">
                        <h6 class="mb-3">@lang('Download Product Setup')</h6>
                        <div class="small text-muted mb-2">@lang('Best for files, bundles, guides, templates, scripts, archives, and instant digital delivery.')</div>
                        <ul class="mb-0 small">
                            <li>@lang('Choose category and subcategory first to load matching custom fields')</li>
                            <li>@lang('Upload thumbnail, preview image, screenshots zip, and at least one downloadable file')</li>
                            <li>@lang('Use product options only if you want multiple downloadable variants')</li>
                            <li>@lang('Keep Published checked if you want it visible on public category pages immediately')</li>
                        </ul>
                    </div>
                </div>
                <div class="card mb-4 type-guide order-guide d-none">
                    <div class="card-body">
                        <h6 class="mb-3">@lang('Order Product Setup')</h6>
                        <div class="small text-muted mb-2">@lang('Best for service requests, consultations, managed delivery, custom jobs, or pay-first order flows.')</div>
                        <ul class="mb-0 small">
                            <li>@lang('Add one or more selectable options so the buyer can choose from a dropdown')</li>
                            <li>@lang('Use Fixed Price for one exact amount, or Min-Max Range when the buyer must enter an allowed request amount')</li>
                            <li>@lang('Upload one or many product images. The first image will be used automatically as the cover image on cards and landing pages')</li>
                            <li>@lang('After the buyer selects an option, the flow can go directly to secure checkout')</li>
                        </ul>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
                        <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-outline--secondary btn-sm">@lang('Back')</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label">@lang('Title')</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $product->title) }}" required>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">@lang('Type')</label>
                                <select name="product_type" class="form-control catalog-product-type" required>
                                    <option value="downloadable" @selected(old('product_type', request('product_type', $product->product_type ?: 'downloadable')) === 'downloadable')>@lang('Download Product')</option>
                                    <option value="option_request" @selected(old('product_type', request('product_type', $product->product_type)) === 'option_request')>@lang('Order Product')</option>
                                </select>
                                <div class="small text-muted mt-1 catalog-type-hint">@lang('Switch type to show the matching upload and pricing sections below.')</div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label base-price-label">@lang('Base Price')</label>
                                <input type="number" step="any" min="0" name="base_price" class="form-control" value="{{ old('base_price', $product->base_price ?? 0) }}" required>
                                <div class="small text-muted mt-1 base-price-hint">@lang('Used for single-price products or as a fallback when no option is selected.')</div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">@lang('Category')</label>
                                <select name="category_id" class="form-control category-switch" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) old('category_id', $selectedCategory) === (string) $category->id)>{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 js-subcategory-wrapper">
                                <label class="form-label">@lang('Subcategory')</label>
                                <select name="subcategory_id" class="form-control subcategory-switch" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" @selected((string) old('subcategory_id', $selectedSubcategory) === (string) $subcategory->id)>{{ __($subcategory->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="small text-muted mt-1">@lang('Choosing a subcategory loads matching custom fields and admin recommendations without resetting the form.')</div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">@lang('Availability')</label>
                                <select name="availability_status" class="form-control" required>
                                    @foreach($availabilityOptions as $availabilityOption)
                                        <option value="{{ $availabilityOption }}" @selected(old('availability_status', $product->availability_status ?: 'available') === $availabilityOption)>{{ __(ucfirst($availabilityOption)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" @checked(old('is_published', $product->exists ? $product->is_published : true))>
                                    <label class="form-check-label" for="is_published">@lang('Published')</label>
                                    <div class="text-muted small mt-1">@lang('Uncheck করলে product save হবে, কিন্তু category page-এ দেখাবে না।')</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Description')</label>
                                <textarea name="description" class="form-control" rows="6" required>{{ old('description', $product->description) }}</textarea>
                            </div>
                            <div class="col-lg-4 downloadable-only">
                                <label class="form-label">@lang('Thumbnail')</label>
                                <input type="file" name="thumbnail" class="form-control" accept=".jpg,.jpeg,.png">
                            </div>
                            <div class="col-lg-4 downloadable-only">
                                <label class="form-label">@lang('Preview Image')</label>
                                <input type="file" name="preview_image" class="form-control" accept=".jpg,.jpeg,.png">
                                <div class="small text-muted mt-1">@lang('Main cover image for the landing page.')</div>
                            </div>
                            <div class="col-lg-4 downloadable-only">
                                <label class="form-label">@lang('Screenshots Zip')</label>
                                <input type="file" name="screenshots" class="form-control" accept=".zip">
                            </div>
                            <div class="col-lg-6 downloadable-only">
                                <label class="form-label">@lang('Demo URL')</label>
                                <input type="url" name="demo_url" class="form-control" value="{{ old('demo_url', $product->demo_url) }}">
                            </div>
                            <div class="col-lg-6 downloadable-only">
                                <label class="form-label">@lang('Tags')</label>
                                <select name="tags[]" class="form-control select2-tag" multiple="multiple">
                                    @foreach(old('tags', $product->tags ?? []) as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 order-only d-none">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('Product Images')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label class="form-label">@lang('Gallery Images')</label>
                                <input type="file" name="gallery_images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
                                <div class="small text-muted mt-1">@lang('Upload one or many images. The first image becomes the main cover automatically, and all uploaded images appear as the public slider/gallery.')</div>
                                @if($product->exists && count($product->screenshots()))
                                    <div class="small text-success mt-2">
                                        {{ count($product->screenshots()) }} @lang('gallery image(s) currently saved for this product')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div id="subcategory-fields-panel">
                    @if($form)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">@lang('Subcategory Fields')</h5>
                            </div>
                            <div class="card-body">
                                <x-viser-form identifier="id" :identifierValue="$form->id" :editData="$product->attribute_info ?? []" />
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">@lang('Product Options')</h5>
                            <div class="small text-muted">@lang('Order products should use options. Download products can leave this empty or use it for downloadable variants.')</div>
                        </div>
                        <button type="button" class="btn btn-outline--primary btn-sm" id="add-option-row">@lang('Add Option')</button>
                    </div>
                    <div class="card-body">
                        <div id="option-rows">
                            @php
                                $optionRows = old('options', $product->exists ? $product->options->map(function ($option) {
                                    $hasRange = $option->min_amount !== null || $option->max_amount !== null;
                                    return [
                                        'id' => $option->id,
                                        'name' => $option->name,
                                        'price' => $option->price,
                                        'pricing_mode' => $hasRange ? 'range' : 'fixed',
                                        'min_amount' => $option->min_amount,
                                        'max_amount' => $option->max_amount,
                                        'availability_note' => $option->availability_note,
                                        'sort_order' => $option->sort_order,
                                        'is_active' => $option->is_active,
                                    ];
                                })->toArray() : []);
                            @endphp
                            @forelse($optionRows as $index => $option)
                                <div class="border rounded p-3 mb-3 option-row" data-index="{{ $index }}">
                                    <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option['id'] ?? '' }}">
                                    <div class="row g-3">
                                        <div class="col-lg-3">
                                            <label class="form-label">@lang('Option Name')</label>
                                            <input type="text" name="options[{{ $index }}][name]" class="form-control option-name" value="{{ $option['name'] ?? '' }}">
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label">@lang('Fixed / Starting Price')</label>
                                            <input type="number" step="any" min="0" name="options[{{ $index }}][price]" class="form-control" value="{{ $option['price'] ?? 0 }}">
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label">@lang('Pricing Mode')</label>
                                            <select name="options[{{ $index }}][pricing_mode]" class="form-control option-pricing-mode" data-minimum-results-for-search="-1">
                                                <option value="fixed" @selected(($option['pricing_mode'] ?? 'fixed') === 'fixed')>@lang('Fixed Price')</option>
                                                <option value="range" @selected(($option['pricing_mode'] ?? 'fixed') === 'range')>@lang('Min-Max Range')</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label">@lang('Min Amount')</label>
                                            <input type="number" step="any" min="0" name="options[{{ $index }}][min_amount]" class="form-control option-min-amount" value="{{ $option['min_amount'] ?? '' }}">
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label">@lang('Max Amount')</label>
                                            <input type="number" step="any" min="0" name="options[{{ $index }}][max_amount]" class="form-control option-max-amount" value="{{ $option['max_amount'] ?? '' }}">
                                        </div>
                                        <div class="col-lg-1">
                                            <label class="form-label">@lang('Sort')</label>
                                            <input type="number" min="0" name="options[{{ $index }}][sort_order]" class="form-control" value="{{ $option['sort_order'] ?? $index }}">
                                        </div>
                                        <div class="col-lg-2 d-flex align-items-center gap-3">
                                            <div class="form-check mt-4">
                                                <input type="checkbox" class="form-check-input option-active" name="options[{{ $index }}][is_active]" value="1" @checked($option['is_active'] ?? true)>
                                                <label class="form-check-label">@lang('Active')</label>
                                            </div>
                                            <button type="button" class="btn btn-outline--danger btn-sm mt-4 remove-option-row">@lang('Remove')</button>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">@lang('Availability Note')</label>
                                            <textarea name="options[{{ $index }}][availability_note]" class="form-control" rows="2">{{ $option['availability_note'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted small" id="empty-options-text">@lang('No option added yet. Leave empty for single-price buy now products.')</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card mb-4 downloadable-only">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">@lang('Downloadable Files')</h5>
                        <button type="button" class="btn btn-outline--primary btn-sm" id="add-file-row">@lang('Add File')</button>
                    </div>
                    <div class="card-body">
                        <div id="file-rows">
                            @php
                                $fileRows = old('catalog_files', $product->exists ? $product->files->map(function ($file) {
                                    return [
                                        'id' => $file->id,
                                        'display_name' => $file->display_name,
                                        'option_reference' => $file->product_option_id ? 'existing:' . $file->product_option_id : '',
                                        'sort_order' => $file->sort_order,
                                        'is_active' => $file->is_active,
                                        'stored_name' => $file->stored_name,
                                    ];
                                })->toArray() : []);
                            @endphp
                            @forelse($fileRows as $index => $file)
                                <div class="border rounded p-3 mb-3 file-row" data-index="{{ $index }}">
                                    <input type="hidden" name="catalog_files[{{ $index }}][id]" value="{{ $file['id'] ?? '' }}">
                                    <div class="row g-3">
                                        <div class="col-lg-3">
                                            <label class="form-label">@lang('Display Name')</label>
                                            <input type="text" name="catalog_files[{{ $index }}][display_name]" class="form-control" value="{{ $file['display_name'] ?? '' }}">
                                            @if(!empty($file['stored_name']))
                                                <small class="text-muted">{{ $file['stored_name'] }}</small>
                                            @endif
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">@lang('Attach To Option')</label>
                                            <select name="catalog_files[{{ $index }}][option_reference]" class="form-control file-option-select" data-current="{{ $file['option_reference'] ?? '' }}">
                                                <option value="">@lang('Shared / No specific option')</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="form-label">@lang('Upload / Replace File')</label>
                                            <input type="file" name="catalog_files[{{ $index }}][file]" class="form-control">
                                        </div>
                                        <div class="col-lg-1">
                                            <label class="form-label">@lang('Sort')</label>
                                            <input type="number" min="0" name="catalog_files[{{ $index }}][sort_order]" class="form-control" value="{{ $file['sort_order'] ?? $index }}">
                                        </div>
                                        <div class="col-lg-2 d-flex align-items-center gap-3">
                                            <div class="form-check mt-4">
                                                <input type="checkbox" class="form-check-input" name="catalog_files[{{ $index }}][is_active]" value="1" @checked($file['is_active'] ?? true)>
                                                <label class="form-check-label">@lang('Active')</label>
                                            </div>
                                            <button type="button" class="btn btn-outline--danger btn-sm mt-4 remove-file-row">@lang('Remove')</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted small" id="empty-files-text">@lang('Add at least one file for downloadable products.')</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn--primary btn-lg">@lang('Save Product')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            const dependencyEndpoint = @json($product->exists ? route('admin.catalog.products.edit', $product->id) : route('admin.catalog.products.create'));
            let dependencyRequestSerial = 0;

            function initSelect2(scope) {
                const $scope = scope ? $(scope) : $(document);

                $scope.find('.select2').each(function() {
                    const $element = $(this);

                    if ($element.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $element.select2({
                        minimumResultsForSearch: $element.data('minimum-results-for-search') ?? 0
                    });
                });

                $scope.find('.select2-tag').each(function() {
                    const $element = $(this);

                    if ($element.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $element.select2({
                        tags: true,
                        tokenSeparators: [',']
                    });
                });
            }

            function buildDependencyUrl(categoryId, subcategoryId) {
                const url = new URL(dependencyEndpoint, window.location.origin);

                if (categoryId) {
                    url.searchParams.set('category_id', categoryId);
                }

                if (subcategoryId) {
                    url.searchParams.set('subcategory_id', subcategoryId);
                }

                return url;
            }

            function setDependencyLoading(isLoading) {
                $('.category-switch, .subcategory-switch').prop('disabled', isLoading);
                $('#subcategory-fields-panel').css({
                    opacity: isLoading ? '0.6' : '1',
                    pointerEvents: isLoading ? 'none' : 'auto'
                });
            }

            async function refreshDependencySections(categoryId, subcategoryId) {
                const requestId = ++dependencyRequestSerial;
                const dependencyUrl = buildDependencyUrl(categoryId, subcategoryId);

                setDependencyLoading(true);

                try {
                    const response = await fetch(dependencyUrl.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Dependency refresh failed');
                    }

                    const html = await response.text();

                    if (requestId !== dependencyRequestSerial) {
                        return;
                    }

                    const documentFragment = new DOMParser().parseFromString(html, 'text/html');
                    const nextSubcategoryWrapper = documentFragment.querySelector('.js-subcategory-wrapper');
                    const nextSubcategoryFields = documentFragment.querySelector('#subcategory-fields-panel');

                    if (nextSubcategoryWrapper) {
                        $('.js-subcategory-wrapper').replaceWith(nextSubcategoryWrapper.outerHTML);
                        initSelect2($('.js-subcategory-wrapper'));
                    }

                    if (nextSubcategoryFields) {
                        $('#subcategory-fields-panel').replaceWith(nextSubcategoryFields.outerHTML);
                        initSelect2($('#subcategory-fields-panel'));
                    }

                    window.history.replaceState({}, '', `${dependencyUrl.pathname}${dependencyUrl.search}`);
                } catch (error) {
                    window.location = dependencyUrl.toString();
                } finally {
                    if (requestId === dependencyRequestSerial) {
                        setDependencyLoading(false);
                    }
                }
            }

            initSelect2();

            let optionIndex = $('#option-rows .option-row').length;
            let fileIndex = $('#file-rows .file-row').length;

            function currentOptionChoices() {
                let options = [];
                $('#option-rows .option-row').each(function() {
                    let index = $(this).data('index');
                    let name = $(this).find('.option-name').val();
                    let existingId = $(this).find('input[name$="[id]"]').val();
                    if (!name) {
                        return;
                    }

                    options.push({
                        value: existingId ? `existing:${existingId}` : `new:${index}`,
                        text: name
                    });
                });
                return options;
            }

            function refreshFileOptionSelects() {
                const choices = currentOptionChoices();
                $('.file-option-select').each(function() {
                    const current = $(this).attr('data-current') || $(this).val();
                    let html = `<option value="">Shared / No specific option</option>`;
                    choices.forEach(choice => {
                        const selected = current === choice.value ? 'selected' : '';
                        html += `<option value="${choice.value}" ${selected}>${choice.text}</option>`;
                    });
                    $(this).html(html);
                });
            }

            function toggleDownloadableSection() {
                const isDownloadable = $('.catalog-product-type').val() === 'downloadable';
                $('.downloadable-only')[isDownloadable ? 'removeClass' : 'addClass']('d-none');
                $('.order-only').toggleClass('d-none', isDownloadable);
                $('.downloadable-guide').toggleClass('d-none', !isDownloadable);
                $('.order-guide').toggleClass('d-none', isDownloadable);
                $('.base-price-label').text(isDownloadable ? 'Base Price' : 'Base Price / Fallback Price');
                $('.base-price-hint').text(
                    isDownloadable
                        ? 'Used for single-price products or as a fallback when no option is selected.'
                        : 'Used only when you want a fallback amount. Most order products should price buyers through option rows below.'
                );
            }

            function toggleOptionPricingRow(row) {
                const pricingMode = row.find('.option-pricing-mode').val() || 'fixed';
                const isRange = pricingMode === 'range';
                const minField = row.find('.option-min-amount');
                const maxField = row.find('.option-max-amount');
                const minWrapper = minField.closest('.col-lg-2');
                const maxWrapper = maxField.closest('.col-lg-2');

                minWrapper.toggleClass('d-none', !isRange);
                maxWrapper.toggleClass('d-none', !isRange);
                minField.prop('disabled', !isRange).prop('required', isRange);
                maxField.prop('disabled', !isRange).prop('required', isRange);

                if (!isRange) {
                    minField.val('');
                    maxField.val('');
                }
            }

            function optionRow(index, data = {}) {
                return `
                    <div class="border rounded p-3 mb-3 option-row" data-index="${index}">
                        <input type="hidden" name="options[${index}][id]" value="${data.id || ''}">
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <label class="form-label">Option Name</label>
                                <input type="text" name="options[${index}][name]" class="form-control option-name" value="${data.name || ''}">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Fixed / Starting Price</label>
                                <input type="number" step="any" min="0" name="options[${index}][price]" class="form-control" value="${data.price || 0}">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Pricing Mode</label>
                                <select name="options[${index}][pricing_mode]" class="form-control option-pricing-mode">
                                    <option value="fixed" ${(data.pricing_mode || 'fixed') === 'fixed' ? 'selected' : ''}>Fixed Price</option>
                                    <option value="range" ${(data.pricing_mode || 'fixed') === 'range' ? 'selected' : ''}>Min-Max Range</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Min Amount</label>
                                <input type="number" step="any" min="0" name="options[${index}][min_amount]" class="form-control option-min-amount" value="${data.min_amount || ''}">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Max Amount</label>
                                <input type="number" step="any" min="0" name="options[${index}][max_amount]" class="form-control option-max-amount" value="${data.max_amount || ''}">
                            </div>
                            <div class="col-lg-1">
                                <label class="form-label">Sort</label>
                                <input type="number" min="0" name="options[${index}][sort_order]" class="form-control" value="${data.sort_order || index}">
                            </div>
                            <div class="col-lg-2 d-flex align-items-center gap-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input option-active" name="options[${index}][is_active]" value="1" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                                <button type="button" class="btn btn-outline--danger btn-sm mt-4 remove-option-row">Remove</button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Availability Note</label>
                                <textarea name="options[${index}][availability_note]" class="form-control" rows="2">${data.availability_note || ''}</textarea>
                            </div>
                        </div>
                    </div>
                `;
            }

            function fileRow(index, data = {}) {
                return `
                    <div class="border rounded p-3 mb-3 file-row" data-index="${index}">
                        <input type="hidden" name="catalog_files[${index}][id]" value="${data.id || ''}">
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <label class="form-label">Display Name</label>
                                <input type="text" name="catalog_files[${index}][display_name]" class="form-control" value="${data.display_name || ''}">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Attach To Option</label>
                                <select name="catalog_files[${index}][option_reference]" class="form-control file-option-select" data-current="${data.option_reference || ''}">
                                    <option value="">Shared / No specific option</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Upload / Replace File</label>
                                <input type="file" name="catalog_files[${index}][file]" class="form-control">
                            </div>
                            <div class="col-lg-1">
                                <label class="form-label">Sort</label>
                                <input type="number" min="0" name="catalog_files[${index}][sort_order]" class="form-control" value="${data.sort_order || index}">
                            </div>
                            <div class="col-lg-2 d-flex align-items-center gap-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" name="catalog_files[${index}][is_active]" value="1" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                                <button type="button" class="btn btn-outline--danger btn-sm mt-4 remove-file-row">Remove</button>
                            </div>
                        </div>
                    </div>
                `;
            }

            $(document).on('change keyup', '.option-name', refreshFileOptionSelects);
            $(document).on('change', '.option-pricing-mode', function() {
                toggleOptionPricingRow($(this).closest('.option-row'));
            });
            $('.catalog-product-type').on('change', toggleDownloadableSection);
            toggleDownloadableSection();
            refreshFileOptionSelects();
            $('#option-rows .option-row').each(function() {
                toggleOptionPricingRow($(this));
            });

            $(document).on('change', '.category-switch', function() {
                refreshDependencySections($(this).val(), '');
            });

            $(document).on('change', '.subcategory-switch', function() {
                refreshDependencySections($('.category-switch').val(), $(this).val());
            });

            $('#add-option-row').on('click', function() {
                $('#empty-options-text').remove();
                $('#option-rows').append(optionRow(optionIndex, {
                    pricing_mode: 'fixed'
                }));
                toggleOptionPricingRow($('#option-rows .option-row').last());
                optionIndex++;
                refreshFileOptionSelects();
            });

            $('#add-file-row').on('click', function() {
                $('#empty-files-text').remove();
                $('#file-rows').append(fileRow(fileIndex));
                fileIndex++;
                refreshFileOptionSelects();
            });

            $(document).on('click', '.remove-option-row', function() {
                $(this).closest('.option-row').remove();
                refreshFileOptionSelects();
            });

            $(document).on('click', '.remove-file-row', function() {
                $(this).closest('.file-row').remove();
            });
        })(jQuery);
    </script>
@endpush
