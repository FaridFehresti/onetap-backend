<?php

namespace App\Http\Controllers;

use App\Models\SecondFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecondFeatureController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => SecondFeature::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_text' => 'required|string',
            'second_text' => 'required|string',
            'image' => 'required|image',
        ]);

        $data['image'] = $request->file('image')->store('second_features', 'public');

        $item = SecondFeature::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'SecondFeature created successfully.',
            'data' => $item,
        ], 201);
    }

    public function show(SecondFeature $secondFeature)
    {
        return response()->json([
            'status' => 'success',
            'data' => $secondFeature,
        ]);
    }

    public function update(Request $request, SecondFeature $secondFeature)
    {
        $data = $request->validate([
            'first_text' => 'required|string',
            'second_text' => 'required|string',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($secondFeature->image);
            $data['image'] = $request->file('image')->store('second_features', 'public');
        }

        $secondFeature->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'SecondFeature updated successfully.',
            'data' => $secondFeature,
        ]);
    }

    public function destroy(SecondFeature $secondFeature)
    {
        Storage::disk('public')->delete($secondFeature->image);
        $secondFeature->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'SecondFeature deleted successfully.',
        ]);
    }
}
