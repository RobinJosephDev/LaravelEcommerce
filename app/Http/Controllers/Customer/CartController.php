<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display the authenticated user's cart items.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $cart = $user->cart ?? $user->cart()->create();
        $items = $cart->items()->with('product')->get();

        return response()->json([
            'cart' => $items,
        ]);
    }

    /**
     * Add a product to the authenticated user's cart.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function add(Product $product): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $cart = $user->cart ?? $user->cart()->create();

        $cartItem = $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => DB::raw('quantity + 1'), 'price' => $product->price]
        );

        return response()->json([
            'message' => 'Product added to cart.',
            'item' => $cartItem,
        ]);
    }

    /**
     * Remove an item from the authenticated user's cart.
     *
     * @param CartItem $item
     * @return JsonResponse
     */
    public function remove(CartItem $item): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure the item belongs to the user's cart
        if ($item->cart_id !== optional($user->cart)->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'Item removed.']);
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param CartItem $item
     * @param int $quantity
     * @return JsonResponse
     */
    public function update(CartItem $item, int $quantity): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($item->cart_id !== optional($user->cart)->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item->update(['quantity' => $quantity]);

        return response()->json([
            'message' => 'Quantity updated.',
            'item' => $item,
        ]);
    }
}
