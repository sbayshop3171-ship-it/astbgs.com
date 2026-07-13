
<div class="alert alert--{{ $type }} alert-dismissible fade show mb-3" role="alert">
    <div class="alert__icon">
        <i class="la la-{{ isset($icon) ? $icon : '' }}"></i>
    </div>
    <div class="alert__content">
        <h6 class="alert__title">{{ isset($title) ? __($title) : '' }}</h6>
        {{ $slot }}. <a href="{{ $route }}" class="text--base">@lang('View all')</a>
        <button type="button" class="alert__icon btn-close btn-close--{{ $type }}" data-bs-dismiss="alert" aria-label="Close">
            <i class="la la-times-circle"></i>
        </button>
    </div>
</div>
