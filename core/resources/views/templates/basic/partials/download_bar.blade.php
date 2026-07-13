<div class="common-sidebar__item">
    <div class="common-sidebar__button mb-3">
        <a href="{{ route('user.product.download', $product->slug) }}"
            class="btn btn--base w-100 download-button">
            <i class="fa fa-spinner d-none fa-spin"></i>
            <span class="text-box">
                <span class="icon">
                    <i class="las la-download"></i>
                </span>
                <span class="text">@lang('Download')</span>
            </span>
        </a>
    </div>
</div>
