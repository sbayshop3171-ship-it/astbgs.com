@php

$featureCategory = getContent('featured_category.content', true);
$categories = App\Models\Category::active()
->withCount([
    'products' => function ($query) {
        $query->catalogPublished();
    },
    ])
    ->featured()
    ->orderByDesc('products_count')
    ->limit(6)
    ->get();
@endphp

<section class="category pt-60 pb-60">
    <div class="container">
        <div class="section-heading style-left flex-between gap-3">
            <div class="section-heading__inner">
                <h4 class="section-heading__title s-highlight" data-s-break="-1" data-s-length="1">{{ __($featureCategory?->data_values?->heading) }}</h4>
            </div>
            <a href="{{ route('categories') }}"
                class="btn btn-outline--base btn--sm  btn--link">@lang('View All Items') <span class="icon"><i
                        class="las la-arrow-right"></i></span></a>
        </div>
        @include('Template::partials.category_card', ['categories' => $categories])
    </div>
</section>
