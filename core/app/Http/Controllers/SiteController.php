<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Plan;
use App\Models\ProductView;
use App\Models\Review;
use App\Models\Rating;
use App\Models\Subcategory;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller {
    public function index() {
        if (isset($_GET['reference'])) {
            session()->put('reference', $_GET['reference']);
        }

        $pageTitle   = 'Home';
        $sections    = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;

        return view('Template::home', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function pages($slug) {
        $page        = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle   = $page->name;
        $sections    = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function categories() {
        $pageTitle = "All Categories";
        $categories = Category::active()
            ->withCount([
                'products' => function ($query) {
                    $query->catalogPublished();
                },
            ])->having('products_count', '>', 0)
            ->orderByDesc('products_count')
            ->get();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'category')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;
        return view('Template::category', compact('pageTitle', 'sections', 'seoContents', 'seoImage', 'categories'));
    }
    public function plans() {
        $pageTitle   = "Membership Plan";
        $plans       = Plan::active()->orderBy('id')->get();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'plans')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;
        $contentData = getContent('membership_plan.content', true);
        $content     = $contentData ? $contentData->data_values : null;
        return view('Template::plan', compact('pageTitle', 'sections', 'seoContents', 'seoImage', 'content', 'plans'));
    }


    public function products() {
        $request = request();
        $pageTitle = "All Items";
        $query = Product::with(['author', 'activeOptions'])->catalogPublished();
        $products = $this->filterPorducts($query);

        // Handle AJAX
        if ($request->ajax()) {
            $view = view('Template::user.product.product_list', compact('products'))->render();
            $pagination = paginateLinks($products)->toHtml();
            return response()->json(['html' => $view, 'pagination' => $pagination]);
        }

        $ratings    = Rating::get();
        $categories = Category::active()->whereHas('products', fn($q) => $q->catalogPublished())->get();

        return view('Template::products', compact(
            'pageTitle',
            'categories',
            'products',
            'ratings'
        ));
    }

    public function categoryProducts($category, $subcategory = null) {
        $category = Category::where('slug', $category)->firstOrFail();
        $seoContents = $category->seo_content;
        $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;

        if ($subcategory) {
            $subcategory = Subcategory::where('slug', $subcategory)->firstOrFail();
            $pageTitle   = $subcategory->name;

            $seoContents = $subcategory->seo_content;
            $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;
        } else {
            $pageTitle   = $category->name;
        }

        $products = Product::with(['author', 'activeOptions'])->catalogPublished()
            ->where('category_id', $category->id)
            ->when($subcategory, function ($query) use ($subcategory) {
                $query->where('subcategory_id', $subcategory->id);
            });

        $products = $this->filterPorducts($products, true);

        // Handle AJAX
        if (request()->ajax()) {
            $view = view('Template::user.product.product_list', compact('products'))->render();
            $pagination = paginateLinks($products)->toHtml();
            return response()->json(['html' => $view, 'pagination' => $pagination]);
        }

        $ratings    = Rating::get();

        return view('Template::products', compact(
            'pageTitle',
            'products',
            'ratings',
            'seoContents',
            'seoImage',
        ));
    }

    private function filterPorducts($query, $isCategoryProduct = false) {
        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains("tags", $search)
                    ->orWhere("title", "like", "%{$search}%")
                    ->orWhereHas("author", function ($q2) use ($search) {
                        $q2->where("username", "like", "%{$search}%");
                    });
            });
        }

        if (!$isCategoryProduct) {
            if ($request->filled('category') && $request->category !== 'all') {
                $category = Category::where('slug', $request->category)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            if ($request->filled('subcategory')) {
                $subcategory = Subcategory::where('slug', $request->subcategory)->first();
                if ($subcategory) {
                    $query->where('subcategory_id', $subcategory->id);
                }
            }
        }

        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('avg_rating', $request->rating);
        }

        if ($request->filled('date_range') && $request->date_range !== 'all') {
            $query->where('created_at', '>=', now()->subDays($request->date_range));
        }

        // Sorting
        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;
            $query->when($sortBy === 'best_downloading', fn($q) => $q->orderByDesc('total_download'))
                ->when($sortBy === 'best_rated', fn($q) => $q->orderByDesc('avg_rating'))
                ->when($sortBy === 'new_item', fn($q) => $q->orderByDesc('created_at'));
        }

        return $query->with(['reviews', 'users', 'author', 'activeOptions'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate(12));
    }


    public function freeProducts() {
        $pageTitle = "Free Items";

        $categories  = Category::active()->whereHas('products', function ($query) {
            $query->storefrontFree();
        })->get();

        $products  = Product::with(['author', 'activeOptions'])->searchable(['title'])->storefrontFree()->orderBy('total_download', 'DESC')->paginate(getPaginate(16));
        return view('Template::free_products', compact('pageTitle', 'categories', 'products'));
    }

    public function productDetails($slug) {
        $product = Product::with(['author', 'activeOptions', 'activeFiles', 'category', 'subcategory'])->countComment()->where(['slug' => $slug])->firstOrFail();

        if (in_array($product->status, [Status::PRODUCT_PERMANENT_DOWN, Status::PRODUCT_HARD_REJECTED])) {
            abort(404);
        }

        if ($product->managed_by_admin && (!$product->is_published || $product->availability_status === Status::PRODUCT_AVAILABILITY_UNAVAILABLE)) {
            abort(404);
        }

        abort_if(!$product->my_product && $product->status != Status::PRODUCT_APPROVED, 404);

        $author                  = $product->author;
        $pageTitle               = "Item Description";

        $seoContents['keywords']           = $product->tags ?? '';
        $seoContents['social_title']       = $product->title;

        $cleanDescription = preg_replace("/\r|\n/", ' ', $product->description);
        $cleanDescription = strip_tags($cleanDescription);
        $shortDescription = strLimit($cleanDescription, 120);

        $seoContents['description']        = $shortDescription;
        $seoContents['social_description'] = $shortDescription;

        if ($product->user_id != auth()->id()) {
            $view = ProductView::where('product_id', $product->id)
                ->whereDate('views_date', today())
                ->first();

            if ($view) {
                $view->increment('views');
            } else {
                $productView = new ProductView();
                $productView->product_id = $product->id;
                $productView->views = 1;
                $productView->views_date = today();
                $productView->save();
            }
        }

        $seoContents = (object) $seoContents;
        $seoImage = $product->preview_image ? getImage(getFilePath('productPreview') . productFilePath($product, 'preview_image'), getFileSize('seo')) : null;

        return view('Template::product_details', compact('pageTitle', 'product', 'author', 'seoContents', 'seoImage'));
    }

    public function productReviews($slug) {
        $pageTitle = 'Item Reviews';
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();
        $this->abortIfAuxiliaryCatalogPageHidden($product);
        $reviews  = Review::where(['product_id' => $product->id])->with(['user', 'replies', 'category']);
        abort_if(($product->status == !Status::PRODUCT_APPROVED && !$product->my_product) || $reviews->count() < gs('min_reviews'), 404);
        $reviewId = request()->review_id;
        if ($reviewId) {
            $reviews->where('id', $reviewId);
        }

        $reviews   = $reviews->paginate(10);
        return view('Template::product_reviews', compact('pageTitle', 'product', 'reviews'));
    }

    public function getProductReview($slug) {
        $product = Product::where(['slug' => $slug])->firstOrFail();
        $this->abortIfAuxiliaryCatalogPageHidden($product);
        $reviews  = Review::where(['product_id' => $product->id])->with(['user', 'replies', 'category']);
        $reviewId = request()->review_id;
        if ($reviewId) {
            $reviews->where('id', $reviewId);
        }

        $reviews   = $reviews->paginate(10);

        $view = view('Template::user.product.review', compact('product', 'reviews'))->render();

        return response()->json([
            'reviews' => $view,
            'hasMorePages' => $reviews->hasMorePages()
        ]);
    }


    public function productComments($slug) {
        $pageTitle = 'Item Comments';
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();
        $this->abortIfAuxiliaryCatalogPageHidden($product);
        abort_if($product->status == !Status::PRODUCT_APPROVED && !$product->my_product, 404);

        $commentId = request()->comment_id;
        $comments  = Comment::where(['product_id' => $product->id, 'parent_id' => 0, 'review_id' => 0])
            ->when($commentId, function ($query) use ($commentId) {
                $query->where('id', $commentId);
            })
            ->with(['user', 'user.earnings', 'product', 'replies' => function ($replyQuery) {
                $replyQuery->with('user');
            }])->paginate(getPaginate(10));

        return view('Template::product_comments', compact('pageTitle', 'product', 'comments'));
    }


    public function getProductComment($slug) {
        $product = Product::where(['slug' => $slug])->firstOrFail();
        $this->abortIfAuxiliaryCatalogPageHidden($product);

        $comments = Comment::where(['product_id' => $product->id, 'parent_id' => 0, 'review_id' => 0])
            ->with(['user', 'user.earnings', 'product', 'replies' => function ($replyQuery) {
                $replyQuery->with('user');
            }])
            ->paginate(10);

        $view = view('Template::user.product.comment', compact('product', 'comments'))->render();

        return response()->json([
            'comments' => $view,
            'hasMorePages' => $comments->hasMorePages()
        ]);
    }


    public function productChangelog($slug) {
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();
        $this->abortIfAuxiliaryCatalogPageHidden($product);

        // Check various conditions before proceeding
        abort_if(
            $product->status != Status::PRODUCT_APPROVED
                && !$product->my_product
                || !gs('changelog')
                || $product->product_updated == Status::PRODUCT_UPDATE_PENDING
                || count($product->changelogs) == 0,
            404
        );

        $pageTitle = 'Item Changelog';

        return view('Template::product_changelog', compact('pageTitle', 'product'));
    }

    public function contact() {
        $pageTitle   = "Contact Us";
        $user        = auth()->user();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = $seoContents?->image ? getImage(getFilePath('seo') . '/' . $seoContents?->image, getFileSize('seo')) : null;
        return view('Template::contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }

    public function contactSubmit(Request $request) {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug) {
        $policy      = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle   = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage    = $seoContents?->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function cookieAccept() {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy() {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    protected function abortIfAuxiliaryCatalogPageHidden(Product $product): void
    {
        abort_if($product->isAdminOrderProduct(), 404);
    }

    public function placeholderImage($size = null) {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance() {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }

    public function incrementClick($id) {
        $ad = Advertisement::find($id);
        if ($ad) {
            $ad->click += 1;
            $ad->save();
        }
        return response()->json(['status' => 'Success', 'message' => 'Advertisement Clicked!']);
    }
}
