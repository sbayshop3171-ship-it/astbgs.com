@extends('Template::layouts.frontend')
@section('content')
    <section class="py-60">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="mb-0">@lang('Checkout')</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('cart.checkout.submit') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">@lang('Customer Note')</label>
                                    <textarea name="customer_note" class="form-control" rows="5" placeholder="@lang('Optional note for the admin')">{{ old('customer_note') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn--base w-100">@lang('Create Order')</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="mb-0">@lang('Order Summary')</h5>
                        </div>
                        <div class="card-body">
                            @foreach($items as $item)
                                <div class="d-flex justify-content-between gap-3 mb-3">
                                    <div>
                                        <div>{{ __($item['title']) }}</div>
                                        <small class="text-muted">{{ $item['option_name'] ?: ucfirst(str_replace('_', ' ', $item['product_type'])) }} x {{ $item['quantity'] }}</small>
                                        @if(!is_null($item['requested_amount']))
                                            <div class="small text-muted">@lang('Requested Amount'): {{ showAmount($item['requested_amount']) }}</div>
                                        @endif
                                    </div>
                                    <div>{{ showAmount($item['unit_price'] * $item['quantity']) }}</div>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>@lang('Subtotal')</strong>
                                <strong>{{ showAmount($subtotal) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
