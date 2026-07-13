@extends('admin.layouts.app')

@section('panel')
    @push('topBar')
        @include('admin.product.categories_top_bar')
    @endpush
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($subcategory) ? route('admin.subcategory.store', ['id' => $subcategory->id]) : route('admin.subcategory.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input class="form-control" name="name" type="text" value="{{ old('name', $subcategory->name ?? '') }}" required />
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="category_id" class="form-label">@lang('Category')</label>
                                    <select name="category_id" id="category_id" class="form-control select2" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $subcategory->category_id ?? null) == $category->id)>{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>@lang('SEO Image')</label>
                                    <x-image-uploader class="w-100" :imagePath="getImage(getFilePath('seo') . '/' . (isset($subcategory->seo_content->image) ? $subcategory->seo_content->image : ''), getFileSize('seo'))" :size="getFileSize('seo')" :required="false" name="image" />

                                </div>
                            </div>

                            <div class="col-xl-8 mt-xl-0 mt-4">
                                <div class="form-group">
                                    <label>@lang('Meta Keywords')</label>
                                    <small class="ms-2 mt-2  ">@lang('Separate multiple keywords by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                    <select name="keywords[]" class="form-control select2-auto-tokenize" multiple="multiple">
                                        @if (isset($subcategory->seo_content->keywords))
                                            @foreach ($subcategory->seo_content->keywords as $option)
                                                <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                 <div class="form-group">
                                    <label>@lang('Meta Robots') <small>(@lang('optional'))</small></label>
                                    <input type="text" class="form-control" name="meta_robots" value="{{ isset($subcategory->seo_content->meta_robots) ? $subcategory->seo_content->meta_robots : '' }}" placeholder="e.g. noindex, follow">
                                </div>

                                <div class="form-group">
                                    <label>@lang('Meta Description')</label>
                                    <textarea name="description" rows="3" class="form-control">{{ isset($subcategory->seo_content->description) ? $subcategory->seo_content->description : '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Social Title')</label>
                                    <input type="text" class="form-control" name="social_title" value="{{ isset($subcategory->seo_content->social_title) ? $subcategory->seo_content->social_title : '' }}" />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Social Description')</label>
                                    <textarea name="social_description" rows="3" class="form-control">{{ isset($subcategory->seo_content->social_description) ? $subcategory->seo_content->social_description : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.subcategory.index') }}" />
@endpush
