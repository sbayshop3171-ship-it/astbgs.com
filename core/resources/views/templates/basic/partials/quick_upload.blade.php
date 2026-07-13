<div class="common-sidebar__item">
    <h6 class="common-sidebar__title">@lang('Customer Shortcuts')</h6>
    <div class="common-sidebar__content">
        <a href="{{ route('cart.index') }}" class="btn btn--base btn--md w-100 mb-2">@lang('View Cart')</a>
        @auth
            <a href="{{ route('user.orders.index') }}" class="btn btn-outline--base btn--md w-100">@lang('My Orders')</a>
        @else
            <a href="{{ route('user.login') }}" class="btn btn-outline--base btn--md w-100">@lang('Login to Checkout')</a>
        @endauth
    </div>
</div>
