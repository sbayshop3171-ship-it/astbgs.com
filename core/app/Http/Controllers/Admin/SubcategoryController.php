<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\RequiredConfig;
use App\Models\Category;
use App\Models\Form;
use App\Models\Subcategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubcategoryController extends Controller {
    public function index() {
        $pageTitle     = 'Subcategories';
        $subcategories = Subcategory::searchable(['name', 'category:name'])->with('category', 'reviewers')->orderBy('id', 'DESC')->paginate(getPaginate());
        $categories    = Category::active()->get();
        return view('admin.product.subcategories', compact('pageTitle', 'subcategories', 'categories'));
    }

    public function form($id = 0) {
        if ($id) {
            $subcategory   = Subcategory::findOrFail($id);
            $pageTitle     = 'Update Subcategory';
        } else {
            $subcategory   = null;
            $pageTitle     = 'Add New Subcategory';
        }

        $categories    = Category::orderBy('name')->get();
        return view('admin.product.subcategory_form', compact('pageTitle', 'subcategory', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|unique:subcategories,name,' . $id,
            'description'         => 'nullable|string',
            'social_title'        => 'nullable|string|max:100',
            'social_description'  => 'nullable|string',
            'keywords'            => 'nullable|array',
            'meta_robots' => [
                'nullable',
                Rule::in([
                    'index,follow',
                    'index,nofollow',
                    'noindex,follow',
                    'noindex,nofollow',
                ]),
            ],
            'image' => ['nullable', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);

        if ($id) {
            $subcategory  = Subcategory::findOrFail($id);
            $notify[] = ['success', 'Subcategory updated successfully'];
        } else {
            $subcategory  = new Subcategory();
            $subcategory->status = Status::ENABLE;
            $notify[] = ['success', 'Subcategory added successfully'];
            RequiredConfig::configured('product_subcategory');
        }

        $subcategory->category_id = $request->category_id;
        $subcategory->name        = $request->name;
        $subcategory->slug        = slug($request->name);

        $image = $subcategory->seo_content?->image ?? null;
        if ($request->hasFile('image')) {
            try {
                $path = getFilePath('seo');
                $image = fileUploader($request->image, $path, getFileSize('seo'), $image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify)->withInput();
            }
        }

        $subcategory->seo_content = [
            'image' => $image,
            'description' => $request->description,
            'social_title' => $request->social_title,
            'social_description' => $request->social_description,
            'keywords' => $request->keywords,
            'meta_robots' => $request->meta_robots
        ];
        $subcategory->save();
        return back()->withNotify($notify);
    }

    public function attributes($categoryId) {
        $pageTitle   = 'Category Attributes';
        $subcategory = Subcategory::findOrFail($categoryId);
        $form        = Form::where('id', $subcategory->form_id)->where('act', 'subcategory_attributes')->first();

        return view('admin.product.attribute_info', compact('pageTitle', 'form', 'subcategory'));
    }

    public function syncReviewers(Request $request, $id) {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->reviewers()->sync($request->reviewer_id);
        $notify[] = ['success', 'Reviewers updated successfully'];
        return back()->withNotify($notify);
    }

    public function storeAttributes($categoryId) {
        $formProcessor = new FormProcessor();
        $subcategory   = Subcategory::findOrFail($categoryId);
        $formExists    = Form::where('id', $subcategory->form_id)->where('act', 'subcategory_attributes')->exists();

        $form                 = $formProcessor->generate('subcategory_attributes', $formExists, identifierField: 'id', identifier: $subcategory->form_id);
        $subcategory->form_id = $form->id;
        $subcategory->save();

        $notify[] = ['success', 'Form updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Subcategory::changeStatus($id);
    }
}
