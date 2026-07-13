@extends('Template::layouts.frontend')

@section('content')
    <section class="product-details pt-60 pb-120">
        <div class="container">
            @include('Template::user.product.top')
            <div class="row gy-4">
                <div class="col-lg-8">
                    @include('Template::user.product.description')
                    @php
                        echo getAds('728x90');
                    @endphp
                </div>
                @include('Template::partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#showScreenshots').on('click', function(event) {
                event.preventDefault();

                $('#screenshotsGallery').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {
                        enabled: true
                    }
                }).magnificPopup('open');
            });

            $('.download-button').on('click', function() {
                setTimeout(() => {
                    $(this).not('[disabled]').addClass('disabled');
                }, 2000);
            });
        })(jQuery);
    </script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/magnific-popup.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.magnific-popup.min.js') }}"></script>
@endpush
