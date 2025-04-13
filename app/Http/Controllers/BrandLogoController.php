<?php

namespace App\Http\Controllers;

use App\Models\Brandlogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandLogoController extends Controller
{
    public function index()
    {
        $data = Brandlogo::latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('brandlogos', 'public');
        }

        $data = Brandlogo::create([
            'image' => $imagePath,
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'BrandLogo created successfully.',
            'data' => $data,
        ], 201);
    }

    public function show(Brandlogo $brandlogo)
    {
        return response()->json([
            'status' => 'success',
            'data' => $brandlogo,
        ]);
    }

    public function update(Request $request, Brandlogo $brandlogo)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($brandlogo->image);
            $brandlogo->image = $request->file('image')->store('brandlogos', 'public');
        }

        $brandlogo->status = $request->input('status');
        $brandlogo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'BrandLogo updated successfully.',
            'data' => $brandlogo,
        ]);
    }

    public function destroy(Brandlogo $brandlogo)
    {
        if ($brandlogo->image) {
            Storage::disk('public')->delete($brandlogo->image);
        }

        $brandlogo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'BrandLogo deleted successfully.',
        ]);
    }

    public function status(Brandlogo $brandlogo)
    {
        $brandlogo->status = $brandlogo->status === 'Active' ? 'Inactive' : 'Active';
        $brandlogo->save();

        return response()->json([
            'status' => 'success',
            'message' => $brandlogo->status === 'Active'
                ? 'BrandLogo published successfully.'
                : 'BrandLogo unpublished successfully.',
            'data' => $brandlogo,
        ]);
    }
}
