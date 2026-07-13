@extends('Template::layouts.app')
@section('panel')
    @include('Template::partials.header')
    @if (
        !request()->routeIs('home') &&
            !request()->routeIs('user.authorization') &&
            !request()->routeIs('user.data') &&
            !request()->routeIs('user.password.*'))
        @include('Template::partials.breadcrumb')
    @endif

    @yield('content')

    @include('Template::partials.footer')

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <!--cookies start -->
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="cookies-card__desc">
                {{ $cookie?->data_values?->short_desc }} <a class="text--base" href="{{ route('cookie.policy') }}"
                    target="_blank">@lang('learn more')</a>
            </p>
            <div class="cookies-card__btn">
                <a href="javascript:void(0)" class="btn btn--base btn--sm policy">@lang('Allow')</a>
                <a href="javascript:void(0)" class="btn btn-outline--secondary btn--sm policy">@lang('Reject')</a>
            </div>
        </div>
        <!--cookies  end-->
    @endif
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });
            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);
        })(jQuery)
    </script>
@endpush
