@extends('admin.layouts.app')
@section('panel')
    @push('topBar')
        @include('admin.product.categories_top_bar')
    @endpush

    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $subcategory)
                                    <tr>
                                        <td>{{ __($subcategory->name) }}</td>
                                        <td>{{ __($subcategory->category?->name) }}</td>
                                        <td>@php echo $subcategory->statusBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.subcategory.form', $subcategory->id) }}" class="btn btn-sm btn-outline--primary"><i class="la la-pencil-alt"></i>@lang('Edit')</a>
                                                <button class="btn btn-outline--dark btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="las la-ellipsis-v"></i> @lang('More')
                                                </button>

                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.subcategory.attributes', $subcategory->id) }}">
                                                            <i class="la la-list"></i> @lang('Attributes')
                                                        </a>
                                                    </li>
                                                    @if ($subcategory->status == Status::DISABLE)
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.subcategory.status', $subcategory->id) }}" data-question="@lang('Are you sure to enable this subcategory?')">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.subcategory.status', $subcategory->id) }}" data-question="@lang('Are you sure to disable this subcategory?')">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($subcategories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subcategories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search..." />
    <a href="{{ route('admin.subcategory.form') }}" class="btn btn-outline--primary btn-sm"><i class="las la-plus"></i>@lang('Add New')</a>
@endpush
