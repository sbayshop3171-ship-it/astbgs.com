@extends('Template::layouts.app')
@section('panel')
    @php
        $register = getContent('register.content', true);
        $policyPages = getContent('policy_pages.element', false, null, true);
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
                                <h3 class="account-thumb-wrapper__title s-highlight" data-s-break="-2" data-s-length="2">
                                    {{ __($register?->data_values?->heading) }}
                                </h3>
                            </div>
                            <div class="account-thumb">
                                <img src="{{ frontendImage('register', $register?->data_values?->image, '680x450') }}"
                                    alt="@lang('Image')">
                                <div class="design-qty flex-center">
                                    <div class="design-qty__content">
                                        <span class="design-qty__icon">
                                            <img src="{{ frontendImage('register', $register?->data_values?->icon_image, '30x20') }}" alt="@lang('Image')">
                                        </span>
                                        <span class="design-qty__number text--base">{{ __($register?->data_values?->icon_title) }}</span>
                                        <span class="design-qty__text">{{ __($register?->data_values?->icon_subtitle) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6">
                        <div class="account-form @if (!gs('registration')) form-disabled p-3 @endif">
                            @if (!gs('registration'))
                                <a class="form-disabled-text" data-bs-toggle="tooltip" href="{{ route('user.login') }}"
                                    title="@lang('We are unable to process the registration at this time.')">
                                    <svg style="enable-background:new 0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                        version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100"
                                        height="100" x="0" y="0" viewBox="0 0 512 512" xml:space="preserve">
                                        <g>
                                            <path data-original="#{{ gs('base_color') }}"
                                                d="M255.999 0c-79.044 0-143.352 64.308-143.352 143.353v70.193c0 4.78 3.879 8.656 8.659 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193c0-42.998 34.981-77.98 77.979-77.98s77.979 34.982 77.979 77.98v70.193c0 4.78 3.88 8.656 8.661 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193C399.352 64.308 335.044 0 255.999 0zM382.04 204.89h-30.748v-61.537c0-52.544-42.748-95.292-95.291-95.292s-95.291 42.748-95.291 95.292v61.537h-30.748v-61.537c0-69.499 56.54-126.04 126.038-126.04 69.499 0 126.04 56.541 126.04 126.04v61.537z"
                                                fill="#{{ gs('base_color') }}" opacity="1"></path>
                                            <path data-original="#{{ gs('base_color') }}"
                                                d="M410.63 204.89H101.371c-20.505 0-37.188 16.683-37.188 37.188v232.734c0 20.505 16.683 37.188 37.188 37.188H410.63c20.505 0 37.187-16.683 37.187-37.189V242.078c0-20.505-16.682-37.188-37.187-37.188zm19.875 269.921c0 10.96-8.916 19.876-19.875 19.876H101.371c-10.96 0-19.876-8.916-19.876-19.876V242.078c0-10.96 8.916-19.876 19.876-19.876H410.63c10.959 0 19.875 8.916 19.875 19.876v232.733z"
                                                fill="#{{ gs('base_color') }}" opacity="1">
                                            </path>
                                            <path data-original="#{{ gs('base_color') }}"
                                                d="M285.11 369.781c10.113-8.521 15.998-20.978 15.998-34.365 0-24.873-20.236-45.109-45.109-45.109-24.874 0-45.11 20.236-45.11 45.109 0 13.387 5.885 25.844 16 34.367l-9.731 46.362a8.66 8.66 0 0 0 8.472 10.436h60.738a8.654 8.654 0 0 0 8.47-10.434l-9.728-46.366zm-14.259-10.961a8.658 8.658 0 0 0-3.824 9.081l8.68 41.366h-39.415l8.682-41.363a8.655 8.655 0 0 0-3.824-9.081c-8.108-5.16-12.948-13.911-12.948-23.406 0-15.327 12.469-27.796 27.797-27.796 15.327 0 27.796 12.469 27.796 27.796.002 9.497-4.838 18.246-12.944 23.403z"
                                                fill="#{{ gs('base_color') }}" opacity="1"></path>
                                        </g>
                                    </svg>
                                </a>
                            @endif
                            <div class="text-center mb--4">
                                <a class="logo d-block d-lg-none" href="{{ route('home') }}">
                                    <img src="{{ siteLogo() }}" alt="@lang('Image')">
                                </a>

                                <h5 class="account-form__title mb-2">{{ __($register?->data_values?->title) }}</h5>
                                <p>{{ __($register?->data_values?->subtitle) }}</p>
                            </div>

                            <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
                                @csrf
                                <div class="row">
                                    @if (session()->get('reference'))
                                        <div class="col-12 mb-5">
                                            <div class="referral-info-box">
                                                <span class="referral-icon"><i class="la la-user-friends"></i></span>
                                                <span class="referral-text">
                                                    <strong>@lang('You were referred by:')</strong> {{ session()->get('reference') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('First Name')</label>
                                            <input type="text" class="form-control form--control " name="firstname" value="{{ old('firstname', $user?->firstname ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Last Name')</label>
                                            <input type="text" class="form-control form--control " name="lastname" value="{{ old('lastname', $user?->lastname ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label class="form--label" for="email">@lang('E-Mail Address')</label>
                                            <input type="email" class="form--control checkUser" id="email" name="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label" for="password">@lang('Password')</label>
                                            <div class="position-relative">
                                                <input type="password" id="password" class="form-control form--control  @if (gs('secure_password')) secure-password @endif" name="password" required>
                                                <span class="password-show-hide fa-solid fa-eye toggle-password"
                                                    id="toggle-password" aria-label="Toggle password visibility"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label" for="password_confirmation">@lang('Confirm Password')</label>
                                            <div class="position-relative">
                                                <input type="password" id="password_confirmation" class="form-control form--control "
                                                    name="password_confirmation" required>
                                                <span class="password-show-hide fa-solid fa-eye toggle-password"
                                                    id="toggle-confirm-password"
                                                    aria-label="Toggle confirm password visibility"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <x-captcha />
                                    </div>
                                    @if (gs('agree'))
                                        <div class="form-group form--checks">
                                            <input type="checkbox" id="agree" @checked(old('agree'))
                                                name="agree" class="form-check-input" required>
                                            <label for="agree" class="form-check-label"> @lang('I agree with')</label>
                                            @foreach ($policyPages as $policy)
                                                <a class="fw-500 forgot-pass fs-14"
                                                    href="{{ route('policy.pages', $policy->slug) }}"
                                                    target="_blank">{{ __($policy->data_values->title) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn--base btn--md w-100"
                                                id="recaptcha">@lang('Create Account')</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @include('Template::partials.social_login')

                            <div class="have-account mt-3">
                                <p class="have-account__text">@lang('Do you have an account?') <a href="{{ route('user.login') }}"
                                        class="have-account__link  fw-500">@lang('Login now')</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal custom--modal register fade custom--modal" id="existModalCenter">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-0 fs-16 fw-500">@lang('You already have an account. Please') <a href="{{ route('user.login') }}" class="">@lang('Login')</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm"
                        data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@if (gs('registration'))
    <style>
        .referral-info-box {
            display: flex;
            align-items: center;
            background-color: #eaf1f8;
            border-left: 5px solid hsl(var(--base));
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .referral-icon {
            font-size: 24px;
            color: hsl(var(--base));
            margin-right: 10px;
        }

        .referral-text {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 600;
        }
    </style>

    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";
            (function($) {


                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('user.checkUser') }}';
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data != false) {
                            $('#existModalCenter').modal('show');
                        }
                    });
                });

            })(jQuery);
        </script>
    @endpush
@else
    @push('style')
        <style>
            .account-form .form--control {
                backdrop-filter: blur(5px) !important;
                background-color: rgb(255 255 255 / 10%) !important;
            }

            .form-disabled {
                overflow: hidden;
                position: relative;
                border-radius: 25px;
            }

            .form-disabled::after {
                content: "";
                position: absolute;
                height: 100%;
                width: 100%;
                background-color: rgba(255, 255, 255, 0.2);
                top: 0;
                left: 0;
                backdrop-filter: blur(2px);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
                z-index: 99;
            }

            .form-disabled-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 991;
                font-size: 24px;
                height: auto;
                width: 100%;
                text-align: center;
                color: hsl(var(--dark-600));
                font-weight: 800;
                line-height: 1.2;
            }
        </style>
    @endpush

    @push('script')
        <script>
            "use strict";
            (function($) {
                @if (!gs('registration'))
                    notify('error', 'Registration is currently disabled!');
                @endif
            })(jQuery);
        </script>
    @endpush

@endif

{{-- @push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush --}}
