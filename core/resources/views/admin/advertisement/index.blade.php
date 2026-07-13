@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Value')</th>
                                    <th>@lang('Size')</th>
                                    <th>@lang('Redirect')</th>
                                    <th>@lang('Impression')</th>
                                    <th>@lang('Click')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advertisements as $advertisement)
                                    <tr>
                                        <td><span>{{ keyToTitle(__($advertisement->type)) }}</span></td>
                                        <td>
                                            @if ($advertisement?->type == 'image')
                                                <img src="{{ getImage(getFilePath('advertisement') . '/' . $advertisement->value) }}"
                                                    alt="" class="max-w-50">
                                            @else
                                                <span class="badge badge--primary">@lang('Script')</span>
                                            @endif
                                            {{ __($advertisement?->symbol) }}

                                        </td>
                                        <td>{{ $advertisement->size }}</td>
                                        <td>
                                            @if ($advertisement->redirect_url != 'N/A')
                                                <a target="_blank" class="text--info"
                                                    href="{{ $advertisement->redirect_url }}">
                                                    <i class="las la-external-link-alt"></i>
                                                </a>
                                            @else
                                                {{ __($advertisement->redirect_url) }}
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge badge--success"> {{ $advertisement?->impression }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">
                                                {{ $advertisement?->click }}
                                            </span>
                                        </td>

                                        <td> @php echo $advertisement->statusBadge @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('admin.advertisement.store', $advertisement->id) }}"
                                                    data-image="{{ getImage(getFilePath('advertisement') . '/' . $advertisement?->value ?? '') }}"
                                                    data-advertisement="{{ $advertisement }}">
                                                    <i class="la la-pen"></i>
                                                    @lang('Edit')
                                                </button>

                                                @if ($advertisement->status == Status::DISABLE)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.advertisement.status', $advertisement->id) }}"
                                                        data-question="@lang('Are you sure to enable this advertisement?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.advertisement.status', $advertisement->id) }}"
                                                        data-question="@lang('Are you sure to disable this advertisement?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
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
                @if ($advertisements->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($advertisements) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <div class="modal fade " id="advertizementModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Advertisement Type')</label>
                                    <select class="form-control" id="advertisementType" name="type" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        <option value="image">@lang('Image')</option>
                                        <option value="script">@lang('Script')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12" id="imageSize">
                                <div class="form-group">
                                    <div class="image-size">
                                        <label>@lang('Size')</label>
                                        <select class="form-control" name="size">
                                            <option value="" selected disabled>@lang('Select One')</option>
                                            <option value="728x90">@lang('728x90')</option>
                                            <option value="300x600">@lang('300X600')</option>
                                            <option value="300x250">@lang('300x250')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 d-none" id="advertizementImage">
                                <div class="form-group">
                                    <label> @lang('Image')</label>
                                    <x-image-uploader name="image" type="advertisement" :size="false" class="w-100"
                                        id="imageUpload" :required="false" />
                                </div>
                                <div class="form-group">
                                    <label class="required">@lang('Redirect Url') </label>
                                    <input type="text" class="form-control" name="redirect_url"
                                        placeholder="@lang('Redirect Url')">
                                </div>
                            </div>
                            <div class="col-lg-12 d-none" id="advertisementScript">
                                <div class="form-group">
                                    <label class="font-weight-bold required">@lang('Script')</label>
                                    <textarea name="script" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button type="button" class="btn btn-sm h-45 btn-outline--primary addAdvertisement"
        data-action="{{ route('admin.advertisement.store') }}">
        <i class="la la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let $modal = $('#advertizementModal');
            let backgroundImage = '';

            function resetModal(title, actionUrl, type = 'image', size = '728x90') {
                $modal.find('.modal-title').text(title);
                $modal.find('form')[0].reset();
                $modal.find('form').attr('action', actionUrl);
                $('#advertisementType').val(type);
                $('#imageSize select').val(size);
                toggleTypeSection(type);
                if (type === 'image') updateImagePreview(size);
            }

            function toggleTypeSection(type) {
                const $imageSection = $('#advertizementImage');
                const $scriptSection = $('#advertisementScript');

                if (type === 'image') {
                    $imageSection.removeClass('d-none').addClass('d-block');
                    $scriptSection.removeClass('d-block').addClass('d-none');
                } else {
                    $scriptSection.removeClass('d-none').addClass('d-block');
                    $imageSection.removeClass('d-block').addClass('d-none');
                }
            }

            function updateImagePreview(size) {
                let url = backgroundImage || `{{ route('placeholder.image', ':size') }}`.replace(':size', size);
                $('.image-upload-preview').css('background-image', `url(${url})`);
                $('.image-upload').show();
                $('#advertisement__image_size').text(`, Upload Image Size Must Be ${size} px`);
                $('#imageUpload').attr('size-validation', size);
            }

            $('.addAdvertisement').on('click', function() {
                backgroundImage = '';
                resetModal("@lang('Add Advertisement')", $(this).data('action'));
                $modal.modal('show');
            });

            $('#advertisementType').on('change', function() {
                let type = $(this).val();
                toggleTypeSection(type);
                if (type === 'image') {
                    let size = $('#imageSize select').val();
                    updateImagePreview(size);
                } else {
                    $('[name="script"]').val('');
                }
            });

            $('#imageSize select').on('change', function() {
                let type = $('#advertisementType').val();
                if (!type) {
                    alert("@lang('Please first select type')");
                    $(this).val('');
                    $('#advertisementType').focus();
                    return;
                }
                if (type === 'image') {
                    updateImagePreview($(this).val());
                }
            });

            $('.editBtn').on('click', function() {
                let data = $(this).data();
                backgroundImage = data.image;
                resetModal("@lang('Edit Advertisement')", data.action, data.advertisement.type, data.advertisement.size);
                if (data.advertisement.type === 'image') {
                    $modal.find('input[name="redirect_url"]').val(data.advertisement.redirect_url);
                    $modal.find('textarea[name="script"]').val('');
                } else {
                    $modal.find('textarea[name="script"]').val(data.advertisement.value);
                }

                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('script')
    <style>
        .max-w-50 {
            max-width: 50px !important;
        }
    </style>
@endpush
