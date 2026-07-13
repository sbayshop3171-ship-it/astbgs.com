@extends('admin.layouts.app')

@section('panel')
    @push('topBar')
        @include('admin.product.categories_top_bar')
    @endpush

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Author Commission')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('category') . '/' . $category->image ?? null) }}" alt="@lang('Category Image')" />
                                                </div>
                                                <span class="ms-2">{{ __($category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($category->author_commission) }}</td>
                                        <td>@php echo $category->statusBadge @endphp</td>
                                        <td>@php echo $category->featuredBadge @endphp</td>
                                        <td>
                                            <div class="button--group">

                                                <a href="{{ route('admin.category.form', $category->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-pencil-alt"></i>@lang('Edit')
                                                </a>

                                                <button class="btn btn-outline--dark btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="las la-ellipsis-v"></i> @lang('More')
                                                </button>
                                                <ul class="dropdown-menu px-2">
                                                    @if ($category->status == Status::DISABLE)
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.category.status', $category->id) }}" data-question="@lang('Are you sure to enable this category?')">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.category.status', $category->id) }}" data-question="@lang('Are you sure to disable this category?')">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if ($category->featured == Status::DISABLE)
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.category.feature.toggle', $category->id) }}" data-question="@lang('Are you sure to feature this category?')">
                                                                <i class="las la-check"></i> @lang('Mark as Featured')
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.category.feature.toggle', $category->id) }}" data-question="@lang('Are you sure to remove feature this category?')">
                                                                <i class="las la-times-circle"></i> @lang('Unfeature')
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Category Name" />
    <a href="{{ route('admin.category.form') }}" class="btn btn-outline--primary btn-sm"><i class="las la-plus"></i>@lang('Add New')</a>
@endpush
