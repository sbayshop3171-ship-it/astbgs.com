<div class="col-lg-4 ps-xxl-5">
    <div class="common-sidebar">
        @if ($product->user_id !== auth()->id())
            @include('Template::partials.download_bar')
        @endif

        @include('Template::partials.author_profile')
        @include('Template::user.product.attribute')
    </div>

    @php
        $advertise300x250 = getAds('300x250');
        $hasAd300x250 = !empty($advertise300x250);
    @endphp

    @if ($hasAd300x250)
        @php echo $advertise300x250 @endphp
    @endif

</div>
