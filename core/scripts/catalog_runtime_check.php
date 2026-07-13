<?php

declare(strict_types=1);

use App\Constants\Status;
use App\Http\Controllers\Admin\CatalogProductController;
use App\Http\Controllers\Admin\ManageOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\User\DownloadController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductController;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductOption;
use App\Models\Subcategory;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class CatalogRuntimeCheck
{
    private array $results = [];
    private string $prefix;
    private string $slugBase;
    private string $tempDir;
    private ?User $buyer = null;
    private ?Category $category = null;
    private ?Subcategory $subcategory = null;
    private ?Form $form = null;
    private ?Product $downloadableProduct = null;
    private ?Product $optionProduct = null;
    private ?Product $legacyProduct = null;
    private ?ProductFile $downloadableFile = null;
    private ?ProductOption $optionChoice = null;
    private ?GatewayCurrency $gatewayCurrency = null;
    private ?Order $mixedOrder = null;
    private ?Order $downloadOrder = null;

    public function __construct()
    {
        $token = now()->format('YmdHis');
        $this->prefix = 'QA Catalog ' . $token;
        $this->slugBase = 'qa-catalog-' . strtolower(Str::random(8));
        $this->tempDir = storage_path('app/catalog-runtime-check');

        File::ensureDirectoryExists($this->tempDir);
        app('session')->start();
    }

    public function run(): int
    {
        try {
            $this->cleanupPreviousFixtures();
            $this->setupFixtures();
            $this->runChecks();
        } catch (Throwable $e) {
            $this->record('runtime.bootstrap', false, $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
        }

        $this->printReport();

        return $this->hasFailures() ? 1 : 0;
    }

    private function setupFixtures(): void
    {
        $this->buyer = User::where('email', 'qa.catalog.buyer@example.com')->first();
        if (!$this->buyer) {
            $this->buyer = new User();
            $this->buyer->firstname = 'QA';
            $this->buyer->lastname = 'Buyer';
            $this->buyer->username = 'qa_catalog_buyer';
            $this->buyer->email = 'qa.catalog.buyer@example.com';
            $this->buyer->dial_code = '1';
            $this->buyer->country_code = 'US';
            $this->buyer->mobile = '5550101234';
            $this->buyer->country_name = 'United States';
            $this->buyer->city = 'QA City';
            $this->buyer->state = 'QA State';
            $this->buyer->zip = '10001';
            $this->buyer->address = 'QA Street';
            $this->buyer->password = Hash::make('Password123!');
            $this->buyer->status = Status::USER_ACTIVE;
            $this->buyer->kv = Status::KYC_VERIFIED;
            $this->buyer->ev = Status::VERIFIED;
            $this->buyer->sv = Status::VERIFIED;
            $this->buyer->profile_complete = Status::YES;
            $this->buyer->tv = Status::YES;
            $this->buyer->is_author = Status::NO;
            $this->buyer->is_author_featured = Status::NO;
            $this->buyer->ref_by = 0;
            $this->buyer->save();
        }

        $this->form = new Form();
        $this->form->act = 'subcategory_attributes';
        $this->form->form_data = [
            'contact_details' => [
                'name'        => 'Contact Details',
                'label'       => 'contact_details',
                'is_required' => 'required',
                'instruction' => '',
                'extensions'  => '',
                'options'     => [],
                'type'        => 'text',
                'width'       => '6',
            ],
            'service_label' => [
                'name'        => 'Service Label',
                'label'       => 'service_label',
                'is_required' => 'required',
                'instruction' => '',
                'extensions'  => '',
                'options'     => [],
                'type'        => 'text',
                'width'       => '6',
            ],
            'stock_limit' => [
                'name'        => 'Stock Limit',
                'label'       => 'stock_limit',
                'is_required' => 'required',
                'instruction' => '',
                'extensions'  => '',
                'options'     => [],
                'type'        => 'number',
                'width'       => '6',
            ],
        ];
        $this->form->save();

        $this->category = new Category();
        $this->category->name = $this->prefix . ' Category';
        $this->category->slug = $this->slugBase . '-category';
        $this->category->author_commission = 0;
        $this->category->featured = 0;
        $this->category->status = Status::ENABLE;
        $this->category->save();

        $this->subcategory = new Subcategory();
        $this->subcategory->name = $this->prefix . ' Subcategory';
        $this->subcategory->slug = $this->slugBase . '-subcategory';
        $this->subcategory->category_id = $this->category->id;
        $this->subcategory->form_id = $this->form->id;
        $this->subcategory->status = Status::ENABLE;
        $this->subcategory->save();

        $this->gatewayCurrency = GatewayCurrency::whereHas('method', function ($query) {
            $query->where('status', Status::ENABLE);
        })->orderBy('id')->first();

        $this->createAdminManagedProducts();
        $this->setupLegacyProduct();
    }

    private function runChecks(): void
    {
        $this->testUploadRouteAccess();
        $this->testAdminProductsCreated();
        $this->testProductRendering();
        $this->testFileReplacement();
        $this->testMixedOrderFlow();
        $this->testDownloadableOrderFlow();
        $this->testLegacyDownloadFlow();
        $this->testAdminOrderScreens();
    }

    private function testUploadRouteAccess(): void
    {
        $author = User::find(6);

        $authorCategoryResponse = $this->dispatchGet(route('user.product.upload.category'), $author);
        $authorUploadResponse = $this->dispatchGet(route('user.product.upload', [
            'category' => $this->category->id,
            'subcategory' => $this->subcategory->id,
        ]), $author);
        $this->record(
            'author.download.upload.available',
            $authorCategoryResponse->getStatusCode() === 200
                && $authorUploadResponse->getStatusCode() === 200
                && str_contains($authorUploadResponse->getContent(), 'Main File'),
            'Author should be able to open downloadable upload flow'
        );

        $buyerResponse = $this->dispatchGet(route('user.product.upload'), $this->buyer);
        $buyerLocation = $buyerResponse->headers->get('Location', '');
        $this->record(
            'buyer.upload.url.blocked',
            $buyerResponse->isRedirect() && (str_contains($buyerLocation, route('user.author.form', [], false)) || str_contains($buyerLocation, route('home', [], false))),
            'Non-author user should not access upload URL'
        );

        $productController = app(ProductController::class);
        $request = Request::create(route('user.product.upload', [
            'category' => $this->category->id,
            'subcategory' => $this->subcategory->id,
        ]), 'GET');
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => $author);
        app()->instance('request', $request);
        $viewResponse = $productController->upload();
        $this->record(
            'upload.controller.renders.form',
            method_exists($viewResponse, 'render'),
            'Upload controller should render author downloadable upload form'
        );
    }

    private function testAdminProductsCreated(): void
    {
        $downloadable = $this->downloadableProduct?->fresh(['files']);
        $optionProduct = $this->optionProduct?->fresh(['options']);

        $this->record(
            'admin.downloadable.product.created',
            $downloadable !== null
                && (bool) $downloadable->managed_by_admin
                && (bool) $downloadable->is_published
                && $downloadable->product_type === Status::PRODUCT_TYPE_DOWNLOADABLE
                && $downloadable->files->count() > 0,
            'Admin downloadable product should be published with active file records'
        );

        $this->record(
            'admin.option.product.created',
            $optionProduct !== null
                && (bool) $optionProduct->managed_by_admin
                && (bool) $optionProduct->is_published
                && $optionProduct->product_type === Status::PRODUCT_TYPE_OPTION_REQUEST
                && $optionProduct->options->count() >= 2,
            'Admin option-request product should be published with active options'
        );

        $attributeInfo = collect($optionProduct?->attribute_info ?? []);
        $labels = $attributeInfo->pluck('name')->all();
        $this->record(
            'dynamic.fields.saved',
            in_array('Contact Details', $labels, true) && in_array('Stock Limit', $labels, true),
            'Subcategory dynamic fields should be stored inside attribute_info'
        );
    }

    private function testProductRendering(): void
    {
        Auth::guard('web')->logout();

        $productsHtml = $this->renderView(function () {
            return app(SiteController::class)->products();
        }, route('products'));

        $this->record(
            'products.list.renders.managed.items',
            str_contains($productsHtml, $this->downloadableProduct->title)
                && str_contains($productsHtml, $this->optionProduct->title)
                && str_contains($productsHtml, $this->legacyProduct->title),
            'Products page should list admin order products and author downloadable items'
        );

        $this->record(
            'products.list.cta.labels',
            str_contains($productsHtml, 'Buy Now') && str_contains($productsHtml, 'Select Options'),
            'List page should show Buy Now for fixed-price items and Select Options for variable items'
        );

        $categoryHtml = $this->renderView(function () {
            return app(SiteController::class)->categoryProducts($this->category->slug, $this->subcategory->slug);
        }, route('category.products', [$this->category->slug, $this->subcategory->slug]));

        $this->record(
            'category.page.renders.both.items',
            str_contains($categoryHtml, $this->downloadableProduct->title)
                && str_contains($categoryHtml, $this->optionProduct->title)
                && str_contains($categoryHtml, $this->legacyProduct->title),
            'Category/subcategory page should render order products and author downloadable items'
        );

        $detailHtml = $this->renderView(function () {
            return app(SiteController::class)->productDetails($this->optionProduct->slug);
        }, route('product.details', $this->optionProduct->slug));

        $this->record(
            'option.detail.selector.visible',
            str_contains($detailHtml, 'Choose an option')
                && str_contains($detailHtml, $this->optionChoice->name)
                && str_contains($detailHtml, showAmount($this->optionChoice->price)),
            'Option product details should render selector with option names and visible pricing'
        );

        $this->record(
            'option.detail.requested.amount.visible',
            str_contains($detailHtml, 'Requested Amount'),
            'Option product details should render requested amount field for ranged options'
        );

        $this->record(
            'option.detail.cleaned.layout',
            str_contains($detailHtml, 'Continue to Checkout')
                && !str_contains($detailHtml, route('product.reviews', $this->optionProduct->slug))
                && !str_contains($detailHtml, route('product.comments', $this->optionProduct->slug))
                && !str_contains($detailHtml, 'Live Preview'),
            'Order product detail should focus on checkout without review, comment, or live preview links'
        );

        $this->record(
            'detail.dynamic.fields.visible',
            str_contains($detailHtml, 'Contact Details') && str_contains($detailHtml, 'Stock Limit'),
            'Dynamic attribute fields should render on product details'
        );

        $downloadHtml = $this->renderView(function () {
            return app(SiteController::class)->productDetails($this->downloadableProduct->slug);
        }, route('product.details', $this->downloadableProduct->slug));

        $this->record(
            'managed.detail.hides.legacy.download',
            !str_contains($downloadHtml, route('user.product.download', $this->downloadableProduct->slug)),
            'Managed product detail page should not expose legacy direct download route'
        );
    }

    private function testFileReplacement(): void
    {
        $product = $this->downloadableProduct->fresh(['files']);
        $existingFile = $product->files->first();
        $oldStoredName = $existingFile?->stored_name;

        $thumb = $this->makeUploadedImage('replace-thumb.png');
        $preview = $this->makeUploadedImage('replace-preview.png');
        $screenshots = $this->makeUploadedZip('replace-screenshots.zip');
        $replacement = $this->makeUploadedText('replaced-download.txt', 'updated file body');

        $request = $this->makeRequest('POST', route('admin.catalog.products.update', $product->id), [
            'title'               => $product->title,
            'description'         => '<p>Updated downloadable description</p>',
            'category_id'         => $this->category->id,
            'subcategory_id'      => $this->subcategory->id,
            'product_type'        => Status::PRODUCT_TYPE_DOWNLOADABLE,
            'availability_status' => Status::PRODUCT_AVAILABILITY_AVAILABLE,
            'base_price'          => '19',
            'is_published'        => '1',
            'tags'                => ['qa', 'replacement'],
            'contact_details'     => 'Telegram: @qa_support',
            'service_label'       => 'Updated File',
            'stock_limit'         => '9',
            'catalog_files'       => [
                [
                    'id'               => $existingFile?->id,
                    'display_name'     => 'Replaced Download.txt',
                    'option_reference' => '',
                    'sort_order'       => '0',
                    'is_active'        => '1',
                ],
            ],
        ], [
            'thumbnail'     => $thumb,
            'preview_image' => $preview,
            'screenshots'   => $screenshots,
            'catalog_files' => [
                [
                    'file' => $replacement,
                ],
            ],
        ]);

        $response = app(CatalogProductController::class)->update($request, $product->id);
        $product = $product->fresh(['files']);
        $updatedFile = $product->files->first();
        $this->downloadableProduct = $product;
        $this->downloadableFile = $updatedFile;

        $this->record(
            'admin.file.replace.works',
            $response instanceof RedirectResponse
                && $updatedFile !== null
                && $updatedFile->stored_name !== $oldStoredName
                && $updatedFile->display_name === 'Replaced Download.txt',
            'Admin should be able to replace downloadable file and future downloads should point to the latest active file'
        );
    }

    private function testMixedOrderFlow(): void
    {
        $this->loginUser($this->buyer);
        \App\Lib\CatalogCart::clear();

        $addOptionRequest = $this->makeRequest('POST', route('cart.add', $this->optionProduct->slug), [
            'quantity'          => 1,
            'product_option_id' => $this->optionChoice->id,
            'requested_amount'  => 3,
            'request_note'      => 'Need the first 3 units',
            'redirect_to'       => 'checkout',
        ]);

        $responseA = app(CartController::class)->add($addOptionRequest, $this->optionProduct->slug);

        $addDownloadable = $this->makeRequest('POST', route('cart.add', $this->downloadableProduct->slug), [
            'quantity' => 1,
        ]);
        $responseB = app(CartController::class)->add($addDownloadable, $this->downloadableProduct->slug);

        $cartItems = \App\Lib\CatalogCart::items();
        $this->record(
            'cart.accepts.both.product.types',
            $responseA instanceof RedirectResponse
                && $responseB instanceof RedirectResponse
                && $cartItems->count() === 2,
            'Cart should accept both option-request and downloadable products'
        );

        $this->record(
            'order.product.redirects.to.checkout',
            $responseA instanceof RedirectResponse
                && $responseA->getTargetUrl() === route('cart.checkout'),
            'Order product add-to-cart flow should redirect directly to checkout when requested'
        );

        $placeOrderRequest = $this->makeRequest('POST', route('cart.checkout.submit'), [
            'customer_note' => 'QA mixed order',
        ]);
        $placeOrderResponse = app(CartController::class)->placeOrder($placeOrderRequest);
        $this->mixedOrder = Order::where('user_id', $this->buyer->id)->latest('id')->with('items.product')->first();

        $downloadItem = $this->mixedOrder?->items->firstWhere('delivery_type', Status::PRODUCT_TYPE_DOWNLOADABLE);
        $optionItem = $this->mixedOrder?->items->firstWhere('delivery_type', Status::PRODUCT_TYPE_OPTION_REQUEST);

        $this->record(
            'checkout.creates.unpaid.order',
            $placeOrderResponse instanceof RedirectResponse
                && $this->mixedOrder !== null
                && $this->mixedOrder->status === Status::CATALOG_ORDER_PENDING_PAYMENT
                && $this->mixedOrder->items->count() === 2,
            'Checkout should create unpaid order with order items'
        );

        $this->record(
            'option.item.snapshots.detail',
            $optionItem !== null
                && (float) optional($optionItem->detail)->requested_amount === 3.0
                && optional($optionItem->detail)->request_note === 'Need the first 3 units'
                && (float) optional($optionItem->detail)->min_amount === 1.0
                && (float) optional($optionItem->detail)->max_amount === 5.0,
            'Option order item should snapshot request note and min/max related values'
        );

        $blockedDownload = false;
        try {
            app(OrderController::class)->downloadFile($downloadItem->id, $this->downloadableFile->id);
        } catch (Throwable $e) {
            $blockedDownload = true;
        }

        $this->record(
            'unpaid.download.blocked',
            $blockedDownload,
            'Unpaid orders must not expose downloadable files'
        );

        $depositResponse = app(PaymentController::class)->depositInsert($this->makeRequest('POST', route('user.deposit.insert'), [
            'gateway'  => (string) $this->gatewayCurrency->method_code,
            'currency' => $this->gatewayCurrency->currency,
            'order_id' => $this->mixedOrder->id,
        ]));

        $deposit = Deposit::where('order_id', $this->mixedOrder->id)->latest('id')->first();
        PaymentController::userDataUpdate($deposit->fresh());
        $this->mixedOrder->refresh();

        $this->record(
            'payment.creates.deposit.for.order',
            $depositResponse instanceof RedirectResponse && $deposit !== null,
            'Order payment should create a linked deposit record'
        );

        $this->record(
            'mixed.order.moves.to.processing.after.payment',
            $this->mixedOrder->status === Status::CATALOG_ORDER_PROCESSING && $this->mixedOrder->paid_at !== null,
            'Mixed delivery orders should become processing after payment success'
        );

        $orderHtml = $this->renderView(function () {
            return app(OrderController::class)->show($this->mixedOrder->id);
        }, route('user.orders.show', $this->mixedOrder->id), $this->buyer);

        $this->record(
            'buyer.order.page.visible.after.checkout',
            str_contains($orderHtml, $this->mixedOrder->order_number),
            'Buyer should see order detail page after order creation'
        );
    }

    private function testDownloadableOrderFlow(): void
    {
        $this->loginUser($this->buyer);
        \App\Lib\CatalogCart::clear();

        app(CartController::class)->add($this->makeRequest('POST', route('cart.add', $this->downloadableProduct->slug), [
            'quantity' => 1,
        ]), $this->downloadableProduct->slug);

        app(CartController::class)->placeOrder($this->makeRequest('POST', route('cart.checkout.submit'), [
            'customer_note' => 'QA download only order',
        ]));

        $this->downloadOrder = Order::where('user_id', $this->buyer->id)->latest('id')->with('items')->first();
        $depositRequest = $this->makeRequest('POST', route('user.deposit.insert'), [
            'gateway'  => (string) $this->gatewayCurrency->method_code,
            'currency' => $this->gatewayCurrency->currency,
            'order_id' => $this->downloadOrder->id,
        ]);
        app(PaymentController::class)->depositInsert($depositRequest);
        $deposit = Deposit::where('order_id', $this->downloadOrder->id)->latest('id')->first();
        PaymentController::userDataUpdate($deposit->fresh());
        $this->downloadOrder->refresh();

        $item = $this->downloadOrder->items->first();
        $response = app(OrderController::class)->downloadFile($item->id, $this->downloadableFile->id);
        $item->refresh();

        $this->record(
            'download.only.order.completed.after.payment',
            $this->downloadOrder->status === Status::CATALOG_ORDER_COMPLETED,
            'Download-only order should auto-complete after payment success'
        );

        $this->record(
            'paid.download.available',
            $response instanceof BinaryFileResponse && $response->getStatusCode() === 200,
            'Paid downloadable order should return a file download response'
        );

        $this->record(
            'order.download.counter.updates',
            (int) $item->download_count === 1 && $item->last_downloaded_at !== null,
            'Paid download should increment order item download count'
        );
    }

    private function testLegacyDownloadFlow(): void
    {
        $legacyAuthor = User::find($this->legacyProduct->user_id);

        UserPlan::where('user_id', $this->buyer->id)->where('plan_id', 1)->delete();
        $planId = Plan::query()->value('id');

        $userPlan = new UserPlan();
        $userPlan->user_id = $this->buyer->id;
        $userPlan->plan_id = $planId;
        $userPlan->plan_duration = Status::MONTHLY_PLAN;
        $userPlan->status = Status::PLAN_ACTIVE;
        $userPlan->is_payment = Status::PAID_SUBSCRIPTION;
        $userPlan->save();

        $beforeCount = (int) $this->legacyProduct->total_download;
        $beforeAuthorBalance = (float) $legacyAuthor->balance;

        $this->loginUser($this->buyer);
        $response = app(DownloadController::class)->downloadProduct($this->legacyProduct->slug);
        $this->legacyProduct->refresh();
        $legacyAuthor->refresh();

        $this->record(
            'legacy.download.flow.still.works',
            $response instanceof BinaryFileResponse && $response->getStatusCode() === 200,
            'Legacy membership-based download flow should keep working'
        );

        $this->record(
            'legacy.download.stats.increment',
            (int) $this->legacyProduct->total_download === $beforeCount + 1 && (float) $legacyAuthor->balance >= $beforeAuthorBalance,
            'Legacy download should continue updating download counters and author commission'
        );
    }

    private function testAdminOrderScreens(): void
    {
        $indexHtml = $this->renderAdminView(function () {
            return app(ManageOrderController::class)->index();
        }, route('admin.orders.index'));

        $showHtml = $this->renderAdminView(function () {
            return app(ManageOrderController::class)->show($this->mixedOrder->id);
        }, route('admin.orders.show', $this->mixedOrder->id));

        $this->record(
            'admin.orders.index.visible',
            str_contains($indexHtml, 'Catalog Orders') && str_contains($indexHtml, $this->mixedOrder->order_number),
            'Admin order index should render catalog orders list'
        );

        $this->record(
            'admin.orders.show.visible',
            str_contains($showHtml, $this->mixedOrder->order_number) && str_contains($showHtml, 'Update Order'),
            'Admin should be able to open order details and fulfillment controls'
        );
    }

    private function createAdminManagedProducts(): void
    {
        $thumb = $this->makeUploadedImage('download-thumb.png');
        $preview = $this->makeUploadedImage('download-preview.png');
        $screenshots = $this->makeUploadedZip('download-screenshots.zip');
        $downloadFile = $this->makeUploadedText('download-package.txt', 'catalog download package');

        $downloadTitle = $this->prefix . ' Downloadable';
        $downloadRequest = $this->makeRequest('POST', route('admin.catalog.products.store'), [
            'title'               => $downloadTitle,
            'description'         => '<p>Downloadable catalog product description</p>',
            'category_id'         => $this->category->id,
            'subcategory_id'      => $this->subcategory->id,
            'product_type'        => Status::PRODUCT_TYPE_DOWNLOADABLE,
            'availability_status' => Status::PRODUCT_AVAILABILITY_AVAILABLE,
            'base_price'          => '15',
            'is_published'        => '1',
            'tags'                => ['download', 'qa'],
            'contact_details'     => 'Telegram: @qa_support',
            'service_label'       => 'Instant File',
            'stock_limit'         => '7',
            'catalog_files'       => [
                [
                    'display_name'     => 'QA Download.txt',
                    'option_reference' => '',
                    'sort_order'       => '0',
                    'is_active'        => '1',
                ],
            ],
        ], [
            'thumbnail'     => $thumb,
            'preview_image' => $preview,
            'screenshots'   => $screenshots,
            'catalog_files' => [
                [
                    'file' => $downloadFile,
                ],
            ],
        ]);

        app(CatalogProductController::class)->store($downloadRequest);
        $this->downloadableProduct = Product::where('title', $downloadTitle)->with('files')->latest('id')->first();
        $this->downloadableFile = $this->downloadableProduct?->files->first();

        $optionThumb = $this->makeUploadedImage('option-thumb.png');
        $optionPreview = $this->makeUploadedImage('option-preview.png');
        $optionScreens = $this->makeUploadedZip('option-screenshots.zip');
        $optionTitle = $this->prefix . ' Option Request';

        $optionRequest = $this->makeRequest('POST', route('admin.catalog.products.store'), [
            'title'               => $optionTitle,
            'description'         => '<p>Option request product description</p>',
            'category_id'         => $this->category->id,
            'subcategory_id'      => $this->subcategory->id,
            'product_type'        => Status::PRODUCT_TYPE_OPTION_REQUEST,
            'availability_status' => Status::PRODUCT_AVAILABILITY_LIMITED,
            'base_price'          => '0',
            'is_published'        => '1',
            'tags'                => ['service', 'qa'],
            'contact_details'     => 'WhatsApp: +1 555 010 2024',
            'service_label'       => 'Managed Service',
            'stock_limit'         => '20',
            'options'             => [
                [
                    'name'              => 'Starter Batch',
                    'price'             => '25',
                    'min_amount'        => '1',
                    'max_amount'        => '5',
                    'availability_note' => 'Available for 1 to 5 units',
                    'sort_order'        => '0',
                    'is_active'         => '1',
                ],
                [
                    'name'              => 'Pro Batch',
                    'price'             => '40',
                    'min_amount'        => '5',
                    'max_amount'        => '10',
                    'availability_note' => 'Available for 5 to 10 units',
                    'sort_order'        => '1',
                    'is_active'         => '1',
                ],
            ],
        ], [
            'thumbnail'     => $optionThumb,
            'preview_image' => $optionPreview,
            'screenshots'   => $optionScreens,
        ]);

        app(CatalogProductController::class)->store($optionRequest);
        $this->optionProduct = Product::where('title', $optionTitle)->with('options')->latest('id')->first();
        $this->optionChoice = $this->optionProduct?->options()->orderBy('id')->first();
    }

    private function setupLegacyProduct(): void
    {
        $product = Product::where('managed_by_admin', Status::NO)->where('status', Status::PRODUCT_APPROVED)->orderBy('id')->first();

        if ($product && $product->file && file_exists(getFilePath('productFile') . '/' . $product->slug . '/' . $product->file)) {
            $this->legacyProduct = $product;
            return;
        }

        $legacyAuthor = User::find(9) ?? User::find(6);
        $legacyTitle = $this->prefix . ' Legacy Download';
        $legacySlug = Str::slug($legacyTitle) . '-' . strtolower(Str::random(4));
        $legacyFileName = 'legacy-download.zip';
        $legacyPath = getFilePath('productFile') . '/' . $legacySlug;
        File::ensureDirectoryExists($legacyPath);
        File::copy($this->createZipPath('legacy-download.zip'), $legacyPath . '/' . $legacyFileName);

        $product = new Product();
        $product->user_id = $legacyAuthor->id;
        $product->category_id = $this->category->id;
        $product->subcategory_id = $this->subcategory->id;
        $product->title = $legacyTitle;
        $product->slug = $legacySlug;
        $product->description = '<p>Legacy plan-based download product</p>';
        $product->status = Status::PRODUCT_APPROVED;
        $product->is_free = Status::NO;
        $product->managed_by_admin = Status::NO;
        $product->product_type = Status::PRODUCT_TYPE_DOWNLOADABLE;
        $product->is_published = Status::NO;
        $product->availability_status = Status::PRODUCT_AVAILABILITY_AVAILABLE;
        $product->base_price = 0;
        $product->file = $legacyFileName;
        $product->thumbnail = $this->copyFixtureImageToProduct($legacySlug, 'legacy-thumb.png', 'productThumbnail');
        $product->preview_image = $this->copyFixtureImageToProduct($legacySlug, 'legacy-preview.png', 'productPreview');
        $product->inline_preview_image = $this->copyFixtureImageToProduct($legacySlug, 'legacy-inline.png', 'productInlinePreview');
        $product->published_at = now();
        $product->last_updated = now();
        $product->tags = ['legacy', 'qa'];
        $product->save();

        $this->legacyProduct = $product;
    }

    private function makeRequest(string $method, string $uri, array $data = [], array $files = [], ?User $user = null): Request
    {
        if ($user) {
            $this->loginUser($user);
        }

        $request = Request::create($uri, $method, $data, [], $files);
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => Auth::guard('web')->user());
        app()->instance('request', $request);

        return $request;
    }

    private function dispatchGet(string $uri, ?User $user = null)
    {
        if ($user) {
            $this->loginUser($user);
        } else {
            Auth::guard('web')->logout();
        }

        $request = Request::create($uri, 'GET');
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => Auth::guard('web')->user());
        app()->instance('request', $request);

        return app(HttpKernel::class)->handle($request);
    }

    private function renderView(callable $callback, string $uri, ?User $user = null): string
    {
        $request = $this->makeRequest('GET', $uri, [], [], $user);
        app()->instance('request', $request);
        $response = $callback();

        return method_exists($response, 'render') ? $response->render() : (string) $response;
    }

    private function renderAdminView(callable $callback, string $uri): string
    {
        $admin = \App\Models\Admin::first();
        if ($admin) {
            Auth::guard('admin')->login($admin);
        }

        $request = Request::create($uri, 'GET');
        $request->setLaravelSession(app('session.store'));
        app()->instance('request', $request);
        $response = $callback();

        return method_exists($response, 'render') ? $response->render() : (string) $response;
    }

    private function loginUser(User $user): void
    {
        Auth::guard('web')->login($user);
        app('session')->put(Auth::guard('web')->getName(), $user->getAuthIdentifier());
    }

    private function makeUploadedImage(string $name): UploadedFile
    {
        $path = $this->tempDir . '/' . $name;
        $image = imagecreatetruecolor(20, 20);
        $background = imagecolorallocate($image, 52, 152, 219);
        imagefill($image, 0, 0, $background);
        imagepng($image, $path);
        imagedestroy($image);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function makeUploadedText(string $name, string $contents): UploadedFile
    {
        $path = $this->tempDir . '/' . $name;
        file_put_contents($path, $contents);

        return new UploadedFile($path, $name, 'text/plain', null, true);
    }

    private function makeUploadedZip(string $name): UploadedFile
    {
        $path = $this->createZipPath($name);
        return new UploadedFile($path, $name, 'application/zip', null, true);
    }

    private function createZipPath(string $name): string
    {
        $path = $this->tempDir . '/' . $name;
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('readme.txt', 'QA zip payload');
        $zip->close();

        return $path;
    }

    private function copyFixtureImageToProduct(string $slug, string $name, string $fileKey): string
    {
        $source = $this->tempDir . '/' . $name;
        $image = imagecreatetruecolor(20, 20);
        $background = imagecolorallocate($image, 46, 204, 113);
        imagefill($image, 0, 0, $background);
        imagepng($image, $source);
        imagedestroy($image);

        $targetDir = getFilePath($fileKey) . '/' . $slug;
        File::ensureDirectoryExists($targetDir);
        File::copy($source, $targetDir . '/' . $name);

        return $name;
    }

    private function cleanupPreviousFixtures(): void
    {
        $buyer = $this->buyer ?? User::where('email', 'qa.catalog.buyer@example.com')->first();

        $oldProducts = Product::where('title', 'like', 'QA Catalog %')->get();

        foreach ($oldProducts as $product) {
            ProductFile::where('product_id', $product->id)->delete();
            ProductOption::where('product_id', $product->id)->delete();
            foreach (['productFile', 'productThumbnail', 'productPreview', 'productInlinePreview', 'screenshots'] as $fileKey) {
                $path = getFilePath($fileKey) . '/' . $product->slug;
                if (is_dir($path)) {
                    File::deleteDirectory($path);
                }
            }
        }

        $oldProductIds = $oldProducts->pluck('id');
        $oldOrderIds = Order::whereHas('items', function ($query) use ($oldProductIds) {
            $query->whereIn('product_id', $oldProductIds);
        })->pluck('id');

        Deposit::whereIn('order_id', $oldOrderIds)->delete();
        Transaction::where('remark', 'catalog_purchase')->whereIn('user_id', [$buyer?->id ?? 0])->delete();
        \App\Models\AdminNotification::where('title', 'like', 'Payment successful via %')->delete();
        \App\Models\DownloadLog::where('user_id', optional($buyer)->id)->delete();
        \App\Models\Earning::where('user_id', optional($buyer)->id)->delete();
        \App\Models\PlanHistory::where('remark', 'purchase')->orWhere('remark', 'commission')->orWhere('remark', 'level_commission')->delete();
        \App\Models\UserPlan::where('user_id', optional($buyer)->id)->delete();

        \App\Models\OrderItem::whereIn('order_id', $oldOrderIds)->delete();
        Order::whereIn('id', $oldOrderIds)->delete();
        Product::whereIn('id', $oldProductIds)->delete();

        Form::where('created_at', '>=', now()->subDay())->where('act', 'subcategory_attributes')->where(function ($query) {
            $query->whereJsonContains('form_data->contact_details->name', 'Contact Details')
                ->orWhereJsonContains('form_data->service_label->name', 'Service Label');
        })->delete();

        Category::where('name', 'like', 'QA Catalog %')->delete();
        Subcategory::where('name', 'like', 'QA Catalog %')->delete();
    }

    private function record(string $key, bool $passed, string $detail): void
    {
        $this->results[] = [
            'key'    => $key,
            'status' => $passed ? 'PASS' : 'FAIL',
            'detail' => $detail,
        ];
    }

    private function hasFailures(): bool
    {
        return collect($this->results)->contains(fn ($result) => $result['status'] === 'FAIL');
    }

    private function printReport(): void
    {
        $passed = 0;
        $failed = 0;

        foreach ($this->results as $result) {
            echo sprintf("[%s] %s - %s\n", $result['status'], $result['key'], $result['detail']);
            if ($result['status'] === 'PASS') {
                $passed++;
            } else {
                $failed++;
            }
        }

        echo "\nSummary: {$passed} passed, {$failed} failed\n";

        if ($this->downloadableProduct) {
            echo "Downloadable QA product: {$this->downloadableProduct->title} ({$this->downloadableProduct->slug})\n";
        }

        if ($this->optionProduct) {
            echo "Option QA product: {$this->optionProduct->title} ({$this->optionProduct->slug})\n";
        }

        if ($this->buyer) {
            echo "QA buyer user: {$this->buyer->email}\n";
        }
    }
}

$checker = new CatalogRuntimeCheck();
exit($checker->run());
