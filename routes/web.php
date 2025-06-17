<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
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
