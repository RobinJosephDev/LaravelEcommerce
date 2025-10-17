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
        return view('vendor.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product', 'user');
        return view('vendor.orders.show', compact('order'));
    }

    public function updateStatus(Order $order)
    {
        $this->authorize('update', $order);
        $order->status = request()->status;
        $order->save();
        return redirect()->back()->with('success', 'Order status updated.');
    }
}
