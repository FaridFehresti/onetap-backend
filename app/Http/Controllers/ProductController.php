<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['productCategory', 'features', 'tags', 'images', 'address', 'bundleProducts'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['productCategory', 'features', 'tags', 'images', 'address', 'bundleProducts']);

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'colors' => 'nullable|array',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'template_id' => 'nullable|integer',
            'address_id' => 'nullable|exists:addresses,id',
            'feature_ids' => 'nullable|array',
            'feature_ids.*' => 'exists:features,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'bundle_product_ids' => 'nullable|array',
            'bundle_product_ids.*' => 'exists:products,id',
        ]);

        $product = Product::create($data);

        $product->features()->sync($data['feature_ids'] ?? []);
        $product->tags()->sync($data['tag_ids'] ?? []);
        $product->bundleProducts()->sync($data['bundle_product_ids'] ?? []);

        return response()->json([
            'status' => 'success',
            'data' => $product->load(['productCategory', 'features', 'tags', 'images', 'address', 'bundleProducts']),
        ], 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'colors' => 'nullable|array',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'template_id' => 'nullable|integer',
            'address_id' => 'nullable|exists:addresses,id',
            'feature_ids' => 'nullable|array',
            'feature_ids.*' => 'exists:features,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'bundle_product_ids' => 'nullable|array',
            'bundle_product_ids.*' => 'exists:products,id',
        ]);

        $product->update($data);

        $product->features()->sync($data['feature_ids'] ?? []);
        $product->tags()->sync($data['tag_ids'] ?? []);
        $product->bundleProducts()->sync($data['bundle_product_ids'] ?? []);

        return response()->json([
            'status' => 'success',
            'data' => $product->load(['productCategory', 'features', 'tags', 'images', 'address', 'bundleProducts']),
        ]);
    }

    public function destroy(Product $product)
    {
        $product->features()->detach();
        $product->tags()->detach();
        $product->bundleProducts()->detach();

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully.',
        ]);
    }
}
