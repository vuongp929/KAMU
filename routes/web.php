<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::prefix('admins')->name('admins.')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('postLogin');
    Route::get('/logout/{id}', [AuthController::class, 'logOut'])->name('logout');
    Route::get('/dashboard', function () {
        return view('admins.dashboard');
    })->name('dashboard');

    // ✅ Move the category routes inside here
    Route::prefix('categorys')->name('categorys.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/show/{id}', [CategoryController::class, 'show'])->name('show');
        Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('{id}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
    });
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
require __DIR__.'/auth.php';
