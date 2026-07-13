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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviewCategories as $reviewCategory)
                                    <tr>
                                        <td>{{ __($reviewCategory->name) }}</td>

                                        <td> @php echo $reviewCategory->statusBadge; @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('admin.review.category.update', $reviewCategory->id) }}"
                                                    data-name="{{ $reviewCategory->name }}"
                                                    data-description="{{ $reviewCategory->description }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>

                                                @if ($reviewCategory->status)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this category?')"
                                                        data-action="{{ route('admin.review.category.status', $reviewCategory->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this category?')"
                                                        data-action="{{ route('admin..status', $reviewCategory->id) }}">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @endif
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
            </div>
        </div>
    </div>


    {{-- Review CATEGORY  MODAL --}}
    <div id="reviewCategoryModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="form-label">@lang('Name')</label>
                            <input type="text" name="name" id="name" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let modal = $('#reviewCategoryModal');

            $('.addBtn').on('click', function() {
                modal.find('form')[0].reset();
                modal.find('.modal-title').text(`@lang('Add Review Category')`);
                modal.find('form').attr('action', `{{ route('admin.review.category.store') }}`);
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                let data = $(this).data();
                modal.find('.modal-title').text(`@lang('Update Review Category')`);
                modal.find('[name=name]').val(data.name);
                modal.find('form').attr('action', data.action);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
