@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Search')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control"
                                    placeholder="@lang('Title')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Type')</label>
                                <select name="product_type" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="downloadable" @selected(request()->product_type === 'downloadable')>@lang('Download Product')</option>
                                    <option value="option_request" @selected(request()->product_type === 'option_request')>@lang('Order Product')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Publish Status')</label>
                                <select name="status" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="published" @selected(request()->status === 'published')>@lang('Published')</option>
                                    <option value="draft" @selected(request()->status === 'draft')>@lang('Draft')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between flex-wrap gap-2">
                    <h5 class="mb-0">@lang('Admin Catalog')</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.catalog.products.create', ['product_type' => 'downloadable']) }}" class="btn btn-outline--primary btn-sm"><i class="las la-download"></i> @lang('Add Download Product')</a>
                        <a href="{{ route('admin.catalog.products.create', ['product_type' => 'option_request']) }}" class="btn btn--primary btn-sm"><i class="las la-plus"></i> @lang('Add Order Product')</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-4 pt-4 small text-muted">
                        @lang('Published products show on storefront categories immediately. Draft products stay hidden.')
                    </div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Publish')</th>
                                    <th>@lang('Availability')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb me-2">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . productFilePath($product, 'thumbnail')) }}" alt="@lang('Product Image')">
                                                </div>
                                                <div>
                                                    <span class="fw-bold">{{ __($product->title) }}</span><br>
                                                    <small>{{ showDateTime($product->created_at) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ __($product->category?->name) }}<br>
                                            <small>{{ __($product->subcategory?->name) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge--info">{{ __($product->productTypeLabel) }}</span>
                                        </td>
                                        <td>{{ $product->catalogPriceLabel }}</td>
                                        <td>
                                            @if ($product->is_published)
                                                <span class="badge badge--success">@lang('Published')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Draft')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge--secondary">{{ __(ucfirst($product->availability_status)) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.catalog.products.edit', $product->id) }}" class="btn btn-outline--primary btn-sm">
                                                <i class="las la-pen"></i> @lang('Edit')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">@lang('No catalog products found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($products->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($products) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
