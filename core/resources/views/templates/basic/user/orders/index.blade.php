@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if($orders->count())
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Order')</th>
                                        <th>@lang('Items')</th>
                                        <th>@lang('Total')</th>
                                        <th>@lang('Payment')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $order->order_number }}</span><br>
                                                <small>{{ showDateTime($order->created_at) }}</small>
                                            </td>
                                            <td>{{ $order->items->count() }}</td>
                                            <td>{{ showAmount($order->total) }}</td>
                                            <td>@php echo $order->paymentSourceBadge; @endphp</td>
                                            <td>@php echo $order->statusBadge; @endphp</td>
                                            <td>
                                                <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-outline--base btn--sm">@lang('Details')</a>
                                                @if($order->status === \App\Constants\Status::CATALOG_ORDER_PENDING_PAYMENT)
                                                    <a href="{{ route('user.orders.pay', $order->id) }}" class="btn btn--base btn--sm">@lang('Pay')</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-empty-list title="No orders found" />
                    @endif
                </div>
                @if($orders->hasPages())
                    <div class="card-footer py-3">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
