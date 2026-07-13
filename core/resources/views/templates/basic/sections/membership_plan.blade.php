<section class="pricing-area pt-120 pb-60" id="pricing">

    @php
        $plans = App\Models\Plan::active()->get();
        $content = getContent('membership_plan.content', true)?->data_values ?? null;
    @endphp

    <x-plan-card :plans="$plans" :content="$content" />

</section>
