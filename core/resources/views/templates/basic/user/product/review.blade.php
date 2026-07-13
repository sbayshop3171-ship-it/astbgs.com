@forelse ($reviews as $review)
    <div class="product-review">
        <div class="product-review__top flex-between">
            <div class="product-review__rating flex-align">
                <x-rating style="3" :value="$review?->rating" />
                <span class="product-review__reason">@lang('For')
                    <span class="product-review__subject"> {{ $review->category?->name }}</span>
                </span>
            </div>
            <div class="product-review__date">
                @lang('by')
                <a href="mailto:{{ $review->user?->email }}" class="product-review__user text--base">
                    {{ $review->user?->fullname }}
                </a>
                {{ diffForHumans($review->updated_at) }}
            </div>
        </div>

        <div class="product-review__body">
            @if ($review->is_reported)
                <div class="alert alert-light" role="alert">
                    @lang('This review is currently under review')
                </div>
            @else
                <p class="product-review__desc">
                    {{ $review?->review }}
                </p>
            @endif
        </div>

        @if (auth()->user() && $review->product->user_id === auth()->id())
            <div class="user-review__report mt-3 text-end">
                @if (!$review->is_reported)
                    <button type="button" class="btn btn--sm btn-outline--warning report-review-btn"
                        data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#reportModal"
                        data-bs-title="@lang('Report This Review')" data-review-id="{{ $review->id }}">
                        <i class="las la-flag me-0"></i> @lang('Report')
                    </button>
                @endif
            </div>
        @endif

        @if (!$review->is_reported)
            <div class="replies-container">
                @foreach ($review->replies as $reply)
                    @include('Template::user.product.review_reply')
                @endforeach
            </div>
        @endif

        @if (!$review->is_reported)
            @if (auth()->user() && $review->author_id == auth()->id())
                <div class="review-reply mt-0 border-0">
                    <div class="review-reply__thumb">
                        <x-author-avatar :author="$review->author" />
                    </div>
                    <div class="review-reply__content">
                        <div class="review-reply-form" data-product-id="{{ $product->id }}"
                            data-review-id="{{ $review->id }}">
                            @csrf
                            <textarea name="reply" class="form--control textarea--sm bg--white" required placeholder="@lang('Write Reply')"></textarea>
                            <div class="review-reply__button text-end">
                                <button type="button"
                                    class="btn btn--base btn--md submit-reply">@lang('Reply')</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
@empty
    <div class="card my-3 custom--card">
        <div class="card-body">
            <x-empty-list title="This product has no review" />
        </div>
    </div>
@endforelse
