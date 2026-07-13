@extends('reviewer.layouts.master')
@section('content')
    <div class="page-wrapper default-version">
        @include('reviewer.partials.sidenav')
        @include('reviewer.partials.topnav')
        
        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">

                    @stack('topBar')
                    @include('partials.breadcrumb')

                    @yield('panel')

                </div><!-- bodywrapper__inner end -->
            </div><!-- body-wrapper end -->
        </div>

    </div>
@endsection
