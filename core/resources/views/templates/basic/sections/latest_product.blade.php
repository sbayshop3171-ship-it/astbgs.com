@php
    use App\Models\Category;
    use App\Models\Product;
    $latestProductContent = getContent('latest_product.content', true);
    $categories = Category::active()
        ->withWhereHas('products', function ($q) {
            $q->catalogPublished()->with('activeOptions')->orderByDesc('id')->take(8);
        })
        ->get();
    $latestProducts = Product::with(['author', 'activeOptions'])->catalogPublished()->orderByDesc('id')->limit(8)->get();
@endphp


<section class="latest-template pt-60 pb-60">
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
            <div class="section-heading text-start">
                <h4 class="section-heading__title s-highlight" data-s-break="2" data-s-length="2">{{ __($latestProductContent?->data_values->title) }}</h4>
                <p class="section-heading__desc">{{ __($latestProductContent?->data_values->subtitle) }}</p>
            </div>
            <div class="text-center view-all-btn mt-0">
                <a href="{{ route('products') }}?sort_by=new_item" class="btn btn--sm btn-outline--base btn--link">
                    @lang('View All Items')
                    <span class="icon"><i class="las la-arrow-right"></i></span>
                </a>
            </div>
        </div>

        <ul class="nav custom--tab nav-pills mb-3 justify-content-start" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-all-items-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-all-items" type="button" role="tab" aria-controls="pills-all-items"
                    aria-selected="true">
                    @lang('All Items')
                </button>
            </li>
            @foreach ($categories ?? [] as $category)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-{{ $category->id }}-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-{{ $category->id }}" type="button" role="tab"
                        aria-controls="pills-{{ $category->id }}" aria-selected="false">
                        {{ __($category->name) }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-all-items" role="tabpanel"
                aria-labelledby="pills-all-items-tab">
                <div class="row gy-4 home-product-grid">
                    @foreach ($latestProducts ?? [] as $product)
                        <div class="col-lg-3 col-sm-6 col-xsm-6">
                            <x-product :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>

            @foreach ($categories ?? [] as $category)
                <div class="tab-pane fade" id="pills-{{ $category->id }}" role="tabpanel"
                    aria-labelledby="pills-{{ $category->id }}-tab">
                    <div class="row gy-4 home-product-grid">
                        @foreach ($category->products ?? [] as $product)
                            <div class="col-lg-3 col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
