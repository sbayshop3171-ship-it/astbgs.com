<div class="author-reply">
    <div class="author-reply__thumb">
        <x-author-avatar :author="$review?->author" />
    </div>
    <div class="author-reply__content">
        <div class="flex-between flex-nowrap">
            <div>
                <h6 class="author-reply__name">
                    <a href="{{ route('user.profile', $review->author?->username) }}" class="link">
                        {{ $review?->author?->fullname }}
                    </a>
                </h6>
                @if($reply->author_reply)
                    <span class="author-reply__response">@lang('Author response')</span>
                @endif
            </div>
            <span class="author-reply__time">{{ diffForHumans($reply->created_at) }}</span>
        </div>
        <p class="author-reply__desc">{{ $reply->text }}</p>
    </div>
</div>