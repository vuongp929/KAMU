<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
      Route::resource('orders', OrderController::class);
});

// Group route admin với middleware bảo vệ
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('/orders', OrderController::class);
});

Route::get('/test-cache-driver', function () {
    return config('cache.default');
});
require __DIR__ . '/auth.php';
