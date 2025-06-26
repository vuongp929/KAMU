<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// === IMPORT CONTROLLERS ===
// Sử dụng cú pháp gọn gàng để import nhiều controller
use App\Http\Controllers\{
    AuthController,
    DiscountController,
    OrderController,
    ProfileController,
    Admin\ProductController,
    Admin\ProductReviewController
};

// === IMPORT MODELS ===
// Import các model cần thiết để lấy dữ liệu
use App\Models\Order;
use App\Models\Product;
use App\Models\User;



Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/post-login', [AuthController::class, 'postLogin'])->name('postLogin');
});

require __DIR__.'/auth.php';


Route::get('/dashboard', function () {

    $revenue = Order::where('status', 'completed')->sum('total_price');
    $ordersCount = Order::count();
    $productsCount = Product::count();
    $usersCount = User::whereDoesntHave('roles', function ($query) {
        $query->where('role', 'admin');
    })->count();


    $revenueData = Order::select(
        DB::raw('SUM(total_price) as revenue'),
        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
    )
    ->where('status', 'completed')
    ->where('created_at', '>=', now()->subMonths(11))
    ->groupBy('month')
    ->orderBy('month', 'asc')
    ->get();

    $months = $revenueData->pluck('month');
    $monthlyRevenue = $revenueData->pluck('revenue');


    return view('dashboard', compact(
        'revenue',
        'ordersCount',
        'productsCount',
        'usersCount', 
        'months',
        'monthlyRevenue'
    ));
})->middleware(['auth', 'check.admin'])->name('dashboard');


Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'check.admin']
], function () {
    Route::resource('orders', OrderController::class);

    Route::resource('discounts', DiscountController::class);
    Route::resource('products', ProductController::class);

    Route::get('/reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{id}', [ProductReviewController::class, 'destroy'])->name('reviews.destroy');

});


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});