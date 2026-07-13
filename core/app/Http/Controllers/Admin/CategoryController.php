<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Category;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller {
    public function index() {
        $pageTitle  = 'Categories';
        $categories = Category::searchable(['name'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.product.categories', compact('pageTitle', 'categories'));
    }

    public function form($id = 0) {
        $pageTitle = 'Add Category';
        $category = null;
        if ($id) {
            $category = Category::findOrFail($id);
            $pageTitle = 'Edit ' . keyToTitle($category?->name);
        }
        return view('admin.product.category_form', compact('pageTitle', 'category'));
    }
    public function store(Request $request, $id = 0) {
        $rules = [
            'name' => 'required|unique:categories,name,' . $id,
            'author_commission'   => 'required|numeric',
            'featured'            => 'nullable|boolean',
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
            'seo_image' => ['nullable', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ];
        $imageRules = ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];

        if (!$id) {
            $rules['image'] = ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
        } else {
            $rules['image'] = $imageRules;
        }

        $rules['image_2'] = $imageRules;
        $rules['image_3'] = $imageRules;

        $request->validate($rules);

        if ($id) {
            $category =   Category::findOrFail($id);
            $notification = "Category updated successfully.";
        } else {
            $category =  new Category();
            $notification = "Category created successfully";
        }

        $category->name = $request->name;
        $category->slug = slug($request->name);
        $category->author_commission = $request->author_commission;
        $category->featured = $request->boolean('featured') ? Status::YES : Status::NO;

        $imageFields = ['image', 'image_2', 'image_3'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                try {
                    $old = $category->{$field} ?? null;
                    $file = $request->file($field);

                    if ($file && $file->isValid()) {
                        $category->{$field} = fileUploader(
                            $file,
                            getFilePath('category'),
                            getFileSize('category'),
                            $old
                        );
                    }
                } catch (\Exception $exp) {
                    $notify[] = ['error', "Couldn't upload your {$field}"];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }

        $seoImage = $category->seo_content?->image ?? null;
        if ($request->hasFile('seo_image')) {
            try {
                $path = getFilePath('seo');
                $seoImage = fileUploader($request->seo_image, $path, getFileSize('seo'), $seoImage);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the SEO image'];
                return back()->withNotify($notify)->withInput();
            }
        }


        $category->seo_content = [
            'image' => $seoImage,
            'description' => $request->description,
            'social_title' => $request->social_title,
            'social_description' => $request->social_description,
            'meta_robots' => $request->meta_robots,
            'keywords' => $request->keywords,
        ];

        if (!$id) {
            RequiredConfig::configured('product_category');
        }

        $category->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function toggleFeature($id) {
        return Category::changeStatus($id, 'featured');
    }
    public function status($id) {
        return  Category::changeStatus($id);
    }
}
