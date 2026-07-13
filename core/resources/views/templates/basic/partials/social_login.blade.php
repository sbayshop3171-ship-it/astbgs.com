@php
    $credentials = gs('socialite_credentials');
    $text = isset($register) ? 'Or register with' : 'Or login with ';
@endphp
@if ($credentials->google->status == Status::ENABLE ||
        $credentials->facebook->status == Status::ENABLE ||
        $credentials->linkedin->status == Status::ENABLE)
    <div class="col-sm-12">
        <div class="mb-3">
            <div class="another-login text-center">
                <hr class="bar">
                <span class="another-login__text">@lang("$text")</span>
                <hr class="bar">
            </div>
        </div>
        <div class="">
            <ul class="social-login-list d-flex gap-3 flex-wrap justify-content-center">
                @if ($credentials->facebook->status == Status::ENABLE)
                    <li class="social-login-list__item facebook">
                        <a href="{{ route('user.social.login', 'facebook') }}" class="social-login-list__link">
                            <img src="{{ getImage($activeTemplateTrue . 'images/facebook.png') }}" alt="">
                        </a>
                    </li>
                @endif

                @if ($credentials->google->status == Status::ENABLE)
                    <li class="social-login-list__item google">
                        <a href="{{ route('user.social.login', 'google') }}" class="social-login-list__link">
                            <img src="{{ getImage($activeTemplateTrue . 'images/google.png') }}" alt="">
                        </a>
                    </li>
                @endif

                @if ($credentials->linkedin->status == Status::ENABLE)
                    <li class="social-login-list__item linkedin">
                        <a href="{{ route('user.social.login', 'linkedin') }}" class="social-login-list__link">
                            <img src="{{ getImage($activeTemplateTrue . 'images/linkedin.png') }}" alt="">
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif
