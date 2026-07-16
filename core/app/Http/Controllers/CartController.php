<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CatalogCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'product_option_id' => [
                'nullable',
                Rule::requiredIf($product->hasActiveOptions()),
                Rule::exists('product_options', 'id')->where(function ($query) use ($product) {
                    $query->where('product_id', $product->id)->where('is_active', Status::YES);
                }),
            ],
            'request_note'      => 'nullable|string|max:1000',
            'requested_amount'  => 'nullable|numeric|min:0',
            'redirect_to'       => 'nullable|in:cart,checkout',
        ]);

        $option = null;
        if ($product->hasActiveOptions()) {
            $option = $product->activeOptions()->find($request->product_option_id);

            if (!$option) {
                throw ValidationException::withMessages([
                    'product_option_id' => ['Please select a valid product option before adding this item to the cart.'],
                ]);
            }
        }

        $requestedAmount = $request->requested_amount;

        if ($error = $this->validateRequestedAmount($product->product_type, $option?->min_amount, $option?->max_amount, $requestedAmount)) {
            throw ValidationException::withMessages([
                'requested_amount' => [$error],
            ]);
        }

        $item = CatalogCart::add($product, $option, (int) ($request->quantity ?? 1), $request->request_note, $requestedAmount);

        $notify[] = ['success', $product->hasActiveOptions() ? 'Product option added to cart' : 'Product added to cart'];

        if ($request->expectsJson()) {
            return response()->json($this->cartResponsePayload(
                $notify[0][1],
                $item,
                $product->hasActiveOptions() ? 'Product option added to cart' : 'Product added to cart'
            ));
        }

        if ($request->redirect_to === 'checkout') {
            return to_route('cart.checkout')->withNotify($notify);
        }

        return to_route('cart.index')->withNotify($notify);
    }

    public function setItemQuantity(Request $request, $cartKey)
    {
        $request->validate([
            'quantity'         => 'required|integer|min:0|max:99',
            'request_note'     => 'nullable|string|max:1000',
            'requested_amount' => 'nullable|numeric|min:0',
        ]);

        $existingItem = CatalogCart::find($cartKey);

        if (!$existingItem) {
            throw ValidationException::withMessages([
                'quantity' => ['This cart item no longer exists.'],
            ]);
        }

        if ($error = $this->validateRequestedAmount(
            $existingItem['product_type'] ?? null,
            $existingItem['min_amount'] ?? null,
            $existingItem['max_amount'] ?? null,
            $request->requested_amount ?? ($existingItem['requested_amount'] ?? null)
        )) {
            throw ValidationException::withMessages([
                'requested_amount' => [$error],
            ]);
        }

        $item = CatalogCart::setQuantity($cartKey, (int) $request->quantity, [
            'request_note'     => $request->request_note,
            'requested_amount' => $request->requested_amount,
        ]);
        $message = $item ? 'Cart updated successfully' : 'Item removed from cart';

        if ($request->expectsJson()) {
            return response()->json($this->cartResponsePayload($message, $item, $message));
        }

        $notify[] = ['success', $message];

        return back()->withNotify($notify);
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
            $existingItem = CatalogCart::items()->get($cartKey);

            if ($existingItem) {
                if ($error = $this->validateRequestedAmount(
                    $existingItem['product_type'] ?? null,
                    $existingItem['min_amount'] ?? null,
                    $existingItem['max_amount'] ?? null,
                    $item['requested_amount'] ?? null
                )) {
                    $notify[] = ['error', $error];
                    return back()->withInput()->withNotify($notify);
                }
            }

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

    protected function validateRequestedAmount(?string $productType, $minAmount, $maxAmount, $requestedAmount): ?string
    {
        $hasRange = $minAmount !== null || $maxAmount !== null;

        if ($productType !== Status::PRODUCT_TYPE_OPTION_REQUEST || !$hasRange) {
            return null;
        }

        if (!filled($requestedAmount)) {
            return 'Requested amount is required for the selected range option';
        }

        if ($minAmount !== null && (float) $requestedAmount < (float) $minAmount) {
            return 'Requested amount is below the minimum allowed range';
        }

        if ($maxAmount !== null && (float) $requestedAmount > (float) $maxAmount) {
            return 'Requested amount is above the maximum allowed range';
        }

        return null;
    }

    protected function cartResponsePayload(string $message, ?array $item = null, ?string $statusMessage = null): array
    {
        return [
            'status'          => 'success',
            'message'         => $statusMessage ?? $message,
            'cart_count'      => CatalogCart::count(),
            'cart_subtotal'   => showAmount(CatalogCart::subtotal()),
            'cart_url'        => route('cart.index'),
            'checkout_url'    => route('cart.checkout'),
            'item'            => $item ? [
                'cart_key'          => $item['cart_key'],
                'product_id'        => $item['product_id'],
                'product_option_id' => $item['product_option_id'],
                'quantity'          => $item['quantity'],
                'requested_amount'  => $item['requested_amount'],
                'item_total'        => showAmount(((float) $item['unit_price']) * ((int) $item['quantity'])),
                'item_url'          => route('cart.item.sync', $item['cart_key']),
            ] : null,
        ];
    }
}
