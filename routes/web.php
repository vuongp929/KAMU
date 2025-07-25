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
use App\Http\Controllers\Client\ProductController as ClientProductController;


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
});


// === ROUTE CHO ADMIN ===
Route::group([
    'prefix' => 'admin',
    'as' => 'admins.',
    'middleware' => ['auth', 'check.admin']
], function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. CRUD Resources
    Route::resource('products', ProductController::class);
    Route::resource('attributes', AttributeController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('categories', CategoryController::class);

    // 3. Product Reviews
    Route::resource('reviews', ProductReviewController::class)->except(['create', 'edit', 'show']);
    Route::post('/reviews/{id}/reply', [ProductReviewController::class, 'reply'])->name('reviews.reply');
    Route::post('/reviews/{id}/toggle-hide', [ProductReviewController::class, 'toggleHide'])->name('reviews.toggleHide');

    // 4. Users
    Route::get('users', [AuthController::class, 'listUser'])->name('users.index');
    Route::get('users/create', [AuthController::class, 'createUser'])->name('users.create');
    Route::post('users', [AuthController::class, 'storeUser'])->name('users.store');
    Route::get('users/{id}/edit', [AuthController::class, 'editUser'])->name('users.edit');
    Route::post('users/{id}', [AuthController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{id}', [AuthController::class, 'deleteUser'])->name('users.destroy');
});

// === DEBUG ROUTE ===
Route::get('/test-cache-driver', function () {
    return config('cache.default');
});