<?php

use Illuminate\Support\Facades\Route;

// === IMPORT CONTROLLERS ===
// Đã sắp xếp lại và thêm DashboardController
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE CÔNG KHAI ---
Route::get('/', function () {
    return view('welcome');
})->name('home');


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
    'as' => 'admin.',
    'middleware' => ['auth', 'check.admin']
], function () {

    // 1. DASHBOARD
    // Logic đã được chuyển vào DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. QUẢN LÝ CRUD
    // Sử dụng Route::resource cho tất cả các tài nguyên
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('categories', CategoryController::class);

    // 3. QUẢN LÝ ĐÁNH GIÁ (PRODUCT REVIEWS)
    // Vẫn dùng resource và bổ sung các route tùy chỉnh nếu cần
    Route::resource('reviews', ProductReviewController::class)->except(['create', 'edit', 'show']);
    Route::post('/reviews/{id}/reply', [ProductReviewController::class, 'reply'])->name('reviews.reply');
    Route::post('/reviews/{id}/toggle-hide', [ProductReviewController::class, 'toggleHide'])->name('reviews.toggleHide');

});
Route::get('/test',[CheckoutController::class, 'test'])->name('test');
Route::post('/momo_payment', [CheckoutController::class, 'momo_payment'])->name('momo_payment');