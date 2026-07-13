@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-md-8">
            <div class="mb-2 sort-by flex-align gap-2">
                <label class="sort-by__name" for="sortBy">@lang('Sort By')</label>
                <select name="sort_by" id="sort_by" class="form--control bg--white select2" data-minimum-results-for-search="-1">
                    <option @selected(@request()->sort_by == 'title') value="title">@lang('Title')</option>
                    <option @selected(@request()->sort_by == 'date') value="date">@lang('Date')</option>
                </select>
            </div>
            <div class="collected-product-item-wrapper ">
                <div class="collected-product-item-wrapper__inner">
                    @if ($collections->count())
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Name')</th>
                                            <th class="text-center">@lang('Total Item')</th>
                                            <th class="text-center">@lang('Is Public')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tobdy>
                                        @foreach ($collections as $collection)
                                            @php
                                                $totalProducts = $collection->products->count();
                                                $image = $collection->image ? getImage(getFilePath('productCollection') . '/' . $collection->image) : getImage('assets/images/default-collection.png');
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="table-product flex-align">
                                                        <div class="table-product__thumb">
                                                            <img src="{{ $image }}" alt="@lang('Collection image')" class="product_thumbnail" />

                                                        </div>
                                                        <div class="table-product__content">
                                                            <div class="table-product__name fw-bold">{{ __($collection->name) }}</div>
                                                            <small>{{ showDateTime($collection->created_at, 'd M, Y') }}</small>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="text-center">{{ $totalProducts }}</td>
                                                <td class="text-center">
                                                    @if ($collection->is_public)
                                                        <span class="badge badge--primary">@lang('Public')</span>
                                                    @else
                                                        <span class="badge badge--dark">@lang('Private')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                                                        <button type="button" class="btn btn-outline--base btn--sm collectionSettingBtn" data-name="{{ $collection->name }}" data-action="{{ route('user.author.collections.update', $collection->id) }}" data-is_public="{{ $collection->is_public }}">
                                                            <i class="las la-pencil-alt"></i> @lang('Edit')</button>

                                                        <button type="button" class="btn btn-outline--danger btn--sm confirmationBtn" data-action="{{ route('user.author.collections.delete', $collection->id) }}" data-question="@lang('Are you sure, you want to delete the collection?')"><i class="las la-trash-alt"></i> @lang('Delete')</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tobdy>
                                </table>
                            </div>
                        </div>

                        @if ($collections->hasPages())
                            <div class="py-4">
                                {{ paginateLinks($collections) }}
                            </div>
                        @endif
                    @else
                        <x-empty-list title="No collections found" />
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <button class="btn btn--base w-100 btn--md" data-bs-toggle="modal" data-bs-target="#collectionModal">
                <i class="la la-add"></i>
                @lang('New Collection')
            </button>

            @php
                $collectionDefinition = getContent('collection_definition.content', true)?->data_values;
            @endphp

            <div class="card mt-2">
                <div class="card-header bg-dark">
                    <h6 class="card-title p-0 m-0 text-white text-center">{{ __($collectionDefinition?->heading) }}</h6>
                </div>
                <div class="card-body">
                    <p>{{ __($collectionDefinition?->details) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div id="collectionModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Create New Collection')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.author.collections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="form--label">@lang('Collection Name')</label>
                            <input type="text" name="name" class="form--control " id="name" placeholder="@lang('Please write a meaningful name')" required />
                        </div>
                        <div class="form-group">
                            <label for="image" class="form-label">@lang('Image')</label>
                            <input type="file" name="image" id="image" class="form--control ">
                            <small id="emailHelp" class="form-text text-muted fs-12">@lang('The uploaded file must be '){{ getFileSize('productCollection') }}</small>
                        </div>
                        <div class="form-group d-flex collection-input-radio">
                            <div class="form--radio">
                                <input type="radio" class="form-check-input" name="is_public" id="private" value="0">
                                <label for="private" class="form-check-label custom-cursor-on-hover"><small>@lang('Keep it private')</small></label>
                            </div>

                            <div class="form--radio">
                                <input type="radio" class="form-check-input" name="is_public" id="public" value="1">
                                <label for="public" class="form-check-label custom-cursor-on-hover"><small>@lang('Keep it public')</small></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal frontend="true" />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            var modal = $('#collectionModal');
            modal.on('hidden.bs.modal', function() {
                modal.find('form').trigger('reset');
            });

            $('#sort_by').on('change', function() {
                let sortBy = $(this).val();
                let url = "{{ route('user.author.collections') }}";
                url += `?sort_by=${sortBy}`;
                window.location.href = url;
            });

            $('.collectionSettingBtn').on('click', function() {
                let data = $(this).data();
                modal.find('[name=name]').val(data.name);
                modal.find('form').attr('action', data.action);
                modal.find(`[value="${data.is_public}"]`).attr('checked', true);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .collected-product-item__right .icon {
            background-color: transparent !important;
        }

        .collected-product-item__right .icon:hover {
            background-color: transparent !important;
            color: hsl(var(--base)) !important;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
@endpush
