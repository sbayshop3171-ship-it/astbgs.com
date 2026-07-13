@extends('Template::layouts.app')
@section('panel')
    @include('Template::partials.header')

    @include('Template::user.profile.profile_banner')
    
    <div class="profile-page py-60">
        <div class="container">
            @yield('content')
        </div>
    </div>

    @include('Template::partials.footer')
    
@endsection



@push('script')
    <script>
        (function($) {
            "use strict";
            $('.select2').each(function(index, element) {
                $(element).select2();
            });
        })(jQuery);
    </script>
@endpush
