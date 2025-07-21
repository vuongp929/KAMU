<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\CartController as ControllersCartController;
use App\Http\Controllers\CheckoutController as ControllersCheckoutController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\OrderController as ControllersOrderController;
use App\Models\Cart;

// === ROUTE CÔNG KHAI ===
Route::get('/', [ClientController::class, 'index'])->name('home');
Route::get('/cart', [ControllersCartController::class, 'index'])->name('cart');

//cart 
Route::prefix('cart')->name('cart.')->group(function () {

    Route::post('/update', [ControllersCartController::class, 'update'])->name('update');
    Route::post('/remove', [ControllersCartController::class, 'remove'])->name('remove');
    Route::post('/cart/remove', [ControllersCartController::class, 'remove'])->name('cart.remove');
});

//order
Route::prefix('order')->name('order.')->group(function () {
    Route::get('/', [ControllersOrderController::class, 'index'])->name('index');
    Route::post('/', [ControllersOrderController::class, 'store'])->name('store');
    Route::post('/order/store', [ControllersOrderController::class, 'store'])->name('order.store');
    Route::get('/order/success', [ControllersOrderController::class, 'success'])->name('success');
});
Route::prefix('checkout')->middleware('auth')->group(function () {
    Route::get('/', [ControllersCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [ControllersCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkouts/{id}', [ControllersCheckoutController::class, 'show'])->name('client.checkouts.show');
    Route::get('checkouts/{id}/cancel', [ControllersCheckoutController::class, 'cancel'])->name('client.checkouts.cancel');
    Route::get('checkouts/{id}/restore', [ControllersCheckoutController::class, 'restore'])->name('client.checkouts.restore');
});

//apply-discount
Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/apply-discount', [OrderController::class, 'applyDiscount'])->name('apply-discount');
});


// === ROUTE XÁC THỰC (Laravel Breeze) ===
require __DIR__ . '/auth.php';

// === ROUTE NGƯỜI DÙNG ĐÃ LOGIN ===
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Nếu bạn muốn cho user thường xem order → giữ dòng này
    Route::resource('orders', OrderController::class);
});

// === ROUTE ADMIN ===
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'check.admin']
], function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý CRUD
    Route::resource('products', ProductController::class);
    Route::resource('attributes', AttributeController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('categories', CategoryController::class);

    // Quản lý đánh giá
    Route::resource('reviews', ProductReviewController::class)->except(['create', 'edit', 'show']);
    Route::post('/reviews/{id}/reply', [ProductReviewController::class, 'reply'])->name('reviews.reply');
    Route::post('/reviews/{id}/toggle-hide', [ProductReviewController::class, 'toggleHide'])->name('reviews.toggleHide');

    // Quản lý người dùng
    Route::get('users', [AuthController::class, 'listUser'])->name('users.index');
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
