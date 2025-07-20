<?php

use Illuminate\Support\Facades\Route;

// === IMPORT CONTROLLERS ===
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Client\ProductController as ClientProductController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================================
// --- ROUTE CÔNG KHAI (DÀNH CHO KHÁCH HÀNG) ---
// ==========================================================
Route::get('/', [ClientController::class, 'index'])->name('home');

// === DÒNG CODE ĐÃ ĐƯỢC DI CHUYỂN RA ĐÂY ===
// Route này không cần middleware 'auth' hay 'admin'
Route::get('/products/{product}', [ClientProductController::class, 'show'])->name('client.products.show');


// --- ROUTE XÁC THỰC CỦA LARAVEL BREEZE ---
require __DIR__.'/auth.php';


// --- ROUTE CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (KHÔNG PHẢI ADMIN) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ======================================================================
// === TOÀN BỘ ROUTE CHO ADMIN (GỘP CHUNG VÀO MỘT NƠI) ===
// ======================================================================
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'check.admin']
], function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. QUẢN LÝ CRUD
    Route::resource('products', ProductController::class);
    // Dòng code sai đã được xóa khỏi đây.

    Route::resource('attributes', AttributeController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('categories', CategoryController::class);

    // 3. QUẢN LÝ ĐÁNH GIÁ (PRODUCT REVIEWS)
    Route::resource('reviews', ProductReviewController::class)->except(['create', 'edit', 'show']);
    Route::post('/reviews/{id}/reply', [ProductReviewController::class, 'reply'])->name('reviews.reply');
    Route::post('/reviews/{id}/toggle-hide', [ProductReviewController::class, 'toggleHide'])->name('reviews.toggleHide');

    // 4. QUẢN LÝ NGƯỜI DÙNG
    Route::get('users', [AuthController::class, 'listUser'])->name('users.index');
    Route::get('users/create', [AuthController::class, 'createUser'])->name('users.create');
    Route::post('users', [AuthController::class, 'storeUser'])->name('users.store');
    Route::get('users/{id}/edit', [AuthController::class, 'editUser'])->name('users.edit');
    Route::post('users/{id}', [AuthController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{id}', [AuthController::class, 'deleteUser'])->name('users.destroy');
});