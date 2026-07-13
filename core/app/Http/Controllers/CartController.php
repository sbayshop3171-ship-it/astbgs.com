<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CatalogCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $pageTitle = 'Cart';
        $items = CatalogCart::items();
        $subtotal = CatalogCart::subtotal();

        return view('Template::cart.index', compact('pageTitle', 'items', 'subtotal'));
    }

    public function add(Request $request, $slug)
    {
        $product = Product::with(['activeOptions'])->catalogPublished()->where('slug', $slug)->firstOrFail();
        $request->validate([
            'quantity'          => 'nullable|integer|min:1|max:99',
            'product_option_id' => 'nullable|exists:product_options,id',
            'request_note'      => 'nullable|string|max:1000',
            'requested_amount'  => 'nullable|numeric|min:0',
        ]);

        $option = null;
        if ($product->hasActiveOptions()) {
            $option = $product->activeOptions()->findOrFail($request->product_option_id);
        }

        $requestedAmount = $request->requested_amount;

        if (
            $product->product_type === Status::PRODUCT_TYPE_OPTION_REQUEST
            && $option
            && ($option->min_amount !== null || $option->max_amount !== null)
        ) {
            $request->validate([
                'requested_amount' => 'required|numeric|min:0',
            ]);

            if ($option->min_amount !== null && (float) $requestedAmount < (float) $option->min_amount) {
                $notify[] = ['error', 'Requested amount is below the minimum allowed range'];
                return back()->withInput()->withNotify($notify);
            }

            if ($option->max_amount !== null && (float) $requestedAmount > (float) $option->max_amount) {
                $notify[] = ['error', 'Requested amount is above the maximum allowed range'];
                return back()->withInput()->withNotify($notify);
            }
        }

        CatalogCart::add($product, $option, (int) ($request->quantity ?? 1), $request->request_note, $requestedAmount);

        $notify[] = ['success', $product->hasActiveOptions() ? 'Product option added to cart' : 'Product added to cart'];
        return to_route('cart.index')->withNotify($notify);
    }

    public function update(Request $request)
    {
        $request->validate([
            'items'                => 'required|array',
            'items.*.quantity'     => 'required|integer|min:1|max:99',
            'items.*.request_note' => 'nullable|string|max:1000',
            'items.*.requested_amount' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->items as $cartKey => $item) {
            CatalogCart::update($cartKey, $item);
        }

        $notify[] = ['success', 'Cart updated successfully'];
        return back()->withNotify($notify);
    }

    public function remove($cartKey)
    {
        CatalogCart::remove($cartKey);
        $notify[] = ['success', 'Item removed from cart'];
        return back()->withNotify($notify);
    }

    public function checkout()
    {
        $items = CatalogCart::items();
        abort_if($items->isEmpty(), 404);

        $pageTitle = 'Checkout';
        $subtotal = CatalogCart::subtotal();

        return view('Template::cart.checkout', compact('pageTitle', 'items', 'subtotal'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_note' => 'nullable|string|max:2000',
        ]);

        $items = CatalogCart::items();
        if ($items->isEmpty()) {
            $notify[] = ['error', 'Your cart is empty'];
            return to_route('cart.index')->withNotify($notify);
        }

        $subtotal = CatalogCart::subtotal();

        $order = new Order();
        $order->user_id = auth()->id();
        $order->order_number = generateOrderNumber();
        $order->status = $subtotal > 0 ? Status::CATALOG_ORDER_PENDING_PAYMENT : Status::CATALOG_ORDER_COMPLETED;
        $order->currency = gs('cur_text');
        $order->subtotal = $subtotal;
        $order->total = $subtotal;
        $order->customer_note = $request->customer_note;
        $order->paid_at = $subtotal > 0 ? null : now();
        $order->save();

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->product_option_id = $item['product_option_id'];
            $orderItem->title = $item['title'];
            $orderItem->delivery_type = $item['delivery_type'];
            $orderItem->option_name = $item['option_name'];
            $orderItem->unit_price = $item['unit_price'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->line_total = $item['unit_price'] * $item['quantity'];
            $orderItem->detail = [
                'request_note'      => $item['request_note'],
                'requested_amount'  => $item['requested_amount'] ?? null,
                'availability_note' => $item['availability_note'],
                'min_amount'        => $item['min_amount'],
                'max_amount'        => $item['max_amount'],
                'attribute_info'    => $product?->attribute_info ?? [],
            ];
            $orderItem->save();
        }

        CatalogCart::clear();

        if ($subtotal <= 0) {
            $notify[] = ['success', 'Order placed successfully'];
            return to_route('user.orders.show', $order->id)->withNotify($notify);
        }

        $notify[] = ['success', 'Order created successfully. Please complete payment.'];
        return to_route('user.orders.pay', $order->id)->withNotify($notify);
    }
}
