@if(auth()->check() && auth()->user()->isAuthor())
<div class="common-sidebar__item">
    <h6 class="common-sidebar__title">@lang('Upload a New Item')</h6>
    <div class="common-sidebar__content">
        <a href="{{ route('user.product.upload') }}" class="btn btn--base btn--md w-100">@lang('Upload Now')</a>
    </div>
</div>
@endif
