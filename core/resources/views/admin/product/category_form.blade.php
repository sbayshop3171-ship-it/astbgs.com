@extends('admin.layouts.app')

@section('panel')
    @push('topBar')
        @include('admin.product.categories_top_bar')
    @endpush
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($category) ? route('admin.category.store', ['id' => $category->id]) : route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input class="form-control" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required />
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Author Commission') <a class="las la-info-circle" title="@lang('Author get this commission, when user download this script.')"></a></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ __(gs('cur_sym')) }}</span>
                                        <input type="number" class="form-control" name="author_commission" value="{{ old('author_commission', getAmount($category->author_commission ?? '')) }}" step="any" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label d-block">@lang('Featured')</label>
                                    <input type="hidden" name="featured" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="featured" id="featured" value="1" @checked(old('featured', $category->featured ?? Status::YES) == Status::YES)>
                                        <label class="form-check-label" for="featured">
                                            @lang('Mark this category as featured')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Image')</label>
                                <x-image-uploader image="{{ $category->image ?? null }}" id="image" class="w-100" type="category" :required=false />
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Image Two')</label>
                                <x-image-uploader image="{{ $category->image_2 ?? null }}" id="image_2" name="image_2" class="w-100" type="category" :required=false />
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Image Three')</label>
                                <x-image-uploader image="{{ $category->image_3 ?? null }}" id="image_3" name="image_3" class="w-100" type="category" :required=false />
                            </div>
                        </div>

                        <div class="row">
                            <div class="my-4">
                                <h3 class="text-center pb-2 border-bottom">@lang('SEO Configuration')</h3>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>@lang('SEO Image')</label>
                                    <x-image-uploader class="w-100" :imagePath="getImage(getFilePath('seo') . '/' . (isset($category->seo_content->image) ? $category->seo_content->image : ''), getFileSize('seo'))" :size="getFileSize('seo')" :required="false" name="seo_image" />

                                </div>
                            </div>

                            <div class="col-xl-8 mt-xl-0 mt-4">
                                <div class="form-group">
                                    <label>@lang('Meta Keywords')</label>
                                    <small class="ms-2 mt-2  ">@lang('Separate multiple keywords by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                    <select name="keywords[]" class="form-control select2-auto-tokenize" multiple="multiple">
                                        @if (isset($category->seo_content->keywords))
                                            @foreach ($category->seo_content->keywords as $option)
                                                <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                 <div class="form-group">
                                    <label>@lang('Meta Robots') <small>(@lang('optional'))</small></label>
                                    <input type="text" class="form-control" name="meta_robots" value="{{ isset($category->seo_content->meta_robots) ? $category->seo_content->meta_robots : '' }}" placeholder="e.g. noindex, follow">
                                </div>

                                <div class="form-group">
                                    <label>@lang('Meta Description')</label>
                                    <textarea name="description" rows="3" class="form-control">{{ isset($category->seo_content->description) ? $category->seo_content->description : '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Social Title')</label>
                                    <input type="text" class="form-control" name="social_title" value="{{ isset($category->seo_content->social_title) ? $category->seo_content->social_title : '' }}" />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Social Description')</label>
                                    <textarea name="social_description" rows="3" class="form-control">{{ isset($category->seo_content->social_description) ? $category->seo_content->social_description : '' }}</textarea>
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
    <x-back route="{{ route('admin.category.index') }}" />
@endpush
