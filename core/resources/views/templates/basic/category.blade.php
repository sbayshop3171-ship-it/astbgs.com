@extends('Template::layouts.frontend')
@section('content')
    @php
        $categoryPageAd = getAds('728x90');
    @endphp

    <section class="category pt-120 pb-120">
        <div class="container{{ !empty($categoryPageAd) ? ' mb-5' : '' }}">
            @include('Template::partials.category_card', ['categories' => $categories])
        </div>

        @if (!empty($categoryPageAd))
            @php echo $categoryPageAd  @endphp
        @endif
    </section>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection
