<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'totalOrders', 'recentOrders'));
    }
}
