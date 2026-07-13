<div class="col-lg-8">
    <div class="setting-content" data-bs-spy="scroll" data-bs-target="#sidebar-scroll-spy">
        @include('Template::user.settings.personal_information')
        @include('Template::user.settings.profile')
        @include('Template::user.settings.email_setting')
        @include('Template::user.settings.social_network')
    </div>
    <button type="submit" class="btn btn--base btn--md w-100">@lang('Submit')</button>
</div>
