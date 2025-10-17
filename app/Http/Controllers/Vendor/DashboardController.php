<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $vendor = Auth::user();
        $products = $vendor->products()->count();
        $orders = Order::whereHas('items', fn($q) => $q->whereHas('product', fn($p) => $p->where('user_id', $vendor->id)))->count();
        return view('vendor.dashboard', compact('products', 'orders'));
    }
}
