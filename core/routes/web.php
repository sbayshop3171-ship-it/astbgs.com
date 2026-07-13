<?php

use Illuminate\Support\Facades\Route;

Route::redirect('activate', '/');
Route::any('activate_system_submit', fn () => redirect('/'))
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('favicon.ico', function () {
    $icon = public_path('assets/images/logo_icon/favicon.png');

    abort_unless(is_file($icon), 404);

    return response()->file($icon, [
        'Cache-Control' => 'public, max-age=86400',
        'Content-Type' => 'image/png',
    ]);
});

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');

// Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('contact', 'contact')->name('contact');
    Route::post('contact', 'contactSubmit');
    Route::get('categories', 'categories')->name('categories');
    Route::get('plans', 'plans')->name('plans');
    Route::get('products', 'products')->name('products');
    Route::get('products/{category}/{subcategory?}', 'categoryProducts')->name('category.products');

    Route::get('free-products', 'freeProducts')->name('free.products');
    Route::get('product/{slug}', 'productDetails')->name('product.details');
    Route::get('product/{slug}/reviews', 'productReviews')->name('product.reviews');
    Route::get('get/product/{slug}/reviews', 'getProductReview')->name('get.product.reviews');
    Route::get('product/{slug}/comments', 'productComments')->name('product.comments');
    Route::get('get/product/{slug}/comments', 'getProductComment')->name('get.product.comments');
    Route::get('product/{slug}/changelog', 'productChangelog')->name('product.changelog');
    Route::get('change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::post('click/{id}', 'incrementClick')->name('advertisement.click');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
