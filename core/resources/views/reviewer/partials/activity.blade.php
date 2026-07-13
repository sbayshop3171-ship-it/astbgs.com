@foreach ($activities as $activity)
    @php
        $isAuthorReply = (bool) $activity->user_id;
        $actorName = $activity->user->fullname ?? $activity->reviewer?->name ?? __('Admin');
        $actorRole = $isAuthorReply ? __('Author') : ($activity->reviewer_id ? (auth('reviewer')->check() ? __('You') : __('Reviewer')) : __('Admin'));
    @endphp
    <div class="product-response__item activity mt-3">
        <div class="product-response-info mb-2">
            <div class="product-response-info__thumb">
                @if ($activity->user)
                    <x-author-avatar :author="$activity->user" />
                @elseif ($activity->reviewer)
                    <img src="{{ getImage(getFilePath('reviewerProfile') . '/' . $activity->reviewer->image, getFileSize('reviewerProfile')) }}"
                        alt="@lang('Reviewer Image')">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 w-100 bg--primary text-white fw-bold">
                        A
                    </div>
                @endif
            </div>
            <div class="product-response-info__content mb-2">
                <h6 class="product-response-info__name">
                    {{ $actorName }}
                    - [{{ $actorRole }}]
                </h6>
                <small class="product-response-info__date text-muted">
                    {{ showDateTime($activity->created_at, 'd M Y ') }}
                    @lang('at')
                    {{ showDateTime($activity->created_at, 'H:ma') }}
                </small>
                <div class="product-response-list">
                    <p>{{ $activity->message }}</p>
                </div>
            </div>
        </div>
    </div>
@endforeach
