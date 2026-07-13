@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Search')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control" placeholder="@lang('Order no / username / email')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Status')</label>
                                <select name="status" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    @foreach (['pending_payment', 'paid', 'processing', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected(request()->status === $status)>{{ __(ucfirst(str_replace('_', ' ', $status))) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Catalog Orders')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Items')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Payment')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $order->order_number }}</span><br>
                                            <small>{{ showDateTime($order->created_at) }}</small>
                                        </td>
                                        <td>
                                            {{ __($order->user?->fullname) }}<br>
                                            <small>@ {{ $order->user?->username }}</small>
                                        </td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td>{{ showAmount($order->total) }}</td>
                                        <td>@php echo $order->paymentStatusBadge; @endphp</td>
                                        <td>@php echo $order->statusBadge; @endphp</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline--primary btn-sm"><i class="las la-eye"></i> @lang('Details')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">@lang('No orders found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
