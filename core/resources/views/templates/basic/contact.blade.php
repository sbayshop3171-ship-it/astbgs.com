@extends('Template::layouts.frontend')
@section('content')

    @php
        $contact = getContent('contact_us.content', true)->data_values ?? null;
    @endphp

    <div class="contact-inner pt-120 pb-60">
        <div class="container">
            <div class="row gx-4 gy-5 mb-5">
                <div class="col-md-4">
                    <div class="contact__info-box">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"
                                fill="none">
                                <g clip-path="url(#clip0_16221_2153)">
                                    <path
                                        d="M25.6623 12.5891C25.6983 8.35947 22.0959 4.48202 17.6319 3.94584C17.5432 3.93531 17.4464 3.91928 17.3441 3.90233C17.1231 3.86571 16.8945 3.82807 16.6639 3.82807C15.749 3.82807 15.5045 4.47074 15.4401 4.85403C15.3775 5.2267 15.4372 5.53966 15.6173 5.78459C15.9203 6.19658 16.4534 6.26972 16.8816 6.32832C17.0071 6.34565 17.1255 6.36177 17.2246 6.38403C21.2353 7.28021 22.5858 8.68919 23.2458 12.6658C23.2619 12.7629 23.2691 12.8827 23.2769 13.0097C23.3057 13.485 23.3657 14.474 24.4289 14.474H24.429C24.5175 14.474 24.6126 14.4663 24.7117 14.4512C25.7018 14.3008 25.6707 13.3968 25.6558 12.9624C25.6516 12.8401 25.6476 12.7244 25.658 12.6474C25.6608 12.628 25.6622 12.6086 25.6623 12.5891Z"
                                        fill="#072C18"></path>
                                    <path
                                        d="M16.4042 2.39329C16.523 2.40186 16.6354 2.41006 16.7291 2.42441C23.315 3.43724 26.3438 6.55761 27.1831 13.1951C27.1974 13.3079 27.1996 13.4455 27.202 13.5912C27.2103 14.1098 27.2276 15.1886 28.3861 15.2109L28.4221 15.2113C28.7854 15.2113 29.0744 15.1017 29.2812 14.8854C29.6418 14.5082 29.6167 13.9478 29.5964 13.4974C29.5914 13.3869 29.5867 13.2828 29.5879 13.1917C29.6716 6.40332 23.7956 0.24764 17.0132 0.0187267C16.9851 0.017795 16.9583 0.0192857 16.9311 0.0231056C16.9178 0.0250621 16.8931 0.0274845 16.8508 0.0274845C16.7832 0.0274845 16.7 0.0216149 16.6118 0.0156522C16.5051 0.00838509 16.3842 0 16.2615 0C15.1815 0 14.9761 0.767702 14.9498 1.22534C14.8893 2.28298 15.9124 2.35742 16.4042 2.39329Z"
                                        fill="#072C18"></path>
                                    <path
                                        d="M26.8654 21.7736C26.7253 21.6666 26.5805 21.5559 26.445 21.4467C25.7259 20.8681 24.9609 20.3347 24.2211 19.8188C24.0675 19.7119 23.914 19.6048 23.761 19.4975C22.8133 18.8318 21.9613 18.5083 21.1563 18.5083C20.0721 18.5083 19.1268 19.1074 18.3467 20.2887C18.001 20.8124 17.5817 21.067 17.0649 21.067C16.7593 21.067 16.412 20.9798 16.0329 20.8077C12.974 19.4205 10.7896 17.2937 9.54054 14.4863C8.93672 13.1294 9.13247 12.2424 10.1951 11.5206C10.7986 11.111 11.9218 10.3485 11.8423 8.88839C11.7522 7.23028 8.09365 2.24124 6.55219 1.6746C5.89955 1.4346 5.21383 1.43236 4.51042 1.66901C2.73837 2.26472 1.46672 3.3109 0.832622 4.69426C0.219858 6.03093 0.247528 7.60044 0.912653 9.23311C2.83545 13.9535 5.5388 18.0691 8.9479 21.4654C12.2848 24.79 16.3861 27.5133 21.1379 29.5593C21.5662 29.7436 22.0154 29.8441 22.3435 29.9175C22.4553 29.9426 22.5518 29.964 22.6221 29.9833C22.6608 29.9939 22.7007 29.9995 22.7406 29.9999L22.7782 30.0001C22.7782 30.0001 22.7782 30.0001 22.7784 30.0001C25.0133 30.0001 27.6968 27.9579 28.5209 25.6296C29.243 23.5909 27.9247 22.5832 26.8654 21.7736Z"
                                        fill="#00796B"></path>
                                    <path
                                        d="M17.3936 7.78547C17.011 7.79516 16.2147 7.81491 15.9352 8.62593C15.8045 9.00494 15.8202 9.33401 15.9818 9.60401C16.219 10.0002 16.6735 10.1216 17.0869 10.1882C18.586 10.4287 19.356 11.2577 19.51 12.7969C19.5815 13.5145 20.0648 14.0157 20.6853 14.0157C20.7311 14.0157 20.7781 14.013 20.8248 14.0073C21.571 13.9185 21.9328 13.3702 21.9001 12.3778C21.912 11.3421 21.37 10.1662 20.4481 9.22957C19.523 8.29006 18.4079 7.76096 17.3936 7.78547Z"
                                        fill="#072C18"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_16221_2153">
                                        <rect width="30" height="30" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="text">
                            <h5>@lang('Call Us')</h5>
                            <p>{{ __($contact?->phone_number) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact__info-box">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"
                                fill="none">
                                <g clip-path="url(#clip0_16221_2132)">
                                    <path
                                        d="M7.52152 15.9121H6.17615C5.82321 15.9121 5.50461 16.1222 5.36499 16.4458L1.64062 25.118L10.4855 20.6005C9.42673 19.1354 8.37914 17.5195 7.52152 15.9121Z"
                                        fill="hsl(var(--base))"></path>
                                    <path
                                        d="M24.6349 16.4458C24.4955 16.1222 24.1767 15.9121 23.824 15.9121H22.4786C20.859 18.9471 18.4995 22.0834 16.9985 23.809C15.9429 25.0198 14.0574 25.0184 13.0034 23.809C12.8956 23.6852 12.3327 23.0335 11.5575 22.0315L9.96289 22.8463L15.9948 28.8666L27.4243 22.9413L24.6349 16.4458Z"
                                        fill="hsl(var(--base))"></path>
                                    <path
                                        d="M0.553204 27.6524L0.0720947 28.7727C-0.176929 29.3525 0.249707 30 0.883023 30H13.8352C13.9162 29.9398 13.9172 29.9435 14.352 29.718L8.31116 23.6897L0.553204 27.6524Z"
                                        fill="hsl(var(--base))"></path>
                                    <path
                                        d="M29.9286 28.7727L28.1213 24.5645L17.6367 30H29.1177C29.7496 30 30.1781 29.3536 29.9286 28.7727Z"
                                        fill="hsl(var(--base))"></path>
                                    <path
                                        d="M15.8819 7.92915C15.8819 7.44347 15.4859 7.04865 14.9995 7.04865C14.5129 7.04865 14.1172 7.44347 14.1172 7.92915C14.1172 8.41461 14.5129 8.80966 14.9995 8.80966C15.4859 8.80966 15.8819 8.41461 15.8819 7.92915Z"
                                        fill="#00796B"></path>
                                    <path
                                        d="M15.6652 22.5948C15.9623 22.2537 22.9402 14.182 22.9402 8.63342C22.9402 -2.80792 7.05859 -2.94754 7.05859 8.63342C7.05859 14.182 14.0365 22.2537 14.3336 22.5948C14.6854 22.9983 15.3141 22.9978 15.6652 22.5948ZM12.3524 7.92915C12.3524 6.47255 13.5398 5.28763 14.9994 5.28763C16.4588 5.28763 17.6462 6.47255 17.6462 7.92915C17.6462 9.38553 16.4588 10.5704 14.9994 10.5704C13.5398 10.5704 12.3524 9.38553 12.3524 7.92915Z"
                                        fill="#00796B"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_16221_2132">
                                        <rect width="30" height="30" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="text">
                            <h5>@lang('Office Address')</h5>
                            <p>{{ __($contact?->location_address) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">

                    <div class="contact__info-box">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"
                                fill="none">
                                <path
                                    d="M24.6668 17.3332H15.3332C14.6 17.3332 14 17.9332 14 18.6664V26.6664C14 27.3996 14.6 28 15.3332 28H16.6664V30.6668L20.6664 28H24.6664C25.3996 28 26 27.3996 26 26.6668V18.6668C26 17.9336 25.3996 17.3332 24.6668 17.3332Z"
                                    fill="hsl(var(--base))"></path>
                                <path
                                    d="M13.8711 11.2056L15.2851 12.6196C17.8895 10.0156 22.1099 10.0156 24.7131 12.6196L26.1283 11.2056C22.7427 7.8204 17.2567 7.8204 13.8711 11.2056Z"
                                    fill="hsl(var(--base))"></path>
                                <path
                                    d="M33.3332 17.3332C33.3332 9.9688 27.366 4 20 4C12.636 4 6.6668 9.9688 6.6668 17.3332C5.194 17.3332 4 18.5272 4 20V28C4 29.4728 5.194 30.6668 6.6668 30.6668H10.6668C11.4032 30.6668 12 30.0704 12 29.3332V22.6668C12 21.9336 11.6672 20.8332 11.2604 20.224L9.3336 17.3336C9.3336 11.4416 14.1088 6.6668 20.0004 6.6668C25.8936 6.6668 30.6672 11.4416 30.6672 17.3336L28.74 20.224C28.3332 20.8332 28 21.9336 28 22.6668V29.706C26.0456 31.926 23.1904 33.3332 20 33.3332H17.3332V36H20C24.3592 36 28.216 33.8996 30.6484 30.6668H33.3332C34.806 30.6668 36 29.4728 36 28V20C36 18.5272 34.806 17.3332 33.3332 17.3332Z"
                                    fill="#00796B"></path>
                            </svg>
                        </div>
                        <div class="text">
                            <h5>@lang('Live Support')</h5>
                            <a href="mailto:{{ $contact?->email_address }}"
                                class="text--base">{{ __($contact?->email_address) }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-inner__wrapper">



                <div class="row gy-4 flex-wrap-reverse">
                    <div class="col-lg-5">
                        <div class="contact-inner__thumb">
                            <img src="https://img.freepik.com/premium-photo/beautiful-business-woman-is-talking-mobile-phone-while-sitting-modern-office_484651-2295.jpg?w=996"
                                alt="">
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-inner__heading text-center">
                            <h2 class="contact-inner__title">{{ __($contact?->heading) }}</h2>
                            <p class="contact-inner__desc">{{ __($contact?->subheading) }}</p>
                        </div>

                        <form class="verify-gcaptcha" method="POST">
                            @csrf
                            <div class="row gx-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form--label required" for="username">@lang('Name')</label>
                                       <input name="name" type="text" class="form-control form--control" value="{{ old('name',$user?->fullname) }}" @if($user && $user->profile_complete) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Email')</label>
                                        <input name="email" type="email" class="form-control form--control" value="{{ old('email',$user?->email) }}" @if($user) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label required" for="subject"> @lang('Subject')</label>
                                        <input name="subject" type="text" class="form-control form--control"
                                            value="{{ old('subject') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label required" for="message"> @lang('Write Messages')</label>
                                        <textarea name="message" wrap="off" class="form-control form--control form--control--sm" required>{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <x-captcha />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group mb-0">
                                        <button class="btn btn--base btn--md w-100"
                                            type="submit">@lang('Send Your Message')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif

@endsection
