<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'reference_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_available' => 'required|integer|min:0',
            'producer_id' => 'nullable|exists:producers,id',
            'wholesale_price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function getProducts(Request $request)
    {
        $defaultSortBy = 'name';
        $defaultSortOrder = 'asc';
        $defaultPerPage = 10;

        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . strtolower($request->query('name')) . '%');
        }

        if ($request->has('reference_number')) {
            $query->where('reference_number', 'like', '%' . strtolower($request->query('reference_number')) . '%');
        }

        $sortBy = $request->query('sort_by', $defaultSortBy);
        $sortOrder = $request->query('sort_order', $defaultSortOrder);
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->query('per_page', $defaultPerPage));

        return response()->json($products);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'reference_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_available' => 'required|integer|min:0',
            'producer_id' => 'nullable|exists:producers,id',
            'wholesale_price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
