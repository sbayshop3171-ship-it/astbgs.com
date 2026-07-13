<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ManageOrderController extends Controller
{
    public function index()
    {
        $pageTitle = 'Catalog Orders';
        $orders = Order::with(['user', 'items'])
            ->when(request()->filled('status'), function ($query) {
                $query->where('status', request()->status);
            })
            ->when(request()->filled('search'), function ($query) {
                $search = request()->search;
                $query->where(function ($builder) use ($search) {
                    $builder->where('order_number', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('username', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(getPaginate());

        return view('admin.orders.index', compact('pageTitle', 'orders'));
    }

    public function show($id)
    {
        $pageTitle = 'Order Details';
        $order = Order::with(['user', 'items.product', 'items.option', 'deposit'])->findOrFail($id);
        $statuses = [
            Status::CATALOG_ORDER_PENDING_PAYMENT,
            Status::CATALOG_ORDER_PAID,
            Status::CATALOG_ORDER_PROCESSING,
            Status::CATALOG_ORDER_COMPLETED,
            Status::CATALOG_ORDER_CANCELLED,
        ];

        return view('admin.orders.show', compact('pageTitle', 'order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status'         => 'required|in:' . implode(',', [
                Status::CATALOG_ORDER_PENDING_PAYMENT,
                Status::CATALOG_ORDER_PAID,
                Status::CATALOG_ORDER_PROCESSING,
                Status::CATALOG_ORDER_COMPLETED,
                Status::CATALOG_ORDER_CANCELLED,
            ]),
            'internal_note'  => 'nullable|string',
            'customer_note'  => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->internal_note = $request->internal_note;
        $order->customer_note = $request->customer_note;
        if ($order->isPaid() && !$order->paid_at) {
            $order->paid_at = now();
        }
        $order->save();

        $notify[] = ['success', 'Order updated successfully'];
        return back()->withNotify($notify);
    }
}
