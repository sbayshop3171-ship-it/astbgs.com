<div class="header-top" id="header">
    <div class="container">
        <div class="top-header__wrapper flex-between">
            <a class="navbar-brand logo site-logo d-lg-block d-none" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            <div class="header-top__right flex-between gap-2">
                @if (gs('multi_language'))
                    <div class="language_switcher">
                        @php
                            $languages = App\Models\Language::get();
                            $defaultLang = $languages->firstWhere('is_default', Status::YES);
                            $currentLangCode = session('lang', config('app.locale'));
                            $currentLang = $languages->firstWhere('code', $currentLangCode) ?: $defaultLang;
                        @endphp
                        <div class="language_switcher__caption">
                            <span class="icon">
                                <img src="{{ getImage(getFilePath('language') . '/' . $currentLang->image, getFileSize('language')) }}" alt="@lang('image')">
                            </span>
                            <span class="text"> {{ __($currentLang->name ?? '') }} </span>
                        </div>
                        <div class="language_switcher__list">
                            @foreach ($languages as $item)
                                @if ($item->id != $currentLang->id)
                                    <div class="language_switcher__item    @if (session('lang') == $item->code) selected @endif" data-value="{{ $item->code }}">
                                        <a href="{{ route('lang', $item->code) }}" class="thumb">
                                            <span class="icon">
                                                <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}" alt="@lang('image')">
                                            </span>
                                            <span class="text"> {{ __($item->name) }}</span>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex-align gap-2">
                    <a href="{{ route('cart.index') }}" class="header-cart-link" aria-label="@lang('Shopping cart')" title="@lang('Shopping cart')">
                        <span class="header-cart-link__icon">
                            <i class="las la-shopping-bag"></i>
                        </span>
                        <span class="header-cart-link__badge" data-cart-count>{{ \App\Lib\CatalogCart::count() }}</span>
                    </a>

                    @guest
                        <ul class="top-menu-list flex-between">
                            <li class="top-menu-list__item">
                                <a href="{{ route('user.register') }}" class="top-menu-list__link"> @lang('Create Account') </a>
                            </li>
                            <li class="top-menu-list__item">
                                <a href="{{ route('user.login') }}" class="top-menu-list__link"> @lang('Login') </a>
                            </li>
                        </ul>
                    @else
                        @php
                            $user = auth()->user();
                        @endphp

                        @if ($user->status == Status::USER_ACTIVE)
                            <div class="profile-info">
                                <button type="button" class="profile-info__button flex-align">
                                    <span class="profile-info__icon">
                                        <img src="{{ asset('assets/images/user/' . $user->avatar ?? null) }}" alt="{{ $user->username }}'s avatar" class="profile-info__avatar">
                                    </span>
                                    <span class="profile-info__content">
                                        <span class="profile-info__name">{{ $user->username }} </span>
                                    </span>
                                    <span class="text-white px-2"><i class="las la-angle-down"></i></span>
                                </button>
                                <div class="profile-dropdown">
                                    <div class="profile-info style-two flex-align">
                                        <span class="profile-info__icon">
                                            <img src="{{ asset('assets/images/user/' . $user->avatar ?? null) }}" alt="{{ $user->fullname ?? '' }}'s avatar" class="profile-info__avatar">
                                        </span>
                                        <span class="profile-info__content">
                                            <span class="profile-info__name">{{ $user->fullname ?? '' }} </span>
                                            <span class="profile-info__text">{{ $user->email }}</span>
                                            <span class="profile-info__text">@lang('Wallet'): {{ showAmount($user->wallet_balance ?? 0) }}</span>
                                        </span>
                                    </div>

                                    <ul class="profile-dropdown-list">
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.home') }}" class="profile-dropdown-list__link {{ menuActive('user.home') }}">
                                                <span class="icon"><i class="la la-home"></i></span>
                                                @lang('Dashboard')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.profile.my') }}" class="profile-dropdown-list__link {{ menuActive('user.profile.my') }}">
                                                <span class="icon">
                                                    <i class="la la-user"></i>
                                                </span>
                                                @lang('Profile')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('cart.index') }}" class="profile-dropdown-list__link {{ menuActive('cart.index') }}">
                                                <span class="icon"><i class="la la-shopping-cart"></i></span>
                                                @lang('Cart')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.orders.index') }}" class="profile-dropdown-list__link {{ menuActive('user.orders.*') }}">
                                                <span class="icon"><i class="la la-receipt"></i></span>
                                                @lang('My Orders')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.transactions', ['balance_type' => \App\Constants\Status::BALANCE_TYPE_WALLET]) }}" class="profile-dropdown-list__link">
                                                <span class="icon"><i class="la la-wallet"></i></span>
                                                @lang('Wallet')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.deposit.index') }}" class="profile-dropdown-list__link {{ menuActive('user.deposit.*') }}">
                                                <span class="icon"><i class="la la-plus-circle"></i></span>
                                                @lang('Add Money')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.subscription.history') }}" class="profile-dropdown-list__link {{ menuActive('user.subscription.history') }} ">
                                                <span class="icon"> <i class="la la-bell"></i></span>@lang('Subscription History')</a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.author.download') }}" class="profile-dropdown-list__link {{ menuActive('user.author.download') }} ">
                                                <span class="icon"> <i class="la la-download"></i></span>@lang('Download History')</a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.author.free.download') }}" class="profile-dropdown-list__link {{ menuActive('user.author.free.download') }} ">
                                                <span class="icon"> <i class="la la-gift"></i></span>@lang('Free Items')</a>
                                        </li>
                                        @if (auth()->check() && auth()->user()->isAuthor())
                                            <li class="profile-dropdown-list__item">
                                                <a href="{{ route('user.withdraw.history') }}" class="profile-dropdown-list__link {{ menuActive('user.withdraw.*') }}">
                                                    <span class="icon"><i class="la la-bank"></i></span>
                                                    @lang('Withdraw History')
                                                </a>
                                            </li>
                                        @endif
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.transactions') }}" class="profile-dropdown-list__link {{ menuActive('user.transactions') }}">
                                                <span class="icon"><i class="la la-exchange-alt"></i></span>
                                                @lang('Transactions')
                                            </a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('ticket.index') }}" class="profile-dropdown-list__link {{ menuActive('ticket.*') }}">
                                                <span class="icon"><i class="la la-ticket"></i></span>
                                                @lang('Support Ticket')
                                            </a>
                                        </li>

                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.author.favorites') }}" class="profile-dropdown-list__link {{ menuActive('user.author.favorites') }}">
                                                <span class="icon"><i class="la la-heart-o"></i></span>@lang('Favorites')
                                            </a>
                                        </li>

                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.author.collections') }}" class="profile-dropdown-list__link {{ menuActive('user.author.collections') }}">
                                                <span class="icon"><i class="la la-copy"></i></span>@lang('Collections')
                                            </a>
                                        </li>

                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.profile.setting') }}" class="profile-dropdown-list__link {{ menuActive('user.profile.setting') }}">
                                                <span class="icon"> <i class="la la-gear"></i></span> @lang('System Settings')</a>
                                        </li>

                                        @if (auth()->check() && auth()->user()->isAuthor())
                                            <li class="profile-dropdown-list__item">
                                                <a href="{{ route('user.twofactor') }}" class="profile-dropdown-list__link {{ menuActive('user.twofactor') }}">
                                                    <span class="icon"> <i class="la la-fingerprint"></i></span>
                                                    @lang('2FA Security')</a>
                                            </li>
                                        @endif

                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.change.password') }}" class="profile-dropdown-list__link {{ menuActive('user.change.password') }}">
                                                <span class="icon"> <i class="la la-key"></i></span> @lang('Change Password')</a>
                                        </li>
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.logout') }}" class="profile-dropdown-list__link">
                                                <span class="icon"> <i class="la la-sign-out-alt"></i></span>
                                                @lang('Logout')</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
