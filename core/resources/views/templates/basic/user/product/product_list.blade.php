@if ($products->count())
    @php
        $advertise728x90 = getAds('728x90');
        $hasAd728x90 = !empty($advertise728x90);
    @endphp
    <div class="row gy-4 product-grid-row">
        @foreach ($products as $index => $product)
            <div class="col-xl-4 col-sm-6 col-xsm-6">
                <x-product :product="$product" />
            </div>
            @if ($hasAd728x90 && ($index + 1) % 12 == 0)
                @php echo $advertise728x90; @endphp
            @endif
        @endforeach
    </div>
    <div class="pt-4">
        {{ paginateLinks($products) }}
    </div>
@else
    <div class="card custom--card">
        <div class="card-body">
            <x-empty-list title="No items found" />
        </div>
    </div>
@endif
