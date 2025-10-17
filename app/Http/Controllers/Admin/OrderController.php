<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user', 'items.product')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function show(Order $order, Request $request)
    {
        $order->load('items.product', 'payment', 'user');

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}
