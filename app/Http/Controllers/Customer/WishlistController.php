<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class WishlistController extends Controller
{
    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return redirect()->back()->with('success', 'Removed from wishlist.');
        }

        Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
        return redirect()->back()->with('success', 'Added to wishlist.');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $items = $user->wishlists()->with('product')->get();

        return view('wishlist.index', compact('items'));
    }
}
