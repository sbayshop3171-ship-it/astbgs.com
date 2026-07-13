<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Product;
use Illuminate\Http\Request;

class ManageProductController extends Controller
{
    public function details($slug)
    {
        $pageTitle = 'Product Details';
        $product = Product::with(['author', 'category', 'subcategory'])->where('slug', $slug)->firstOrFail();
        return view('admin.product.details', compact('pageTitle', 'product'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Items';
        $products  = $this->productData('pending');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function softRejected()
    {
        $pageTitle = 'Soft Rejected Items';
        $products  = $this->productData('softRejected');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function hardRejected()
    {
        $pageTitle = 'Hard Rejected Items';
        $products  = $this->productData('hardRejected');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function down()
    {
        $pageTitle = 'Soft Disabled Items';
        $products  = $this->productData('down');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function permanentDown()
    {
        $pageTitle = 'Permanent Disabled Items';
        $products  = $this->productData('permanentDown');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Items';
        $products = $this->productData('approved');
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    public function all($authorId = 0)
    {
        $pageTitle = 'All Items';
        $products = $this->productData(authorId: $authorId);
        return view('admin.product.list', compact('pageTitle', 'products'));
    }

    private function productData($scope = null, $authorId = 0)
    {
        $products = $scope ? Product::$scope() : Product::query();
        if ($authorId) $products = $products->where('user_id', $authorId);
        $products = $products->with(['author', 'category', 'subCategory'])->searchable(['title', 'author:username'])->filter(['is_free'])->dateFilter()->orderBy('id', 'desc')->paginate(getPaginate());
        return $products;
    }

    public function toggleFeature($id)
    {
        $product = Product::findOrFail($id);

        if ($product->status != Status::PRODUCT_APPROVED) {
            $notify[] = ['error', 'Product is not approved'];
            return back()->withNotify($notify);
        }

        $product->is_featured = !$product->is_featured;
        $product->save();
        $notify[] = ['success', 'Featured changed successfully'];
        return back()->withNotify($notify);
    }

    public function approve($id)
    {
        $product = Product::with(['author', 'category'])->findOrFail($id);

        if (!in_array($product->status, [Status::PRODUCT_PENDING, Status::PRODUCT_SOFT_REJECTED])) {
            $notify[] = ['error', 'Only pending or soft rejected products can be approved from admin panel'];
            return back()->withNotify($notify);
        }

        if (!$product->file && !$product->temp_file) {
            $notify[] = ['error', 'Product file was not found'];
            return back()->withNotify($notify);
        }

        if ($product->temp_file) {
            $product->file      = $product->temp_file;
            $product->temp_file = null;
        }

        $product->assigned_to  = Status::NO;
        $product->approved_by  = null;
        $product->published_at = now();
        $product->status       = Status::PRODUCT_APPROVED;
        $product->save();

        $activity = $this->storeAdminActivity(
            $product,
            '[Approve] Your product has been approved by admin',
            Status::PRODUCT_APPROVED
        );

        $author = $product->author;
        if ($author?->email_settings?->review_notification) {
            notify(
                $author,
                'PRODUCT_APPROVED',
                [
                    'author'           => $author->username,
                    'message'          => $activity->message,
                    'product_name'     => $product->title,
                    'product_category' => $product->category?->title ?? $product->category?->name,
                    'url'              => route('product.details', $product->slug),
                    'approved_time'    => showDateTime(now()),
                ],
                ['email']
            );
        }

        $notify[] = ['success', 'Product approved successfully'];
        return to_route('admin.product.details', $product->slug)->withNotify($notify);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $product = Product::with(['author', 'category'])->findOrFail($id);

        $product->assigned_to = Status::NO;
        $product->status      = Status::PRODUCT_SOFT_REJECTED;
        $product->save();

        $activity = $this->storeAdminActivity(
            $product,
            '[Soft Reject] ' . $request->reason,
            Status::PRODUCT_SOFT_REJECTED
        );

        $author = $product->author;
        if ($author?->email_settings?->review_notification) {
            notify(
                $author,
                'PRODUCT_SOFT_REJECTED',
                [
                    'author'           => $author->username,
                    'product_name'     => $product->title,
                    'product_category' => $product->category?->name,
                    'review_time'      => showDateTime(now()),
                    'message'          => $activity->message,
                    'edit_url'         => route('user.product.edit', $product->slug),
                ],
                ['email']
            );
        }

        $notify[] = ['success', 'Product rejected successfully'];
        return to_route('admin.product.details', $product->slug)->withNotify($notify);
    }

    private function storeAdminActivity(Product $product, string $message, int $status): Activity
    {
        $activity              = new Activity();
        $activity->message     = $message;
        $activity->product_id  = $product->id;
        $activity->status      = $status;
        $activity->reviewer_id = 0;
        $activity->save();

        return $activity;
    }
}
