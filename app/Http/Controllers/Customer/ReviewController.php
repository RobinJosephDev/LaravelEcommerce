<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Product $product, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        // Check if user purchased the product
        $hasBought = $user->orders()->whereHas('items', fn($q) => $q->where('product_id', $product->id))->exists();
        if (!$hasBought) return redirect()->back()->with('error', 'You can only review purchased products.');

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->back()->with('success', 'Review submitted.');
    }
}
