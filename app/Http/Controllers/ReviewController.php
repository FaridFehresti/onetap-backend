<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user')->get();

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ]);
    }

    public function show(Review $review)
    {
        return response()->json([
            'status' => 'success',
            'data' => $review,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'star' => 'required|integer|min:1|max:5',
            'product_id' => 'required|exists:products,id',
            'description' => 'nullable|string',
        ]);

        $data['user_id'] = $request->user()->id;

        $review = Review::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ], 201);
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'star' => 'required|integer|min:1|max:5',
            'product_id' => 'required|exists:products,id',
            'description' => 'nullable|string',
        ]);

        $review->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully.',
        ]);
    }
}
