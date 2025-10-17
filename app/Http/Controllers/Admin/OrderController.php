<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'items.product')->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'payment', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Order $order)
    {
        // Update status (e.g. shipped/delivered)
        $order->status = request()->status;
        $order->save();
        return redirect()->back()->with('success', 'Order status updated.');
    }
}
