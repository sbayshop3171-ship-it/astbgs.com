
    <div class="row gy-4">
        @forelse ($categories as $category)
            <div class="col-lg-4 col-sm-6">
                <a href="{{ route('products', ['category' => $category->slug]) }}" class="popular-category-item">
                    <div class="popular-category-item__title">
                        <h5> {{ __($category->name) }} </h5> <span data-bs-toggle="tooltip"
                            title="@lang('Total Items')">[{{ $category->products_count }}]</span>
                    </div>
                    <div class="popular-category-item__content">
                        <div class="popular-category-item__thumb">
                            <img src="{{ getImage(getFilePath('category') . '/' . $category->image_3, getFileSize('category')) }}"
                                alt="{{ __($category->name) }}" />
                        </div>
                        <div class="popular-category-item__thumb">
                            <img src="{{ getImage(getFilePath('category') . '/' . $category->image_2, getFileSize('category')) }}"
                                alt="{{ __($category->name) }}" />
                        </div>
                        <div class="popular-category-item__thumb">
                            <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                alt="{{ __($category->name) }}" />
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <h5 class="mb-2">@lang('No categories available right now')</h5>
                    <p class="mb-0 text-muted">@lang('Categories will appear here once they are added from the admin panel.')</p>
                </div>
            </div>
        @endforelse
    </div>
