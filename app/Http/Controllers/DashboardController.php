<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
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
    }
}