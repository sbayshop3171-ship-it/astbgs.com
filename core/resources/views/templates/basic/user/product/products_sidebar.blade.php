<div class="product-sidebar">
    <button type="button" class="close-sidebar d-lg-none d-block"><i class="icon-Remove"></i></button>
    <div class="product-sidebar__item-wrappper">
        <!-- Search Filter -->
        <div class="product-sidebar__item mt-3 mt-lg-0">
            <div class="input-group">
                <input type="search" class="form--control form--control--sm form-control search-filter"
                    placeholder="@lang('Search by keyword...')" value="{{ request()->search }}" />
            </div>
        </div>

        @isset($categories)
        <!-- Category Filter -->
        <div class="product-sidebar__item">
            <h6 class="product-sidebar__title has-accordion">@lang('Category')</h6>
            <div class="product-sidebar__content">
                <ul class="text-list text-list--category">
                    <li class="text-list__item cursor-pointer {{ request()->category == 'all' ? 'active' : '' }}"
                        data-category="all">
                        <span class="text-list__link">@lang('All Categories')</span>
                    </li>
                    @foreach ($categories ?? [] as $category)
                        <li class="text-list__item cursor-pointer {{ request()->category == $category->slug ? 'active' : '' }}"
                            data-category="{{ $category->slug }}">
                            {{ __($category->name) }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endisset

        <!-- Rating Filter -->
        <div class="product-sidebar__item">
            <h6 class="product-sidebar__title has-accordion">@lang('Rating')</h6>
            <div class="product-sidebar__content">
                <ul class="text-list rating-filter">
                    <li class="text-list__item">
                        <div class="form--radio">
                            <input class="form-check-input" type="radio" name="rating" value="all" id="rating_all"
                                @checked(request()->rating == 'all') />
                            <label class="form-check-label" for="rating_all">
                                @lang('All Rating')
                            </label>
                        </div>
                    </li>

                    @foreach ($ratings as $rating)
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="rating"
                                    value="{{ $rating->value }}" id="star{{ $rating->value }}"
                                    @checked(request()->rating == $rating->value) />
                                <label class="form-check-label" for="star{{ $rating->value }}">
                                    {{ __($rating->name) }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="product-sidebar__item">
            <h6 class="product-sidebar__title has-accordion">@lang('Date')</h6>
            <div class="product-sidebar__content">
                <ul class="text-list date-filter">
                    <li class="text-list__item">
                        <div class="form--radio">
                            <input class="form-check-input" type="radio" name="date_range" id="anyDate"
                                value="all" @checked(request()->date_range == 'all') />
                            <label class="form-check-label" for="anyDate">@lang('All Date')</label>
                        </div>
                    </li>
                    <li class="text-list__item">
                        <div class="form--radio">
                            <input class="form-check-input" type="radio" name="date_range" id="year"
                                value="365" @checked(request()->date_range == 365) />
                            <label class="form-check-label" for="year"> @lang('Last Year') </label>
                        </div>
                    </li>
                    <li class="text-list__item">
                        <div class="form--radio">
                            <input class="form-check-input" type="radio" name="date_range" id="month"
                                value="30" @checked(request()->date_range == 30) />
                            <label class="form-check-label" for="month"> @lang('Last Month') </label>
                        </div>
                    </li>
                    <li class="text-list__item">
                        <div class="form--radio">
                            <input class="form-check-input" type="radio" name="date_range" id="week"
                                value="7" @checked(request()->date_range == 7) />
                            <label class="form-check-label" for="week"> @lang('Last Week') </label>
                        </div>
                    </li>
                    <li class="text-list__item">
                        <div class="form--radio">
                            <input class="form-check-input" type="radio" name="date_range" id="day"
                                value="1" @checked(request()->date_range == 1) />
                            <label class="form-check-label" for="day"> @lang('Last Day') </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if (!empty(($advertise300x250 = getAds('300x250'))))
        @php
            echo $advertise300x250;
        @endphp
    @endif
</div>
