@extends('admin.layouts.app')

@section('panel')
    @push('topBar')
        @include('admin.product.categories_top_bar')
    @endpush
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header flex-wrap gap-2 bg--primary d-flex justify-content-between">
                    <h5 class="text-white">{{ __($subcategory->name) }}</h5>
                    <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                </div>

                <form method="POST" action="{{ route('admin.subcategory.save.attributes', $subcategory->id) }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <x-generated-form :form=$form />
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-form-generator-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.subcategory.index') }}" />
@endpush
