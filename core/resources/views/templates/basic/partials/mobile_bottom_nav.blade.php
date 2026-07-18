<nav class="mobile-bottom-nav" aria-label="@lang('Mobile navigation')">
    <a href="{{ route('home') }}" class="mobile-bottom-nav__link {{ menuActive('home') }}">
        <span class="mobile-bottom-nav__icon"><i class="las la-home"></i></span>
        <span class="mobile-bottom-nav__text">@lang('Home')</span>
    </a>
    <a href="{{ route('categories') }}" class="mobile-bottom-nav__link {{ menuActive('categories') }}">
        <span class="mobile-bottom-nav__icon"><i class="las la-th-large"></i></span>
        <span class="mobile-bottom-nav__text">@lang('Categories')</span>
    </a>
    <a href="{{ route('cart.index') }}" class="mobile-bottom-nav__link mobile-bottom-nav__link--cart {{ menuActive('cart.*') }}">
        <span class="mobile-bottom-nav__cart">
            <span class="mobile-bottom-nav__icon"><i class="las la-shopping-bag"></i></span>
            <span class="mobile-bottom-nav__badge" data-cart-count>{{ \App\Lib\CatalogCart::count() }}</span>
        </span>
        <span class="mobile-bottom-nav__text">@lang('Cart')</span>
    </a>
    <a href="{{ route('plans') }}" class="mobile-bottom-nav__link {{ menuActive('plans') }}">
        <span class="mobile-bottom-nav__icon"><i class="las la-layer-group"></i></span>
        <span class="mobile-bottom-nav__text">@lang('Plans')</span>
    </a>
    @auth
        <a href="{{ route('user.home') }}" class="mobile-bottom-nav__link {{ menuActive('user.home') }}">
            <span class="mobile-bottom-nav__icon"><i class="las la-user-circle"></i></span>
            <span class="mobile-bottom-nav__text">@lang('Account')</span>
        </a>
    @else
        <a href="{{ route('user.login') }}" class="mobile-bottom-nav__link {{ menuActive('user.login') }}">
            <span class="mobile-bottom-nav__icon"><i class="las la-user"></i></span>
            <span class="mobile-bottom-nav__text">@lang('Log In')</span>
        </a>
    @endauth
</nav>
