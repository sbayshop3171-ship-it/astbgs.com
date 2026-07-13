@extends('Template::layouts.app')
@section('panel')
    @php
        $login = getContent('login.content', true);
    @endphp

    <section class="account">
        <div class="account-inner">
            <div class="container">
                <div class="row gy-4 flex-wrap-reverse align-items-center">
                    <div class="col-xl-7 col-lg-6 d-lg-block d-none">
                        <div class="account-thumb-wrapper">
                            <div class="account-left-top d-flex align-items-center justify-content-center">
                                <a class="logo" href="{{ route('home') }}">
                                    <img src="{{ siteLogo('dark') }}" alt="@lang('Image')">
                                </a>
                            </div>
                            <div class="text-center">
                                <h3 class="account-thumb-wrapper__title s-highlight" data-s-break="-3" data-s-length="3">{{ __($login?->data_values?->heading) }}</h3>
                            </div>
                            <div class="account-thumb">
                                <img src="{{ frontendImage('login', $login?->data_values?->image, '680x450') }}" alt="@lang('Image')">
                                <div class="design-qty flex-center">
                                    <div class="design-qty__content">
                                        <span class="design-qty__icon"> <img src="{{ frontendImage('login', $login?->data_values?->icon_image, '30x20') }}" alt="@lang('Image')"> </span>
                                        <span class="design-qty__number text--base">{{ __($login?->data_values?->icon_title) }}</span>
                                        <span class="design-qty__text">{{ __($login?->data_values?->icon_subtitle) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6">
                        <div class="account-form">
                            <div class="text-center mb--4">
                                <a class="logo d-block d-lg-none" href="{{ route('home') }}">
                                    <img src="{{ siteLogo() }}" alt="@lang('Image')">
                                </a>

                                <h5 class="account-form__title mb-2">{{ __($login?->data_values?->title) }}</h5>
                                <p>{{ __($login?->data_values?->subtitle) }}</p>
                            </div>

                            <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="username" class="form--label">@lang('Username or Email')</label>
                                            <input type="text" name="username" value="{{ old('username') }}" class="form--control " placeholder="@lang('Enter Username or Email')" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="d-flex flex-wrap justify-content-between">
                                                <label for="password" class="form--label">@lang('Password')</label>
                                                <a class="forgot-pass fs-14 text--base" href="{{ route('user.password.request') }}">
                                                    @lang('Forgot password?')
                                                </a>
                                            </div>
                                            <div class="position-relative">
                                                <input type="password" id="password" name="password" class="form-control form--control " placeholder="@lang('Enter Password')" required>
                                                <span class="password-show-hide fa-solid fa-eye toggle-password" id="toggle-password" aria-label="Toggle password visibility"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <x-captcha />
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group form--check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                @lang('Remember Me')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mt-2">
                                            <button class="btn btn--base btn--md w-100" id="recaptcha">@lang('Login')</button>
                                        </div>
                                    </div>

                                </div>
                            </form>

                            @include('Template::partials.social_login')

                            <div class="have-account mt-3">
                                <p class="have-account__text">@lang('Don\'t have any account?')
                                    <a href="{{ route('user.register') }}" class="have-account__link">@lang('Create Account')</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
