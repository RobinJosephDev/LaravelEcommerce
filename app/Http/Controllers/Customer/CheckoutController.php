<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $cart = $user->cart ?? $user->cart()->create();

        return view('checkout.index', compact('cart'));
    }

    /**
     * Process checkout and create order
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure cart exists and load items
        $cart = $user->cart()->with('items.product')->firstOrFail();

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $cart->items->sum(fn($i) => $i->quantity * $i->price),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => $request->shipping_address,
        ]);

        // Create order items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

        // Stripe Checkout
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = $cart->items->map(fn($i) => [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => ['name' => $i->product->name],
                'unit_amount' => intval($i->price * 100),
            ],
            'quantity' => $i->quantity,
        ])->toArray();

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', $order),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return redirect($session->url);
    }

    /**
     * Stripe success callback
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function success(Order $order)
    {
        $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        return view('checkout.success', compact('order'));
    }

    /**
     * Stripe cancel callback
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function cancel(Order $order)
    {
        return view('checkout.cancel', compact('order'));
    }
}
