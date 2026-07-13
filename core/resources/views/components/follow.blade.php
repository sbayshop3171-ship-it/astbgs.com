<div class="follow-content__item flex-between gap-4">
    <div class="author-info author-info--sm">
        <div class="author-info__thumb">
            <x-author-avatar :author="$profile" />
        </div>
        <div class="author-info__content">
            <a href="{{ route('user.profile', $profile->username) }}" class="d-block">
                <h6 class="author-info__name">{{ $profile->fullname ?? '' }}</h6>
            </a>
            <span class="author-info__date">
                @lang('Member since')
                {{ showDateTime($profile->created_at, 'M, Y') }}
            </span>
        </div>
    </div>
    <div class="follow-content__right flex-align">
        <div class="follow-statistics">
            <h6 class="fs-18 follow-statistics__number">{{ $profile->followers()->count() }}</h6>
            <span class="follow-statistics__text">@lang('Followers')</span>
        </div>
        <div class="follow-statistics">
            <h6 class="fs-18 follow-statistics__number">{{ $profile->follows()->count() }}</h6>
            <span class="follow-statistics__text">@lang('Following')</span>
        </div>
        @php  $downloads = $profile->total_download ?? 0; @endphp

        @if ($profile->is_author ?? false)
            <div class="follow-statistics">
                <h6 class="fs-18 follow-statistics__number">{{ $downloads }}</h6>
                <span class="follow-statistics__text">@lang(str()->plural('Download', $downloads))</span>
            </div>
        @endif


        @if (auth()->user() && auth()->id() !== $profile->id)
            <div class="follow-content__button">
                <form action="{{ route('user.author.follow', $profile->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn--base btn--sm">
                        @if (auth()->user()->follows->contains($profile->id))
                            @lang('Unfollow')
                        @else
                            @lang('Follow')
                        @endif
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
