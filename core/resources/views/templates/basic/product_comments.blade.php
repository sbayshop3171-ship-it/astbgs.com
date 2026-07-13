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

                    <div class="user-comment-wrapper">
                        @include('Template::user.product.comment', ['comments' => $comments])
                        @if ($comments->hasMorePages())
                            <div class="text-center my-2">
                                <button id="loadMoreComments" class="btn btn-sm btn--base"
                                    data-url="{{ route('get.product.comments', $product->slug) }}" data-page="2">
                                    @lang('Load More')
                                </button>
                            </div>
                        @endif
                    </div>
                    @if ($product->comment_disable == Status::ENABLE)
                        <div class="status-card status-card--disabled">
                            <div class="status-card__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    <line x1="9" y1="10" x2="15" y2="10"></line>
                                </svg>
                            </div>
                            <h4 class="status-card__title">@lang('Comments Disabled')</h4>
                            <p class="status-card__message">@lang('The author has temporarily disabled comments for this product')</p>
                        </div>
                    @else
                        @if (auth()->user() && $product?->user_id !== auth()->id())
                            <div class="user-comment mt-4">
                                <h6 class="user-comment__name">@lang('Add a comment')</h6>
                                <div class="author-reply">
                                    <div class="author-reply__thumb">
                                        <x-author-avatar :author="auth()->user()" />
                                    </div>
                                    <div class="review-reply__content">
                                        <form action="{{ route('user.author.comment.store', $product->id) }}"
                                            method="POST">
                                            @csrf
                                            <textarea name="text" class="form--control textarea--sm bg--white" placeholder="@lang('Leave a comment for the author...')"></textarea>
                                            <div class="review-reply__button text-end">
                                                <button type="submit"
                                                    class="btn btn--base btn--md">@lang('Post Comment')</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if ($hasAd728x90)
                      <div class="text-center mt-4">
                          @php echo $advertise728x90  @endphp
                      </div>
                    @endif

                </div>

                @include('Template::partials.common_sidebar')
            </div>
        </div>
    </section>

    <div id="reportModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Report Comment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="question mb-2">@lang('Please provide a reason for reporting this comment:')</p>
                        <textarea name="report_reason" class="form--control" rows="4" placeholder="@lang('Write your reason...')" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--md w-100">@lang('Submit Report')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.reportCommentBtn', function() {
                var modal = $('#reportModal');
                let data = $(this).data();
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });


            $(document).on('click', '#loadMoreComments', function() {
                let button = $(this);
                let page = button.data('page');
                let url = button.data('url');

                $.get(url + '?page=' + page, function(data) {
                    $('.user-comment-wrapper .user-comment:last').after(data.comments);

                    if (data.hasMorePages) {
                        button.data('page', page + 1);
                    } else {
                        button.remove();
                    }
                });
            });



            $(document).on('click', '.submit-comment-reply', function() {
                let form = $(this).closest('.comment-reply-form');
                let productId = form.data('product-id');
                let commentId = form.data('comment-id');
                let replyText = form.find('textarea[name="text"]').val();
                let csrfToken = form.find('input[name="_token"]').val();
                let parentId = form.find('input[name="parent_id"]').val();
                let repliesContainer = form.parents('.user-comment').find('.replies-container');

                let button = $(this);
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> @lang('Processing')');

                $.ajax({
                    url: "{{ route('user.author.comment.reply', ['productId' => ':productId', 'commentId' => ':commentId']) }}"
                        .replace(':productId', productId)
                        .replace(':commentId', commentId),
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        text: replyText
                    },
                    success: function(response) {
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
