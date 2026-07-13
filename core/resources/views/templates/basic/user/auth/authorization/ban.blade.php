@extends('Template::layouts.frontend')
@section('content')
    @php
        $ban = getContent('ban.content', true)->data_values ?? null;
    @endphp
    <div class="maintenance-page flex-column justify-content-center py-120">
        <div class="container">
            <div class="card ban-content custom--card">
                <div class="card-body">
                    <div class="ban-content__thumb">
                        <img src="{{ frontendImage('ban', $ban?->image, '220x220') }}" alt="@lang('image')"
                            class="img-fluid mx-auto mb-4">
                    </div>
                    <h4 class="ban-content__sub-title mb-4 text--danger"> {{ __($ban?->title) }}</h4>
                    <h6 class="ban-content__title mb-1">{{ __($ban?->heading) }}</h6>
                    <p class="text-center mx-auto mb-4">{{ __($ban?->subheading) }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
