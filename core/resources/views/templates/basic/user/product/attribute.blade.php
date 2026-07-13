@php $isOrderProduct = $product->isAdminOrderProduct(); @endphp

@if (!$isOrderProduct && $product->total_download > 0)
    <div class="common-sidebar__item d-none d-lg-block">
        <div class="common-sidebar__content text-center">
            <h5 class="mb-0"><i class="las la-download"></i> {{ $product->total_download }}
                {{ str()->plural('Download', $product->total_download) }} </h5>
        </div>
    </div>
@endif

<div class="common-sidebar__item mb-4">
    <h6 class="common-sidebar__title">@lang($isOrderProduct ? 'Order Information' : 'Product Information')</h6>
    <div class="common-sidebar__content">
        <ul class="product-info">
            @if ($product->managed_by_admin)
                <li class="product-info__item">
                    <span class="product-info__title">@lang('Type')</span>
                    <div class="product-info__content">
                        <span>{{ __($product->productTypeLabel) }}</span>
                    </div>
                </li>
            @endif
            <li class="product-info__item">
                <span class="product-info__title">@lang('Availability')</span>
                <div class="product-info__content">
                    <span>{{ __(ucfirst($product->availability_status ?? 'available')) }}</span>
                </div>
            </li>
            <li class="product-info__item">
                <span class="product-info__title">@lang('Last Updated')</span>
                <div class="product-info__content">
                    <span>{{ showDateTime($product->last_updated, 'd M Y') }}</span>
                </div>
            </li>
            <li class="product-info__item">
                <span class="product-info__title">@lang('Published')</span>
                <div class="product-info__content">
                    <span>{{ showDateTime($product->published_at, 'd M Y') }}</span>
                </div>
            </li>
            @foreach (($product->attribute_info ?? []) as $info)
                <li class="product-info__item">
                    <span class="product-info__title">{{ __($info->name) }}</span>

                    <div class="product-info__content">
                        @if (is_array($info->value))
                            @foreach ($info->value as $val)
                                <a href="{{ route('products', ['search' => $val]) }}">{{ __($val) }}</a>@if (!$loop->last), @endif
                            @endforeach
                        @else
                            <span>{{ __($info->value) }}</span>
                        @endif
                    </div>
                </li>
            @endforeach

            @if (count((array) ($product->tags ?? [])))
                <li class="product-info__item">
                    <span class="product-info__title">@lang('Tag')</span>
                    <div class="product-info__content">
                        @foreach (($product->tags ?? []) as $tag)
                            <a href="{{ route('products', ['search' => $tag]) }}">{{ __($tag) }}</a>@if(!$loop->last), @endif
                        @endforeach
                    </div>
                </li>
            @endif
        </ul>
    </div>
</div>
