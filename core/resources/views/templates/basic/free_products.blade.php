@extends('Template::layouts.frontend')
@section('content')
    @php
        $freeProduct = getContent('free_product.content', true);
    @endphp
    <section class="latest-template pt-60 pb-120">
        <div class="container">
            <div class="section-heading d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <h5 class="section-heading__title">{{ __($freeProduct?->data_values?->title) }}</h5>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--md" />
            </div>
            <ul class="nav custom--tab nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-all-items-tab" data-bs-toggle="pill" data-bs-target="#pills-all-items" type="button" role="tab" aria-controls="pills-all-items" aria-selected="true">@lang('All')</button>
                </li>
                @foreach ($categories as $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-{{ $category->id }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $category->id }}" type="button" role="tab" aria-controls="pills-{{ $category->id }}" aria-selected="false">{{ __($category->name) }}</button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-all-items" role="tabpanel" aria-labelledby="pills-all-items-tab" tabindex="0">
                    <div class="row gy-4">
                        @foreach ($products as $product)
                            <div class="col-lg-3 col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                        @endforeach
                    </div>
                    @if ($products->hasPages())
                        <div class="py-2">
                            {{ paginateLinks($products) }}
                        </div>
                    @endif
                </div>

                @foreach ($categories as $category)
                    <div class="tab-pane fade" id="pills-{{ $category->id }}" role="tabpanel" aria-labelledby="pills-{{ $category->id }}-tab" tabindex="0">
                        <div class="row gy-4">
                            @php
                                $categoryProducts = $products->where('category_id', $category->id)->sortByDesc('total_download')->take(8);
                            @endphp
                            @forelse ($categoryProducts as $product)
                                <div class="col-lg-3 col-sm-6 col-xsm-6">
                                    <x-product :product="$product" />
                                </div>
                            @empty
                                <x-empty-list title="No product found" />
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
