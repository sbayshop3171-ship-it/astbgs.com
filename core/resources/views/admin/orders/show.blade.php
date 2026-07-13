@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ $order->order_number }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Item')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Option')</th>
                                    <th>@lang('Qty')</th>
                                    <th>@lang('Price')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ __($item->title) }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $item->delivery_type))) }}</td>
                                        <td>{{ $item->option_name ?: '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ showAmount($item->line_total) }}</td>
                                    </tr>
                                    @if(optional($item->detail)->request_note)
                                        <tr>
                                            <td colspan="5" class="small text-muted">
                                                <strong>@lang('Customer Note:')</strong> {{ $item->detail->request_note }}
                                                @if(!is_null(optional($item->detail)->requested_amount))
                                                    <br><strong>@lang('Requested Amount:')</strong> {{ showAmount($item->detail->requested_amount) }}
                                                @endif
                                                @if(optional($item->detail)->min_amount || optional($item->detail)->max_amount)
                                                    <br><strong>@lang('Allowed Range:')</strong>
                                                    {{ showAmount(optional($item->detail)->min_amount ?? 0) }} - {{ showAmount(optional($item->detail)->max_amount ?? 0) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif(!is_null(optional($item->detail)->requested_amount) || optional($item->detail)->min_amount || optional($item->detail)->max_amount)
                                        <tr>
                                            <td colspan="5" class="small text-muted">
                                                @if(!is_null(optional($item->detail)->requested_amount))
                                                    <strong>@lang('Requested Amount:')</strong> {{ showAmount($item->detail->requested_amount) }}<br>
                                                @endif
                                                @if(optional($item->detail)->min_amount || optional($item->detail)->max_amount)
                                                    <strong>@lang('Allowed Range:')</strong>
                                                    {{ showAmount(optional($item->detail)->min_amount ?? 0) }} - {{ showAmount(optional($item->detail)->max_amount ?? 0) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Order Summary')</h5>
                </div>
                <div class="card-body">
                    <p><strong>@lang('Customer:')</strong> {{ __($order->user?->fullname) }}</p>
                    <p><strong>@lang('Email:')</strong> {{ $order->user?->email }}</p>
                    <p><strong>@lang('Total:')</strong> {{ showAmount($order->total) }}</p>
                    <p><strong>@lang('Payment:')</strong> @php echo $order->paymentStatusBadge; @endphp</p>
                    <p><strong>@lang('Status:')</strong> @php echo $order->statusBadge; @endphp</p>
                    @if($order->deposit)
                        <p><strong>@lang('Gateway:')</strong> {{ $order->deposit->methodName() }}</p>
                        <p><strong>@lang('Transaction:')</strong> {{ $order->deposit->trx }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Update Order')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Status')</label>
                            <select name="status" class="form-control select2" data-minimum-results-for-search="-1" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ __(ucfirst(str_replace('_', ' ', $status))) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Internal Note')</label>
                            <textarea name="internal_note" class="form-control" rows="4">{{ old('internal_note', $order->internal_note) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Customer Visible Note')</label>
                            <textarea name="customer_note" class="form-control" rows="4">{{ old('customer_note', $order->customer_note) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn--primary w-100">@lang('Save')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
