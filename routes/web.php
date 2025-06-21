<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth admin routes
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/post-login', [AuthController::class, 'postLogin'])->name('postLogin');
    Route::post('logout/{id}', [AuthController::class, 'logOut'])->name('logOut');
});

// Admin routes
Route::group(
    [
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => 'check.admin'
    ],
    function () {
        Route::get('/dashboard', function () {
            return view('admins.dashboard');
        })->name('dashboard');


        // Discount routes
        Route::resource('discounts', DiscountController::class);
        Route::post('discounts/apply', [DiscountController::class, 'applyDiscount'])->name('discounts.apply');
        Route::get('/reviews', [\App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{id}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::post('/reviews/{id}/reply', [\App\Http\Controllers\Admin\ProductReviewController::class, 'reply'])->name('reviews.reply');
        Route::post('/reviews/{id}/toggle-hide', [\App\Http\Controllers\Admin\ProductReviewController::class, 'toggleHide'])->name('reviews.toggleHide');
        Route::get('/reviews/create', [\App\Http\Controllers\Admin\ProductReviewController::class, 'create'])->name('reviews.create');
        Route::post('/reviews', [\App\Http\Controllers\Admin\ProductReviewController::class, 'store'])->name('reviews.store');
        Route::get('/reviews/{id}/edit', [\App\Http\Controllers\Admin\ProductReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{id}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'update'])->name('reviews.update');
    }
);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-cache-driver', function () {
    return config('cache.default');
});
