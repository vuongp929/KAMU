<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordersCount = Order::count(); 
        $revenue = Order::sum('total_price'); 
        $productsCount = Product::count(); 

        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month');


        $months = range(1, 12);
        $formattedRevenue = [];
        foreach ($months as $month) {
            $formattedRevenue[] = $monthlyRevenue->get($month, 0); 
        }
        $months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $monthlyRevenue = [5000000, 8000000, 10000000, 20000000, 25000000, 15000000, 17000000, 30000000, 22000000, 35000000, 38000000, 45000000];

        return view('dashboard', [
            'revenue' => 150000000,
            'ordersCount' => 1200,
            'productsCount' => 350,
            'months' => $months,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
        // return view('dashboard', [
        //     'ordersCount' => $ordersCount,
        //     'revenue' => $revenue,
        //     'productsCount' => $productsCount,
        //     'months' => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
        //     'monthlyRevenue' => $formattedRevenue,
        // ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
