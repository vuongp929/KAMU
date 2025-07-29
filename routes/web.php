<?php

use Illuminate\Support\Facades\Route;

// === IMPORT CONTROLLERS ===
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
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE CÔNG KHAI ---
Route::get('/', [ClientController::class, 'index'])->name('home');
Route::get('/products/{product}', [ClientProductController::class, 'show'])->name('client.products.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/apply-discount', [OrderController::class, 'applyDiscount'])->name('apply-discount');
});

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
    });

    Route::get('/my-orders', [MyOrderController::class, 'index'])->name('client.orders.index');
    Route::get('/my-orders/{order}', [MyOrderController::class, 'show'])->name('client.orders.show');

    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/history/{receiverId}', [ChatController::class, 'getHistory'])->name('chat.history');
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

});

// === DEBUG ROUTE ===
Route::get('/test-cache-driver', function () {
    return config('cache.default');
});