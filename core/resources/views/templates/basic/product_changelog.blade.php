@extends('Template::layouts.frontend')

@section('content')
    @php
        $author = $product->author;
    @endphp

    <section class="product-details pt-60 pb-120">
        <div class="container">
            @include('Template::user.product.top')

            <div class="row gy-4">
                <div class="col-lg-8">
                    @if (!empty($product->changelogs))
                        @foreach ($product->changelogs->sortByDesc('id') as $key => $change)
                            <h6>{{ $change->heading }}</h6> <hr>
                            <p>@php echo $change->description @endphp</p> <hr>
                        @endforeach
                    @else
                        <div class="card mb-3 custom--card">
                            <div class="card-body">
                                <x-empty-list title="This product has no changelog" />
                            </div>
                        </div>
                    @endif
                </div>
                @include('Template::partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection
