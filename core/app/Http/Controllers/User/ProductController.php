<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FileUploader;
use App\Lib\FormProcessor;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Changelog;
use App\Models\Form;
use App\Models\Product;
use App\Models\Subcategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected function uploadsDisabledResponse()
    {
        $notify[] = ['error', 'Vendor item uploads are disabled. Products are now managed by the admin catalog only.'];
        return to_route('home')->withNotify($notify);
    }

    public function selectCategory()
    {
        return $this->uploadsDisabledResponse();
    }

    public function upload()
    {
        return $this->uploadsDisabledResponse();
    }

    public function edit($slug)
    {
        return $this->uploadsDisabledResponse();
    }

    public function saveProduct(Request $request, $id = null)
    {
        return $this->uploadsDisabledResponse();

        if (!$id) {
            $product          = new Product();
            $product->slug    = generateUniqueProductSlug($request->title);
            $product->user_id = auth()->id();
            $isFree           = $request->has('is_free') && $request->is_free == 1;
        } else {
            $product = Product::findOrFail($id);
            $isFree  = $product->is_free;
        }

        $isRequired        = $id ? 'nullable' : 'required';

        $validationRule = [
            'title'         => 'required',
            'description'   => 'required',
            'category'      => 'required',
            'thumbnail'     => [$isRequired, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'preview_image' => [$isRequired, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'main_file'     => [$isRequired, new FileTypeValidate(['zip'])],
            'demo_url'      => 'nullable|url',
            'is_free'       => 'nullable|in:0,1',
        ];


        if ($request->is_free == 1 && !gs('free_item')) {
            $notify[] = ['error', 'Free item upload is currently disabled'];
            return back()->withNotify($notify);
        }
        $subcategory = Subcategory::active()->findOrFail($request->subcategory);
        $category    = Category::active()->findOrFail($request->category);
        $form        = Form::where('id', $subcategory->form_id)->where('act', 'subcategory_attributes')->first();

        $allValidationRule = $validationRule;
        $formProcessor     = null;
        if ($form) {
            $formProcessor      = new FormProcessor();
            $formValidationRule = $formProcessor->valueValidation($form?->form_data);
            $allValidationRule = array_merge($allValidationRule, $formValidationRule);
        }

        $request->validate($allValidationRule);

        if ($id && $request->hasFile('main_file') && $product->product_updated == Status::PRODUCT_UPDATE_PENDING) {
            $notify[] = ['error', 'You have a pending submission'];
            return back()->withNotify($notify);
        }

        if (gs('free_item')) {
            $product->is_free = $isFree ? Status::ENABLE : Status::DISABLE;
        }

        $product->title = $request->title;
        $purifier       = new \HTMLPurifier();
        if ($request->changelog) {
            if (!gs('changelog')) {
                $notify[] = ['error', 'Changelog option is currently disabled'];
                return back()->withNotify($notify);
            }

            if (isset($request->changelog)) {
                foreach ($request->changelog as $changelog) {
                    if (!empty($changelog['heading']) && !empty($changelog['description'])) {
                        Changelog::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'heading'    => $changelog['heading'],
                            ],
                            [
                                'description' => htmlspecialchars_decode($purifier->purify($changelog['description'])),
                            ]
                        );
                    }
                }
            }
        }

        if ($request->hasFile('main_file')) {
            $this->uploadMainFile($request, $product, $id);
        }

        if ($request->hasFile('screenshots')) {
            $this->uploadScreenshot($request, $product, $id);
        }

        if ($request->hasFile('thumbnail')) {
            $this->uploadThumbnail($request, $product, $id);
        }

        if ($request->hasFile('preview_image')) {
            $this->uploadPreviewImage($request, $product, $id);
        }

        $attributeInfo = [];
        if ($formProcessor) {
            $attributeInfo = $formProcessor->processFormData($request, $form->form_data);
        }
        $product->tags            = $request->tags;
        $product->description     = htmlspecialchars_decode($purifier->purify($request->description));
        $product->category_id     = $category->id;
        $product->subcategory_id  = $subcategory->id;
        $product->demo_url        = $request->demo_url;
        $product->attribute_info  = $attributeInfo;
        $product->save();

        if ($request->message) {
            $activity             = new Activity();
            $activity->user_id    = auth()->id();
            $activity->message    = $request->message;
            $activity->product_id = $product->id;
            $activity->save();
        }

        if ($request->hasFile('main_file')) {
            $notify[] = ['info', 'Your submission is under review'];
        }

        $notify[] = ['success', 'Product information saved successfully'];

        return back()->withNotify($notify);
    }

    public function productActivities($slug)
    {
        return $this->uploadsDisabledResponse();

        $pageTitle = 'Item Activity Log';
        $product   = Product::where('status', '!=', Status::PRODUCT_HARD_REJECTED)->countComment()->where('slug', $slug)->firstOrFail();

        abort_if($product->user_id != auth()->id(), 404);
        $activities = $product->activities()->with(['user', 'reviewer'])->latest()->take(8)->get();

        return view('Template::user.product.activities', compact('pageTitle', 'product', 'activities'));
    }

    public function ajaxActivity($slug){
        return response()->json(['html' => '', 'hasMore' => false]);

        if (!request()->ajax()) {
            return response()->json(['html' => '', 'hasMore' => false]);
        }

        $product = Product::where('user_id', auth()->id())->where('slug', $slug)->first();
        if(!$product){
            return response()->json(['html' => '', 'hasMore' => false]);
        }

        $perPage = 8;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;


        $activities = $product->activities()->with('user', 'reviewer')->latest()->skip($offset)->take($perPage)->get();

        if ($activities->isEmpty()) {
            return response()->json(['html' => '', 'hasMore' => false]);
        }

        $hasMore = $product->activities()->latest()->skip($offset + $perPage)->take(1)->exists();
        $html = view('reviewer.partials.activity', compact('activities'))->render();
        return response()->json(['html' => $html, 'hasMore' => $hasMore]);
    }

    public function replyActivity(Request $request, $productId)
    {
        return $this->uploadsDisabledResponse();

        $request->validate([
            'message' => 'required',
        ]);

        $activity             = new Activity();
        $activity->message    = $request->message;
        $activity->product_id = $productId;
        $activity->user_id    = auth()->id();
        $activity->save();

        $notify[] = ['success', 'Your message submitted successfully'];
        return back()->withNotify($notify);
    }

    private function uploadScreenshot($request, &$product, $id)
    {
        try {
            $slug          = $product->slug;
            $zipPath       = $request->file('screenshots')->path();
            $extractedPath = getFilePath('screenshots') . '/' . $slug . '/screenshots';

            $zip = new \ZipArchive;
            $zip->open($zipPath);
            $invalidFile = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename      = $zip->getNameIndex($i);
                $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

                if (!in_array($fileExtension, ['png', 'jpg', 'jpeg']) || strpos($filename, '/') != false) {
                    $invalidFile = true;
                    break;
                }
            }

            if ($invalidFile) {
                $notify[] = ['error', 'You have to upload images only'];
                return back()->withInput($request->all())->withNotify($notify);
            }

            if ($id && is_dir($extractedPath)) {
                fileManager()->removeDirectory($extractedPath);
            }
            fileManager()->makeDirectory($extractedPath);

            $zip->extractTo($extractedPath);
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t extract and upload your screenshots'];
            return back()->withNotify($notify);
        }
    }

    private function uploadThumbnail($request, &$product, $id)
    {
        try {
            $slug               = $product->slug;
            $product->thumbnail = fileUploader(
                $request->thumbnail,
                getFilePath('productThumbnail') . '/' . $slug,
                getFileSize('productThumbnail'),
                $product->thumbnail ?? null
            );
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your preview image'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    private function uploadPreviewImage($request, &$product)
    {
        try {
            $slug = $product->slug;

            $product->preview_image = fileUploader(
                $request->preview_image,
                getFilePath('productPreview') . '/' . $slug,
                getFileSize('productPreview'),
                $product->preview_image ?? null
            );

            $product->inline_preview_image = fileUploader(
                $request->preview_image,
                getFilePath('productInlinePreview') . '/' . $slug,
                getFileSize('productInlinePreview'),
                $product->inline_preview_image ?? null
            );
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your preview image'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    private function uploadMainFile($request, &$product, $id)
    {
        try {
            $slug = $product->slug;
            if ($id) {
                $product->product_updated = Status::PRODUCT_UPDATE_PENDING;
            }

            $fileUploader       = new FileUploader();
            $fileUploader->path = getFilePath('productFile') . '/' . $slug;
            $fileUploader->file = $request->main_file;
            $fileUploader->upload();

            $product->temp_file = $fileUploader->fileName;
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your file'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    public function commenting($slug)
    {
        return $this->uploadsDisabledResponse();

        $product = Product::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();

        $product->comment_disable = !$product->comment_disable;
        $product->save();
        $message  = $product->comment_disable ? 'Comments have been disabled' : 'Comments have been enabled';
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}
