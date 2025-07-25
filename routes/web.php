<?php

use Illuminate\Support\Facades\Route;

// === IMPORT CONTROLLERS ===
// Đã sắp xếp lại và thêm DashboardController
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Client\MyOrderController;
use App\Http\Controllers\Client\ProductController as ClientProductController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE CÔNG KHAI ---
Route::get('/', [ClientController::class, 'index'])->name('home');
<<<<<<< HEAD
Route::get('/cart', [ControllersCartController::class, 'index'])->name('cart');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/update', [ControllersCartController::class, 'update'])->name('update');
    Route::post('/remove', [ControllersCartController::class, 'remove'])->name('remove');
    Route::post('/cart/remove', [ControllersCartController::class, 'remove'])->name('cart.remove');
    Route::post('/apply-discount', [OrderController::class, 'applyDiscount'])->name('apply-discount');
});

// Order
Route::prefix('order')->name('order.')->group(function () {
    Route::get('/', [ControllersOrderController::class, 'index'])->name('index');
    Route::post('/', [ControllersOrderController::class, 'store'])->name('store');
    Route::post('/order/store', [ControllersOrderController::class, 'store'])->name('order.store');
    Route::get('/order/success', [ControllersOrderController::class, 'success'])->name('success');
});

// Checkout
Route::prefix('checkout')->middleware('auth')->group(function () {
    Route::get('/', [ControllersCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [ControllersCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkouts/{id}', [ControllersCheckoutController::class, 'show'])->name('client.checkouts.show');
    Route::get('checkouts/{id}/cancel', [ControllersCheckoutController::class, 'cancel'])->name('client.checkouts.cancel');
    Route::get('checkouts/{id}/restore', [ControllersCheckoutController::class, 'restore'])->name('client.checkouts.restore');
});

// === ROUTE XÁC THỰC (Laravel Breeze) ===
require __DIR__ . '/auth.php';
=======

Route::get('/products/{product}', [ClientProductController::class, 'show'])->name('client.products.show');


Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/apply-discount', [OrderController::class, 'applyDiscount'])->name('apply-discount');
});

// --- ROUTE XÁC THỰC CỦA LARAVEL BREEZE ---
// Xử lý các trang /login, /register, /logout...
require __DIR__.'/auth.php';

>>>>>>> origin/main

// --- ROUTE CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (KHÔNG PHẢI ADMIN) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

<<<<<<< HEAD
    // Cho user thường xem order
    Route::resource('orders', OrderController::class);
=======

    Route::prefix('cart')->name('client.cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index'); // Tên: client.cart.index
        Route::post('/add', [CartController::class, 'add'])->name('add'); // Tên: client.cart.add
        Route::post('/update', [CartController::class, 'update'])->name('update'); // Tên: client.cart.update
        Route::get('/remove/{cartItemId}', [CartController::class, 'remove'])->name('remove'); 
    });

    Route::prefix('checkout')->name('client.checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('placeOrder');
    });

    Route::get('/my-orders', [MyOrderController::class, 'index'])->name('client.orders.index');
    Route::get('/my-orders/{order}', [MyOrderController::class, 'show'])->name('client.orders.show');
>>>>>>> origin/main
});


//======================================================================
// === TOÀN BỘ ROUTE CHO ADMIN (GỘP CHUNG VÀO MỘT NƠI) ===
//======================================================================
Route::group([
    'prefix' => 'admin',
    'as' => 'admins.',
    'middleware' => ['auth', 'check.admin']
], function () {

    // 1. DASHBOARD
    // Logic đã được chuyển vào DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. QUẢN LÝ CRUD
    // Sử dụng Route::resource cho tất cả các tài nguyên
    Route::resource('products', ProductController::class);
    // Dòng code sai đã được xóa khỏi đây.

    Route::resource('attributes', AttributeController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('categories', CategoryController::class);

    // 3. QUẢN LÝ ĐÁNH GIÁ (PRODUCT REVIEWS)
    // Vẫn dùng resource và bổ sung các route tùy chỉnh nếu cần
    Route::resource('reviews', ProductReviewController::class)->except(['create', 'edit', 'show']);
    Route::post('/reviews/{id}/reply', [ProductReviewController::class, 'reply'])->name('reviews.reply');
    Route::post('/reviews/{id}/toggle-hide', [ProductReviewController::class, 'toggleHide'])->name('reviews.toggleHide');


    // 4. QUẢN LÝ NGƯỜI DÙNG
    Route::get( 'users', [AuthController::class, 'listUser'])->name('users.index');
    Route::get('users/create', [AuthController::class, 'createUser'])->name('users.create');
    Route::post('users', [AuthController::class, 'storeUser'])->name('users.store');
    Route::get('users/{id}/edit', [AuthController::class, 'editUser'])->name('users.edit');
    Route::post('users/{id}', [AuthController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{id}', [AuthController::class, 'deleteUser'])->name('users.destroy');
});

// === ROUTE DEBUG ===
Route::get('/test-cache-driver', function () {
    return config('cache.default');
});
