  @forelse ($comments as $comment)
      <div class="user-comment">

          <div class="author-reply">
              <div class="author-reply__thumb">
                  <x-author-avatar :author="$comment?->user" />
              </div>
              <div class="author-reply__content">
                  <div class="flex-between flex-nowrap">
                      <div>
                          <h6 class="author-reply__name">
                              <a href="{{ route('user.profile', $comment?->user->username) }}">{{ $comment?->user?->fullname }}</a>
                          </h6>
                          @if ($comment->user->downloadLog()->where('product_id', $product->id)->count())
                              <small>@lang('Downloaded')</small>
                          @endif
                      </div>
                      <span class="author-reply__time">{{ diffForHumans($comment->created_at) }}</span>
                  </div>
                  @if (!$comment->is_reported)
                      <p class="author-reply__desc mt-2">{{ $comment->text }}</p>
                  @endif
              </div>
          </div>

          @if ($comment->is_reported)
              <div class="custom-alert custom-alert--under-review">
                  <div class="custom-alert__icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="8" x2="12" y2="12"></line>
                          <line x1="12" y1="16" x2="12.01" y2="16"></line>
                      </svg>
                  </div>
                  <div class="custom-alert__content">
                      <span class="custom-alert__title">@lang('Under Review')</span>
                      <p class="custom-alert__message">@lang('This comment is currently being reviewed by our team')</p>
                  </div>
              </div>
          @endif

          {{-- Report Option for Author --}}
          @if (auth()->user() && $comment->product?->user_id === auth()->id())
              <div class="user-comment__report text-end">
                  @if (!isset($comment->is_reported) || (isset($comment->is_reported) && !$comment->is_reported))
                      <button type="button" class="btn btn--sm btn-outline--warning reportCommentBtn" data-action="{{ route('user.author.comments.report', $comment->id) }}">
                          <i class="las la-flag me-0"></i> @lang('Report')
                      </button>
                  @endif
              </div>
          @endif

          {{-- replies of the comment --}}
          @if (!$comment->is_reported)
              <div class="replies-container">
                  @foreach ($comment->replies ?? [] as $reply)
                      @include('Template::user.product.comment_reply')
                  @endforeach
              </div>

              @if (auth()->user() && ($comment->product?->user_id === auth()->id() || auth()->id() == $comment->user_id))
                  @if (isset($product->comment_disable) && $product->comment_disable == Status::NO)
                      <div class="author-reply">
                          <div class="author-reply__thumb">
                              <x-author-avatar :author="auth()->user()" />
                          </div>
                          <div class="review-reply__content">
                              <div class="comment-reply-form" data-product-id="{{ $product->id }}" data-comment-id="{{ $comment->id }}">
                                  @csrf
                                  <textarea name="text" class="form--control textarea--sm bg--white" placeholder="@lang('Write reply...')"></textarea>
                                  <div class="review-reply__button text-end">
                                      <button type="submit" class="btn btn--base btn--md submit-comment-reply">@lang('Reply')</button>
                                  </div>
                              </div>
                          </div>
                      </div>
                  @endif
              @endif
          @endif
      </div>
  @empty
      <div class="card mb-3 custom--card">
          <div class="card-body">
              <x-empty-list title="This product has no comments" />
          </div>
      </div>
  @endforelse
