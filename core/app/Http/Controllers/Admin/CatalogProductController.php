<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Form;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductOption;
use App\Models\Subcategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CatalogProductController extends Controller
{
    public function index()
    {
        $pageTitle = 'Admin Catalog';
        $products = Product::catalogManaged()
            ->with(['category', 'subcategory', 'activeOptions'])
            ->searchable(['title'])
            ->when(request()->filled('status'), function ($query) {
                $query->where('is_published', request()->status === 'published' ? Status::YES : Status::NO);
            })
            ->when(request()->filled('product_type'), function ($query) {
                $query->where('product_type', request()->product_type);
            })
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.catalog_products.index', compact('pageTitle', 'products'));
    }

    public function create(Request $request)
    {
        return $this->formView(new Product(), 'Create Catalog Product', $request);
    }

    public function edit(Request $request, $id)
    {
        $product = Product::catalogManaged()->with(['options', 'files'])->findOrFail($id);

        return $this->formView($product, 'Edit Catalog Product', $request);
    }

    public function store(Request $request)
    {
        $product = new Product();
        $product->slug = generateUniqueProductSlug($request->title);
        $product->user_id = 0;

        return $this->persist($request, $product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::catalogManaged()->with(['options', 'files'])->findOrFail($id);

        return $this->persist($request, $product);
    }

    protected function formView(Product $product, string $pageTitle, Request $request)
    {
        $selectedCategory = $request->integer('category_id') ?: $product->category_id;
        $selectedSubcategory = $request->integer('subcategory_id') ?: $product->subcategory_id;

        $categories = Category::orderBy('name')->with(['subcategories' => function ($query) {
            $query->orderBy('name');
        }])->get();

        $subcategories = collect();
        $form = null;

        if ($selectedCategory) {
            $subcategories = Subcategory::where('category_id', $selectedCategory)->orderBy('name')->get();
        }

        if ($selectedSubcategory) {
            $form = Form::where('id', optional(Subcategory::find($selectedSubcategory))->form_id)
                ->where('act', 'subcategory_attributes')
                ->first();
        }

        $availabilityOptions = [
            Status::PRODUCT_AVAILABILITY_AVAILABLE,
            Status::PRODUCT_AVAILABILITY_LIMITED,
            Status::PRODUCT_AVAILABILITY_UNAVAILABLE,
        ];

        return view('admin.catalog_products.form', compact(
            'pageTitle',
            'product',
            'categories',
            'subcategories',
            'selectedCategory',
            'selectedSubcategory',
            'form',
            'availabilityOptions'
        ));
    }

    protected function persist(Request $request, Product $product)
    {
        $isNew = !$product->exists;
        $requiredImage = $isNew ? 'required' : 'nullable';

        $validation = [
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'category_id'         => 'required|exists:categories,id',
            'subcategory_id'      => 'required|exists:subcategories,id',
            'product_type'        => 'required|in:' . Status::PRODUCT_TYPE_DOWNLOADABLE . ',' . Status::PRODUCT_TYPE_OPTION_REQUEST,
            'availability_status' => 'required|in:' . implode(',', [
                Status::PRODUCT_AVAILABILITY_AVAILABLE,
                Status::PRODUCT_AVAILABILITY_LIMITED,
                Status::PRODUCT_AVAILABILITY_UNAVAILABLE,
            ]),
            'base_price'          => 'required|numeric|min:0',
            'thumbnail'           => [$requiredImage, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'preview_image'       => [$requiredImage, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'demo_url'            => 'nullable|url',
            'tags'                => 'nullable|array',
            'tags.*'              => 'nullable|string|max:100',
            'screenshots'         => 'nullable|file|mimes:zip',
            'catalog_files.*.file' => 'nullable|file',
        ];

        $subcategory = Subcategory::findOrFail($request->subcategory_id);
        $form = Form::where('id', $subcategory->form_id)->where('act', 'subcategory_attributes')->first();
        $formProcessor = null;

        if ($form) {
            $formProcessor = new FormProcessor();
            $validation = array_merge($validation, $formProcessor->valueValidation($form->form_data));
        }

        $request->validate($validation);

        $product->managed_by_admin = Status::YES;
        $product->status = Status::PRODUCT_APPROVED;
        $product->published_at = $request->boolean('is_published') && !$product->published_at ? now() : $product->published_at;
        $product->is_published = $request->boolean('is_published') ? Status::YES : Status::NO;
        $product->product_type = $request->product_type;
        $product->availability_status = $request->availability_status;
        $product->base_price = $request->base_price;
        $product->title = $request->title;
        $product->description = htmlspecialchars_decode((new \HTMLPurifier())->purify($request->description));
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->demo_url = $request->demo_url;
        $product->tags = array_values(array_filter($request->input('tags', [])));
        $product->attribute_info = $formProcessor ? $formProcessor->processFormData($request, $form->form_data) : [];

        if ($request->hasFile('thumbnail')) {
            $this->uploadThumbnail($request, $product);
        }

        if ($request->hasFile('preview_image')) {
            $this->uploadPreviewImage($request, $product);
        }

        if ($request->hasFile('screenshots')) {
            $this->uploadScreenshots($request, $product);
        }

        $product->save();

        $optionIds = $this->syncOptions($request, $product);
        $this->syncFiles($request, $product, $optionIds);

        if (
            $product->product_type === Status::PRODUCT_TYPE_DOWNLOADABLE
            && !$product->files()->where('is_active', Status::YES)->exists()
        ) {
            $notify[] = ['error', 'At least one active downloadable file is required for downloadable products'];
            return back()->withInput()->withNotify($notify);
        }

        $notify[] = ['success', $isNew ? 'Catalog product created successfully' : 'Catalog product updated successfully'];
        return to_route('admin.catalog.products.edit', $product->id)->withNotify($notify);
    }

    protected function syncOptions(Request $request, Product $product): array
    {
        $submittedOptions = collect($request->input('options', []))
            ->filter(fn($option) => filled($option['name'] ?? null))
            ->values();

        $keptIds = [];
        $optionIdsByIndex = [];

        foreach ($submittedOptions as $index => $optionData) {
            $option = isset($optionData['id']) ? $product->options()->find($optionData['id']) : new ProductOption();
            $option ??= new ProductOption();
            $option->product_id = $product->id;
            $option->name = $optionData['name'];
            $option->price = $optionData['price'] ?? 0;
            $option->min_amount = $optionData['min_amount'] ?: null;
            $option->max_amount = $optionData['max_amount'] ?: null;
            $option->availability_note = $optionData['availability_note'] ?? null;
            $option->sort_order = $optionData['sort_order'] ?? $index;
            $option->is_active = isset($optionData['is_active']) ? Status::YES : Status::NO;
            $option->save();

            $keptIds[] = $option->id;
            $optionIdsByIndex[$index] = $option->id;
        }

        $product->options()->whereNotIn('id', $keptIds ?: [0])->delete();

        return $optionIdsByIndex;
    }

    protected function syncFiles(Request $request, Product $product, array $optionIdsByIndex): void
    {
        $submittedFiles = collect($request->input('catalog_files', []))->values();
        $keptIds = [];

        foreach ($submittedFiles as $index => $fileData) {
            $uploadedFile = $request->file("catalog_files.$index.file");
            $hasExistingId = filled($fileData['id'] ?? null);
            $hasDisplayName = filled($fileData['display_name'] ?? null);

            if (!$hasExistingId && !$hasDisplayName && !$uploadedFile) {
                continue;
            }

            $fileModel = $hasExistingId ? $product->files()->find($fileData['id']) : new ProductFile();
            $fileModel ??= new ProductFile();

            if (!$hasExistingId && !$uploadedFile) {
                continue;
            }

            $fileModel->product_id = $product->id;
            $fileModel->display_name = $fileData['display_name'] ?: ($fileModel->display_name ?? 'Download file');
            $fileModel->product_option_id = $this->resolveOptionReference($fileData['option_reference'] ?? null, $optionIdsByIndex);
            $fileModel->sort_order = $fileData['sort_order'] ?? $index;
            $fileModel->is_active = isset($fileData['is_active']) ? Status::YES : Status::NO;

            if ($uploadedFile) {
                $storedName = fileUploader($uploadedFile, getFilePath('productFile') . '/' . $product->slug, null, $fileModel->stored_name ?? null);
                $fileModel->stored_name = $storedName;
            }

            $fileModel->save();
            $keptIds[] = $fileModel->id;
        }

        $product->files()->whereNotIn('id', $keptIds ?: [0])->delete();
    }

    protected function resolveOptionReference($reference, array $optionIdsByIndex)
    {
        if (!filled($reference)) {
            return null;
        }

        if (str_starts_with((string) $reference, 'existing:')) {
            return (int) str_replace('existing:', '', $reference);
        }

        if (str_starts_with((string) $reference, 'new:')) {
            $index = (int) str_replace('new:', '', $reference);
            return $optionIdsByIndex[$index] ?? null;
        }

        return is_numeric($reference) ? (int) $reference : null;
    }

    protected function uploadThumbnail(Request $request, Product $product): void
    {
        $product->thumbnail = fileUploader(
            $request->thumbnail,
            getFilePath('productThumbnail') . '/' . $product->slug,
            getFileSize('productThumbnail'),
            $product->thumbnail ?? null
        );
    }

    protected function uploadPreviewImage(Request $request, Product $product): void
    {
        $product->preview_image = fileUploader(
            $request->preview_image,
            getFilePath('productPreview') . '/' . $product->slug,
            getFileSize('productPreview'),
            $product->preview_image ?? null
        );

        $product->inline_preview_image = fileUploader(
            $request->preview_image,
            getFilePath('productInlinePreview') . '/' . $product->slug,
            getFileSize('productInlinePreview'),
            $product->inline_preview_image ?? null
        );
    }

    protected function uploadScreenshots(Request $request, Product $product): void
    {
        $zipPath = $request->file('screenshots')->path();
        $extractedPath = getFilePath('screenshots') . '/' . $product->slug . '/screenshots';

        $zip = new \ZipArchive();
        $zip->open($zipPath);

        if (is_dir($extractedPath)) {
            fileManager()->removeDirectory($extractedPath);
        }

        fileManager()->makeDirectory($extractedPath);
        $zip->extractTo($extractedPath);
    }
}
