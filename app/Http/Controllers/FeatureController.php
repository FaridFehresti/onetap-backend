<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::all();

        return response()->json([
            'status' => 'success',
            'data' => $features,
        ]);
    }

    public function show(Feature $feature)
    {
        return response()->json([
            'status' => 'success',
            'data' => $feature,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $feature = Feature::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $feature,
        ], 201);
    }

    public function update(Request $request, Feature $feature)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $feature->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $feature,
        ]);
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Feature deleted successfully.',
        ]);
    }
}
