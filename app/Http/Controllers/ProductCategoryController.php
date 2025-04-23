<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::all();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function show(ProductCategory $productCategory)
    {
        return response()->json([
            'status' => 'success',
            'data' => $productCategory,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
        ]);

        $category = ProductCategory::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->validate([
            'title' => 'required|string',
        ]);

        $productCategory->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $productCategory,
        ]);
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product category deleted successfully.',
        ]);
    }
}
