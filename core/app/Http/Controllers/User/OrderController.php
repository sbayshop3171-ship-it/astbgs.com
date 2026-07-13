<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FileUploader;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductFile;

class OrderController extends Controller
{
    public function index()
    {
        $pageTitle = 'My Orders';
        $orders = auth()->user()->orders()->with('items.product')->paginate(getPaginate());

        return view('Template::user.orders.index', compact('pageTitle', 'orders'));
    }

    public function show($id)
    {
        $pageTitle = 'Order Details';
        $order = auth()->user()->orders()->with(['items.product.files', 'items.option', 'deposit'])->findOrFail($id);

        return view('Template::user.orders.show', compact('pageTitle', 'order'));
    }

    public function pay($id)
    {
        $order = auth()->user()->orders()->pendingPayment()->findOrFail($id);

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $pageTitle = 'Payment Methods';

        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'order'));
    }

    public function downloadFile($orderItemId, $fileId)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($orderItemId);
        abort_if($orderItem->order->user_id !== auth()->id(), 404);
        abort_if(!$orderItem->order->isPaid(), 404);

        $file = ProductFile::active()->where('product_id', $orderItem->product_id)->findOrFail($fileId);
        abort_if($file->product_option_id && $file->product_option_id !== $orderItem->product_option_id, 404);

        $fileUploader = new FileUploader();
        $path = getFilePath('productFile') . '/' . $orderItem->product->slug . '/' . $file->stored_name;

        return $fileUploader->downloadRelativeFile($path, $file->display_name, $orderItem);
    }
}
