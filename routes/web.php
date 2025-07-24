<?php

use Illuminate\Support\Facades\Route;

// === IMPORT CONTROLLERS ===
// Đã sắp xếp lại và thêm DashboardController
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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE CÔNG KHAI ---
Route::get('/', [ClientController::class, 'index'])->name('home');



// --- ROUTE XÁC THỰC CỦA LARAVEL BREEZE ---
// Xử lý các trang /login, /register, /logout...
require __DIR__.'/auth.php';


// --- ROUTE CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (KHÔNG PHẢI ADMIN) ---
Route::middleware('auth')->group(function () {
    // Trang quản lý hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bạn có thể thêm các route khác cho người dùng ở đây (ví dụ: /my-orders)
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


    // // 5. QUẢN LÝ DANH MỤC SẢN PHẨM
    // Route::get( 'categories', [CategoryController::class, 'index'])->name('categories.index');
    // Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    // Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    // Route::get('categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    // Route::post('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    // Route::delete('categories/{id}', [CategoryController::class, 'delete'])->name('categories.destroy');
});