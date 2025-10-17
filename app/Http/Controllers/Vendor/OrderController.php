<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();
        $orders = Order::whereHas('items.product', fn($q) => $q->where('user_id', $vendorId))
            ->with('items.product', 'user')
            ->paginate(15);

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return response()->json($order);
    }

    public function updateStatus(Order $order)
    {
        $order->status = request()->status;
        $order->save();
        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }
}
