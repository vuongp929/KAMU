<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

// === IMPORT CONTROLLERS ===
use App\Http\Controllers\PageController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Client\MyOrderController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Client\CheckoutController;

use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Client\RewardController;
use App\Http\Controllers\Client\PaymentController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE CÔNG KHAI ---
Route::get('/', [ClientController::class, 'index'])->name('home');
Route::get('/products/{product}', [ClientProductController::class, 'show'])->name('client.products.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/apply-discount', [DiscountController::class, 'applyDiscount'])->name('apply-discount')->middleware('auth');
});

// Trang giao hàng
Route::get('giao-hang', [PageController::class, 'giaoHang'])->name('giao-hang');
Route::get('dich-vu-goi-qua', [PageController::class, 'goiQua'])->name('dich-vu-goi-qua');
Route::get('cach-giat-gau-bong', [PageController::class, 'giatGau'])->name('cach-giat-gau-bong');
Route::get('chinh-sach-doi-tra', [PageController::class, 'doiTra'])->name('chinh-sach-doi-tra');
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist',                 [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add',            [WishlistController::class, 'addWishlist'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{id}',  [WishlistController::class, 'removeWishlist'])->name('wishlist.remove');
});
Route::get('/search', [App\Http\Controllers\Client\ProductController::class, 'search'])->name('clients.search');


// --- ROUTE XÁC THỰC (Laravel Breeze) ---
require __DIR__ . '/auth.php';


// --- ROUTE CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (KHÔNG PHẢI ADMIN) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Nếu bạn muốn cho user thường xem order → giữ dòng này
    Route::resource('orders', OrderController::class);
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::post('/products/{product}/reviews/{review}/reply', [\App\Http\Controllers\ProductReviewController::class, 'reply'])->name('products.reviews.reply');
    Route::delete('/products/{product}/reviews/{review}', [\App\Http\Controllers\ProductReviewController::class, 'destroy'])->name('products.reviews.destroy');
    Route::get('/orders/{order}/confirm', [MyOrderController::class, 'confirm'])
        ->name('client.orders.confirm')
        ->middleware('signed');


    Route::prefix('cart')->name('client.cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::post('/update', [CartController::class, 'update'])->name('update');
        Route::get('/remove/{cartItemId}', [CartController::class, 'remove'])->name('remove');
    });

    Route::prefix('checkout')->name('client.checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('placeOrder');
        Route::post('/validate-discount', [CheckoutController::class, 'validateDiscount'])->name('validateDiscount');
    });

    Route::get('/my-orders', [MyOrderController::class, 'index'])->name('client.orders.index');
    Route::get('/my-orders/{order}', [MyOrderController::class, 'show'])->name('client.orders.show');
    Route::post('/my-orders/{order}/complete', [MyOrderController::class, 'complete'])->name('client.orders.complete');

    // Routes cho điểm thưởng
    Route::prefix('rewards')->name('client.rewards.')->group(function () {
        // Route::get('/', [RewardController::class, 'index'])->name('index');
        // Route::post('/exchange', [RewardController::class, 'exchangePoints'])->name('exchange');
        // Route::get('/history', [RewardController::class, 'history'])->name('history');
        // Route::get('/discount-codes', [RewardController::class, 'discountCodes'])->name('discount-codes');
    });

    // Routes cho thanh toán
    Route::prefix('payment')->name('client.payment.')->group(function () {
        Route::post('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
        Route::post('/failed', [PaymentController::class, 'paymentFailed'])->name('failed');
        Route::post('/cod/{order}', [PaymentController::class, 'processCodPayment'])->name('cod');
    });

    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/history/{receiverId}', [ChatController::class, 'getHistory'])->name('chat.history');

    Route::post('/my-orders/{order}/cancel', [MyOrderController::class, 'cancel'])->name('client.orders.cancel');

});

// === ROUTE CHO ADMIN ===
Route::middleware(['auth', 'check.admin'])->prefix('admin')->group(function () {


    // 1. DASHBOARD
    // Tên đầy đủ được đặt trực tiếp ở đây
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // 2. QUẢN LÝ CRUD
    // Sử dụng ->name() để gán tiền tố tên cho resource routes
    Route::resource('products', ProductController::class)->names('admin.products');
    Route::resource('attributes', AttributeController::class)->names('admin.attributes');
    Route::resource('orders', OrderController::class)->names('admin.orders');
    Route::resource('discounts', DiscountController::class)->names('admin.discounts');
    Route::resource('categories', CategoryController::class)->names('admin.categories');

    // 3. QUẢN LÝ ĐÁNH GIÁ (PRODUCT REVIEWS)
    Route::resource('reviews', ProductReviewController::class)
        ->except(['create', 'edit', 'show'])
        ->names('admin.reviews'); // Đặt tên cho resource

    // Đặt tên đầy đủ cho các route tùy chỉnh
    Route::post('/reviews/{id}/reply', [ProductReviewController::class, 'reply'])->name('admin.reviews.reply');
    Route::post('/reviews/{id}/toggle-hide', [ProductReviewController::class, 'toggleHide'])->name('admin.reviews.toggleHide');

    // 4. QUẢN LÝ NGƯỜI DÙNG
    Route::get('users', [AuthController::class, 'listUser'])->name('admin.users.index');
    Route::get('users/create', [AuthController::class, 'createUser'])->name('admin.users.create');
    Route::post('users', [AuthController::class, 'storeUser'])->name('admin.users.store');
    Route::get('users/{id}/edit', [AuthController::class, 'editUser'])->name('admin.users.edit');
    Route::post('users/{id}', [AuthController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('users/{id}', [AuthController::class, 'deleteUser'])->name('admin.users.destroy');

    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::get('/chat/search-users', [AdminChatController::class, 'searchUsers'])->name('admin.chat.searchUsers');

    //Hóa đơn 
    Route::get('admin/invoices/{order}', [InvoiceController::class, 'show'])->name('admin.invoices.show');
});


Route::prefix('payment')->name('payment.')->group(function () {
    // VNPay Routes (nếu có)
    // Route::get('/vnpay/create', [PaymentController::class, 'createVnpay'])->name('vnpay.create')->middleware('auth');
    // Route::get('/vnpay/return', [PaymentController::class, 'returnVnpay'])->name('vnpay.return');

    // Momo Routes
    Route::get('/momo/create', [PaymentController::class, 'createMomo'])->name('momo.create')->middleware('auth'); // Chỉ người đã đăng nhập mới được tạo
    Route::get('/momo/return', [PaymentController::class, 'returnMomo'])->name('momo.return');
    Route::post('/momo/ipn', [PaymentController::class, 'ipnMomo'])->name('momo.ipn');

    Route::get('/vnpay/create', [ClientPaymentController::class, 'createVnpay'])->name('vnpay.create')->middleware('auth');
    
    // Route VNPAY
    Route::get('/vnpay/return', [ClientPaymentController::class, 'returnVnpay'])->name('vnpay.return');
    Route::get('/vnpay/ipn', [ClientPaymentController::class, 'ipnVnpay'])->name('vnpay.ipn');
    // Các trang thông báo chung
    Route::get('/success', function () { /* ... */ })->name('success');
    Route::get('/failed', function () { /* ... */ })->name('failed');
});
// === DEBUG ROUTE ===
Route::get('/test-cache-driver', function () {
    return config('cache.default');
});
