@extends('Template::layouts.frontend')
@section('content')
    <section class="py-60">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h4 class="mb-0">{{ __($pageTitle) }}</h4>
                <a href="{{ route('products') }}" class="btn btn-outline--base btn--sm">@lang('Continue Shopping')</a>
            </div>

            @if($items->isEmpty())
                <div class="card custom--card">
                    <div class="card-body">
                        <x-empty-list title="Your cart is empty" />
                    </div>
                </div>
            @else
                <form action="{{ route('cart.update') }}" method="POST">
                    @csrf
                    <div class="card custom--card mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table--light">
                                    <thead>
                                        <tr>
                                            <th>@lang('Item')</th>
                                            <th>@lang('Type')</th>
                                            <th>@lang('Quantity')</th>
                                            <th>@lang('Price')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ getImage(getFilePath('productThumbnail') . '/' . $item['slug'] . '/' . $item['thumbnail'], getFileSize('productThumbnail')) }}" alt="" width="60">
                                                        <div>
                                                            <a href="{{ route('product.details', $item['slug']) }}">{{ __($item['title']) }}</a>
                                                            @if($item['option_name'])
                                                                <div class="small text-muted">{{ $item['option_name'] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ __(ucfirst(str_replace('_', ' ', $item['product_type']))) }}</td>
                                                <td style="max-width: 110px;">
                                                    <input type="number" min="1" max="99" class="form-control" name="items[{{ $item['cart_key'] }}][quantity]" value="{{ $item['quantity'] }}">
                                                </td>
                                                <td>{{ showAmount($item['unit_price'] * $item['quantity']) }}</td>
                                                <td>
                                                    <button formaction="{{ route('cart.remove', $item['cart_key']) }}" class="btn btn-outline--danger btn--sm">@lang('Remove')</button>
                                                </td>
                                            </tr>
                                            @if($item['request_note'] || $item['availability_note'] || !is_null($item['requested_amount']))
                                                <tr>
                                                    <td colspan="5" class="small text-muted">
                                                        @if($item['availability_note'])
                                                            <div><strong>@lang('Availability:')</strong> {{ $item['availability_note'] }}</div>
                                                        @endif
                                                        @if(!is_null($item['requested_amount']))
                                                            <div class="mt-2">
                                                                <label class="form-label">@lang('Requested Amount')</label>
                                                                <input type="number" step="any" min="0" name="items[{{ $item['cart_key'] }}][requested_amount]" class="form-control" value="{{ $item['requested_amount'] }}">
                                                            </div>
                                                        @endif
                                                        <div class="mt-2">
                                                            <label class="form-label">@lang('Request Note')</label>
                                                            <textarea name="items[{{ $item['cart_key'] }}][request_note]" class="form-control" rows="2">{{ $item['request_note'] }}</textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <button type="submit" class="btn btn-outline--base">@lang('Update Cart')</button>
                        <div class="text-end">
                            <div class="mb-2"><strong>@lang('Subtotal:')</strong> {{ showAmount($subtotal) }}</div>
                            <a href="{{ route('cart.checkout') }}" class="btn btn--base">@lang('Proceed to Checkout')</a>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection
