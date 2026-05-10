<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = Sale::sum('final_amount');
        $totalRevenue = Sale::sum('final_amount');
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $lowStockProducts = Product::lowStock()->count();
        
        $recentSales = Sale::latest()->take(10)->get();
        
        return view('dashboard', compact(
            'totalSales',
            'totalRevenue',
            'totalCustomers',
            'totalProducts',
            'lowStockProducts',
            'recentSales'
        ));
    }
}
