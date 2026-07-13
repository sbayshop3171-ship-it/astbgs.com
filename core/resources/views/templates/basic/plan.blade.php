@extends('Template::layouts.frontend')
@section('content')
    <section class="pricing-area pt-60 pb-60" id="pricing">
        <x-plan-card :plans="$plans" :content="$content" />
    </section>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
    
@endsection





