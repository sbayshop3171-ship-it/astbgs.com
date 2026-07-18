@extends('Template::layouts.frontend')
@section('content')
    <section class="cart-page py-60">
        <div class="container">
            <div class="cart-page__top d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
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
                <form action="{{ route('cart.update') }}" method="POST" class="cart-form">
                    @csrf
                    <div class="card custom--card cart-table-card mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive cart-table-responsive">
                                <table class="table table--light cart-table">
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
                                            <tr class="cart-item-row">
                                                <td class="cart-item-cell" data-label="@lang('Item')">
                                                    <div class="cart-item-info d-flex align-items-center gap-3">
                                                        <img class="cart-item-thumb" src="{{ getImage(getFilePath('productThumbnail') . '/' . $item['slug'] . '/' . $item['thumbnail'], getFileSize('productThumbnail')) }}" alt="{{ __($item['title']) }}" width="60">
                                                        <div class="cart-item-content">
                                                            <a class="cart-item-title" href="{{ route('product.details', $item['slug']) }}">{{ __($item['title']) }}</a>
                                                            @if($item['option_name'])
                                                                <div class="cart-item-option small text-muted">{{ $item['option_name'] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cart-type-cell" data-label="@lang('Type')">{{ __(ucfirst(str_replace('_', ' ', $item['product_type']))) }}</td>
                                                <td class="cart-quantity-cell" data-label="@lang('Quantity')">
                                                    <input type="number" min="1" max="99" class="form-control cart-qty-input" name="items[{{ $item['cart_key'] }}][quantity]" value="{{ $item['quantity'] }}">
                                                </td>
                                                <td class="cart-price-cell" data-label="@lang('Price')">{{ showAmount($item['unit_price'] * $item['quantity']) }}</td>
                                                <td class="cart-action-cell" data-label="@lang('Action')">
                                                    <button formaction="{{ route('cart.remove', $item['cart_key']) }}" class="btn btn-outline--danger btn--sm cart-remove-btn">@lang('Remove')</button>
                                                </td>
                                            </tr>
                                            @if($item['request_note'] || $item['availability_note'] || !is_null($item['requested_amount']))
                                                <tr class="cart-note-row">
                                                    <td colspan="5" class="small text-muted cart-note-cell">
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

                    <div class="cart-footer-actions d-flex justify-content-between flex-wrap gap-3">
                        <button type="submit" class="btn btn-outline--base">@lang('Update Cart')</button>
                        <div class="cart-summary text-end">
                            <div class="cart-subtotal mb-2"><strong>@lang('Subtotal:')</strong> {{ showAmount($subtotal) }}</div>
                            <a href="{{ route('cart.checkout') }}" class="btn btn--base">@lang('Proceed to Checkout')</a>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

@push('style')
    <style>
        .cart-item-info {
            min-width: 0;
        }

        .cart-item-thumb {
            width: 60px;
            height: 60px;
            flex: 0 0 60px;
            object-fit: cover;
            border-radius: 8px;
            background: #f8fafc;
        }

        .cart-item-content {
            min-width: 0;
        }

        .cart-item-title {
            color: hsl(var(--heading-color));
            font-weight: 600;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .cart-item-title:hover {
            color: hsl(var(--base));
        }

        .cart-qty-input {
            max-width: 96px;
            min-height: 40px;
            text-align: center;
        }

        @media (max-width: 767px) {
            .cart-page {
                padding-top: 34px !important;
                padding-bottom: 36px !important;
            }

            .cart-page .container {
                max-width: 100%;
                padding-left: 12px;
                padding-right: 12px;
            }

            .cart-page__top {
                margin-bottom: 16px !important;
            }

            .cart-page__top h4 {
                font-size: 1.2rem;
            }

            .cart-page__top .btn {
                min-height: 36px;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: .78rem;
            }

            .cart-table-card {
                border: 0;
                box-shadow: none;
                background: transparent;
            }

            .cart-table-responsive {
                overflow: visible;
            }

            .cart-table {
                display: block;
                border: 0;
                overflow: visible;
                background: transparent;
            }

            .cart-table thead {
                display: none;
            }

            .cart-table tbody {
                display: grid;
                gap: 12px;
                background: transparent;
            }

            .cart-table tbody tr.cart-item-row {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 10px 12px;
                padding: 12px;
                border: 1px solid #e8edf3;
                border-radius: 10px;
                background: #fff;
                box-shadow: 0 8px 22px rgba(15, 23, 42, .05);
            }

            .cart-table tbody tr.cart-note-row {
                display: block;
                padding: 12px;
                margin-top: -4px;
                border: 1px solid #e8edf3;
                border-radius: 10px;
                background: #fff;
                box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
            }

            .cart-table tbody tr td,
            .cart-table tbody tr:last-child td,
            .cart-table tbody tr td:first-child,
            .cart-table tbody tr td:last-child {
                display: block;
                max-width: none;
                padding: 0;
                border: 0;
                text-align: left;
                font-size: .84rem;
            }

            .cart-table tbody tr.cart-item-row td::before {
                content: attr(data-label);
                display: block;
                width: auto !important;
                margin-bottom: 4px;
                color: #64748b;
                font-size: .68rem;
                font-weight: 700;
                line-height: 1.2;
                text-transform: uppercase;
            }

            .cart-table tbody tr.cart-item-row .cart-item-cell {
                grid-column: 1 / -1;
            }

            .cart-table tbody tr.cart-item-row .cart-item-cell::before,
            .cart-table tbody tr.cart-note-row td::before {
                display: none;
            }

            .cart-item-info {
                display: grid !important;
                grid-template-columns: 72px minmax(0, 1fr);
                gap: 10px !important;
            }

            .cart-item-thumb {
                width: 72px;
                height: 72px;
                flex-basis: 72px;
            }

            .cart-item-title {
                display: -webkit-box;
                overflow: hidden;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                font-size: .9rem;
            }

            .cart-item-option {
                margin-top: 3px;
                font-size: .72rem;
                line-height: 1.3;
            }

            .cart-type-cell,
            .cart-price-cell {
                min-width: 0;
                overflow-wrap: anywhere;
            }

            .cart-quantity-cell,
            .cart-action-cell {
                align-self: end;
            }

            .cart-qty-input {
                width: 76px;
                max-width: 100%;
                min-height: 36px;
                padding: 6px 8px;
                border-radius: 8px;
            }

            .cart-remove-btn {
                min-height: 36px;
                width: 100%;
                padding: 7px 10px;
                border-radius: 8px;
                font-size: .76rem;
                white-space: normal;
            }

            .cart-note-cell .form-label {
                margin-bottom: 4px;
                font-size: .75rem;
            }

            .cart-note-cell .form-control {
                min-height: 38px;
                border-radius: 8px;
                font-size: .82rem;
            }

            .cart-footer-actions {
                display: grid !important;
                grid-template-columns: 1fr;
                gap: 12px !important;
                padding: 12px;
                border: 1px solid #e8edf3;
                border-radius: 10px;
                background: #fff;
                box-shadow: 0 8px 22px rgba(15, 23, 42, .05);
            }

            .cart-footer-actions .btn {
                width: 100%;
                min-height: 42px;
                border-radius: 8px;
                font-size: .88rem;
            }

            .cart-summary {
                text-align: left !important;
            }

            .cart-subtotal {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                font-size: .95rem;
            }
        }
    </style>
@endpush
