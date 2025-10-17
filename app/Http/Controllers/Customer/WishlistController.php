<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    /**
     * Toggle a product in the user's wishlist.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function toggle(Product $product): JsonResponse
    {
        $user = Auth::user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['message' => 'Removed from wishlist.']);
        }

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);
        return response()->json(['message' => 'Added to wishlist.']);
    }

    /**
     * Get all wishlist items for the authenticated user.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $items = $user->wishlists()->with('product')->get();

        return response()->json([
            'wishlist' => $items
        ]);
    }
}
