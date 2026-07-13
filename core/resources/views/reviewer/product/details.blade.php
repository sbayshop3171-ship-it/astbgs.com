@extends('reviewer.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="row gy-4">
                <div class="col-md-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex gap-2 justify-content-start align-items-center">
                                <h4 class="m-0">{{ __($product->title) }}</h4>
                                @php echo $product->statusBadge; @endphp
                            </div>
                            <div class="image-upload mt-3">
                                <img src="{{ getImage(getFilePath('productPreview') . productFilePath($product, 'preview_image')) }}" alt="@lang('Product Preview')" />
                            </div>

                            <div class="info mt-4">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                        <span>@lang('Project Files')</span>
                                        <span>
                                            @if ($product->file)
                                                <a href="{{ route('reviewer.product.download', $product->id) }}?time={{ time() }}">
                                                    <i class="las la-download"></i>
                                                    @lang('Download File')
                                                </a>
                                            @endif

                                            @if ($product->product_updated == Status::PRODUCT_UPDATE_PENDING || $product->status == Status::PRODUCT_PENDING)
                                                @php $hasFile = true; @endphp
                                                <a href="{{ route('reviewer.product.download.temp', $product->id) }}?time={{ time() }}">
                                                    <i class="las la-download"></i>
                                                    @lang('Download Updated File')
                                                </a>
                                            @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                        <span>@lang('Category')</span>
                                        <span>{{ $product?->category?->name }}</span>
                                    </li>
                                    @if ($product->is_free)
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                            <span>@lang('Price')</span>
                                            <span class="badge badge--success">@lang('Free')</span>
                                        </li>
                                    @endif
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                        <span>@lang('Update Status')</span>
                                        <?php echo $product->updateStatusBadge; ?>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                        <span>@lang('Demo Link')</span>
                                        @php $hasDemo = !empty($product->demo_url); @endphp
                                        <a href="{{ $hasDemo ? $product->demo_url : 'javascript:void(0)' }}" @if ($hasDemo) target="_blank" @endif>
                                            <i class="las la-external-link-alt"></i> @lang('Live Preview')
                                        </a>
                                    </li>
                                    @foreach ($product->attribute_info ?? [] as $info)
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                            <span>@lang($info->name)</span>
                                            @if (is_array($info->value))
                                                <div>
                                                    <span>{{ implode(', ', $info->value) }}</span>
                                                </div>
                                            @else
                                                <span>{{ $info?->value }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap px-0">
                                        <span>@lang('Tags')</span>
                                        <div>
                                            @if (isset($product->tags) && count($product->tags))
                                                <span>{{ implode(', ', $product->tags) }}</span>
                                            @else
                                                @lang('No Tags')
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card h-100 position-relative  @if ($activities->count() > 0) activity-wrapper @endif">
                        <div class="card-body d-flex flex-column p-0">
                            <h3 class="p-3 border-bottom">@lang('Activity Log')</h3>
                            <div id="activity-container" class="flex-grow-1 overflow-auto px-3 @if(!$activities->count()) empty @endif">
                                @if ($activities->count() > 0)
                                    <div id="activity-list">
                                        @include('reviewer.partials.activity', compact('activities'))
                                    </div>
                                @else
                                    <x-empty-list title="This product has no activities" />
                                @endif
                                <div id="loader" class="text-center py-3 d-none">
                                    <span class="spinner-border text--primary"></span>
                                </div>
                            </div>

                            {{-- Floating Reply Form --}}
                            @if ($activities->count() > 0)
                                <div class="border-top p-3 bg-white reviewer-msg-wrapper">
                                    <form id="reply-form" action="{{ route('reviewer.product.activities.reply', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label class="text--dark required">@lang('Message')</label>
                                            <textarea name="message" class="form-control" rows="3" placeholder="@lang('Reply...')" required></textarea>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- reject modal --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reason" class="form-label  reason-message">@lang('Reason for rejection')</label>
                            <textarea name="reason" id="reason" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@php
    $hiddenForever = in_array($product->status, [Status::PRODUCT_PERMANENT_DOWN]);
@endphp

@push('breadcrumb-plugins')
    <div class="d-flex gap-2 flex-wrap text-end">
        @if ($product->assigned_to == 0)
            <button data-action="{{ route('reviewer.product.assign', $product->slug) }}" class="btn btn-sm btn-outline--primary confirmationBtn" data-question="@lang('Are you sure to assign this product for review?')">
                <i class="las la-play"></i>@lang('Start Review')
            </button>
        @else
            @if (!$hiddenForever)
                @if ($product->status == Status::PRODUCT_APPROVED)
                    @if ($product->product_updated)
                        <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to approve the update?')" data-action="{{ route('reviewer.product.update.approve', ['id' => $product->id]) }}">
                            <i class="las la-check"></i>@lang('Update Approve')
                        </button>
                        <button class="btn btn-sm btn-outline--warning rejectBtn" data-question="@lang('Are you sure to soft reject this product')?" data-action="{{ route('reviewer.product.update.reject', ['id' => $product->id, 'type' => Status::PRODUCT_UPDATE_SOFT_REJECT]) }}"><i class="las la-times-circle"></i>
                            @lang('Update Soft Reject')
                        </button>
                        <button class="btn btn-sm btn-outline--danger rejectBtn" data-question="@lang('Are you sure to reject the update?')" data-action="{{ route('reviewer.product.update.reject', ['id' => $product->id, 'type' => Status::PRODUCT_UPDATE_HARD_REJECT]) }}">
                            <i class="las la-ban"></i>@lang('Update Hard Reject')
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline--warning rejectBtn" data-title="@lang('Are you sure to soft reject this product')?" data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_SOFT_REJECTED]) }}">
                            <i class="las la-times-circle"></i> @lang('Soft Reject')
                        </button>
                        <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to hard reject this product?')" data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_HARD_REJECTED]) }}" @disabled(Status::PRODUCT_HARD_REJECTED == $product->status)>
                            <i class="las la-ban"></i>@lang('Hard Reject')
                        </button>
                    @endif
                    <button class="btn btn-sm btn-outline--warning rejectBtn" data-reject-label="@lang('Disable Reason')" data-question="@lang('Are you sure to soft disable product?')" data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_DOWN]) }}">
                        <i class="las la-ban"></i>
                        @lang('Soft Disable')
                    </button>
                    <button data-reject-label="@lang('Disable Reason')" class="btn btn-sm btn-outline--danger rejectBtn" data-title="@lang('Are you sure to permanently disable this product')?" data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_PERMANENT_DOWN]) }}" @disabled(Status::PRODUCT_PERMANENT_DOWN == $product->status)>
                        <i class="las la-times"></i>
                        @lang('Permanent Disable')
                    </button>
                @else
                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to approve the product?')" data-action="{{ route('reviewer.product.approve', $product->id) }}">
                        <i class="las la-check"></i>@lang('Approve')
                    </button>
                    <button class="btn btn-sm btn-outline--warning rejectBtn" data-title="@lang('Are you sure to soft reject this product')?" data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_SOFT_REJECTED]) }}">
                        <i class="las la-times-circle"></i> @lang('Soft Reject')
                    </button>
                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to hard reject this product?')" data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_HARD_REJECTED]) }}" @disabled(Status::PRODUCT_HARD_REJECTED == $product->status)>
                        <i class="las la-ban"></i>@lang('Hard Reject')
                    </button>
                @endif
            @endif
        @endif
    </div>
@endpush

@push('style')
    <style>
        .product-response-info__date {
            font-size: .80rem;
        }

        .empty_list {
            margin: 50px 0px;
        }

        .activity-wrapper {
            min-height: 760px;
        }

        .reviewer-msg-wrapper {
            bottom: 0;
            z-index: 10;
            border-top: 1px solid #dee2e6;
            padding: 15px;
        }

        #activity-container {
            max-height: 525px;
        }

        #activity-container.empty{
            display:flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.reason-message').text($(this).data('reject-label') || "@lang('Reason for rejection')");
                modal.modal('show');
            });


            let page = 2;
            let isLoading = false;
            let hasMore = true;

            const container = document.getElementById('activity-container');
            const loader = document.getElementById('loader');
            const activityList = document.getElementById('activity-list');

            container.addEventListener('scroll', async () => {
                if (isLoading || !hasMore) return;

                if (container.scrollTop + container.clientHeight >= container.scrollHeight - 50) {
                    isLoading = true;
                    loader.classList.remove('d-none');

                    try {
                        const response = await fetch(
                            `{{ route('reviewer.product.activities.ajax', $product->id) }}?page=${page}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }

                        const result = await response.json();
                        if (!result.hasMore || result.html.trim() === '') {
                            hasMore = false;
                        } else {
                            activityList.insertAdjacentHTML('beforeend', result.html);
                            page++;
                        }

                    } catch (err) {
                        console.error('Failed to load more activities:', err);
                        hasMore = false; // stop infinite loop on error
                    }

                    loader.classList.add('d-none');
                    isLoading = false;
                }
            });
        })(jQuery);
    </script>
@endpush
