<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('vendor', 'categories')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);
        }

        return view('admin.products.index', compact('products'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function show(Product $product)
    {
        $product->load(['vendor', 'categories']);

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

        $product = Product::create($data);

        if ($request->categories) {
            $product->categories()->sync($request->categories);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

        $product->update($data);

        if ($request->categories) {
            $product->categories()->sync($request->categories);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
