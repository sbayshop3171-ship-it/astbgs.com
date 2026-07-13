@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-lg-8">
            <div class="card custom--card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0">{{ $order->order_number }}</h6>
                    <span>@php echo $order->statusBadge; @endphp</span>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <h6 class="mb-1">{{ __($item->title) }}</h6>
                                    <div class="small text-muted">{{ $item->option_name ?: ucfirst(str_replace('_', ' ', $item->delivery_type)) }}</div>
                                </div>
                                <div>{{ showAmount($item->line_total) }}</div>
                            </div>
                            @if(optional($item->detail)->request_note)
                                <div class="small text-muted mt-2"><strong>@lang('Request Note:')</strong> {{ $item->detail->request_note }}</div>
                            @endif
                            @if(!is_null(optional($item->detail)->requested_amount))
                                <div class="small text-muted mt-2"><strong>@lang('Requested Amount:')</strong> {{ showAmount($item->detail->requested_amount) }}</div>
                            @endif
                            @if(optional($item->detail)->min_amount || optional($item->detail)->max_amount)
                                <div class="small text-muted mt-2">
                                    <strong>@lang('Allowed Range:')</strong>
                                    {{ showAmount(optional($item->detail)->min_amount ?? 0) }} - {{ showAmount(optional($item->detail)->max_amount ?? 0) }}
                                </div>
                            @endif
                            @if($order->isPaid() && $item->delivery_type === \App\Constants\Status::PRODUCT_TYPE_DOWNLOADABLE)
                                @php
                                    $files = $item->product?->visibleFilesForOption($item->product_option_id)->get() ?? collect();
                                @endphp
                                @if($files->isNotEmpty())
                                    <div class="mt-3">
                                        <strong>@lang('Downloads')</strong>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach($files as $file)
                                                <a href="{{ route('user.downloads.file', [$item->id, $file->id]) }}" class="btn btn-outline--base btn--sm">
                                                    <i class="las la-download"></i> {{ __($file->display_name) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card custom--card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Summary')</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>@lang('Total:')</strong> {{ showAmount($order->total) }}</p>
                    <p class="mb-2"><strong>@lang('Created:')</strong> {{ showDateTime($order->created_at) }}</p>
                    @if($order->paid_at)
                        <p class="mb-2"><strong>@lang('Paid At:')</strong> {{ showDateTime($order->paid_at) }}</p>
                    @endif
                    @if($order->customer_note)
                        <p class="mb-0"><strong>@lang('Note:')</strong> {{ $order->customer_note }}</p>
                    @endif
                </div>
            </div>

            @if($order->status === \App\Constants\Status::CATALOG_ORDER_PENDING_PAYMENT)
                <a href="{{ route('user.orders.pay', $order->id) }}" class="btn btn--base w-100">@lang('Complete Payment')</a>
            @endif
        </div>
    </div>
@endsection
