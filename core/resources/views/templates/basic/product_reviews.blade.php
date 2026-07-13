@extends('Template::layouts.frontend')

@section('content')
    @php
        $author = $product->author;
        $advertise728x90 = getAds('728x90');
        $hasAd728x90 = !empty($advertise728x90);
    @endphp

    <section class="product-details pt-60 pb-120">
        <div class="container">
            @include('Template::user.product.top')

            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="user-review-wrapper">

                        @include('Template::user.product.review')

                        @if ($reviews->hasMorePages())
                            <div class="text-center my-2">
                                <button id="loadMoreReviews" class="btn btn-sm btn--base"
                                    data-url="{{ route('get.product.reviews', $product->slug) }}" data-page="2">
                                    @lang('Load More')
                                </button>
                            </div>
                        @endif
                    </div>

                    @if ($hasAd728x90)
                        <div class="text-center mt-4">
                            @php echo $advertise728x90  @endphp
                        </div>
                    @endif

                </div>
                @include('Template::partials.common_sidebar')
            </div>
        </div>
        {{-- REPORT MODAL --}}
        <div id="reportModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Report Review')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('user.author.reviews.report') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="review_id" value="">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">@lang('Reason')</label>
                                <textarea name="description" class="form-control form--control" value="{{ old('description') }}" placeholder="@lang('Describe your reason for reporting this review.')" required></textarea>
                            </div>

                            <button type="button" class="btn btn-outline--base btn--sm addAttachment my-2"> <i
                                    class="fas fa-plus"></i> @lang('Add Attachment') </button>
                            <small class="mb-2 text-muted d-block">
                                @lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')
                            </small>
                            <div class="row fileUploadsContainer"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--base w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {


            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border-0"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });

            $(document).ready(function() {
                $('.report-review-btn').on('click', function() {
                    const reviewId = $(this).data('review-id');

                    $('#reportModal').find('input[name="review_id"]').val(reviewId);

                });
            });


            $(document).on('click', '#loadMoreReviews', function() {
                let button = $(this);
                let page = button.data('page');
                let url = button.data('url');

                $.get(url + '?page=' + page, function(data) {
                    $('.user-review-wrapper .product-review:last').after(data.reviews);

                    if (data.hasMorePages) {
                        button.data('page', page + 1);
                    } else {
                        button.remove();
                    }
                });
            });


            $(document).on('click', '.submit-reply', function() {
                let form = $(this).closest('.review-reply-form');
                let productId = form.data('product-id');
                let reviewId = form.data('review-id');
                let replyText = form.find('textarea[name="reply"]').val();
                let csrfToken = form.find('input[name="_token"]').val();
                let repliesContainer = form.parents('.product-review').find('.replies-container');

                let button = $(this);
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> @lang('Processing')');

                $.ajax({
                    url: "{{ route('user.author.review.reply', ['productId' => ':productId', 'reviewId' => ':reviewId']) }}"
                        .replace(':productId', productId)
                        .replace(':reviewId', reviewId),
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        reply: replyText
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            repliesContainer.append(response.html);
                            form.find('textarea').val('');
                            notify('success', response.message);
                        } else {
                            notify('error', response.message);
                        }
                    },

                    complete: function() {
                        button.prop('disabled', false).html('@lang('Reply')');
                    }
                });
            });


        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .form-control:focus {
            box-shadow: none !important;
        }

        .removeFile {
            color: #fff !important;
        }
    </style>
@endpush
