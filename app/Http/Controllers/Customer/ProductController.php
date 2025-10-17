<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('status', 'active');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category) {
            $query->whereHas('categories', fn($q) => $q->where('id', $request->category));
        }

        $products = $query->with(['categories', 'vendor'])->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'vendor', 'reviews']);

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }
}
