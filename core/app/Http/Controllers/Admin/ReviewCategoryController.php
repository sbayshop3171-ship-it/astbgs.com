<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewCategory;
use Illuminate\Http\Request;

class ReviewCategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'Review Categories';
        $reviewCategories = ReviewCategory::searchable(['name'])->paginate(getPaginate());
        return view('admin.product.review_categories', compact('pageTitle', 'reviewCategories'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|unique:review_categories,name,' . $id
        ]);
        if ($id) {
            $reviewCategory = ReviewCategory::findOrFail($id);
            $notify[] = ['success', 'Review category updated'];
        } else {
            $reviewCategory       = new ReviewCategory();
            $notify[] = ['success', 'Review category created'];
        }

        $reviewCategory->name = $request->name;
        $reviewCategory->save();

        return back()->withNotify($notify);
    }

    public function toggleStatus($id)
    {
        return ReviewCategory::changeStatus($id);
    }
}
