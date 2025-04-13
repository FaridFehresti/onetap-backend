<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Color::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colors' => 'required|array',
            'colors.*.title' => 'required|string|max:255',
        ]);

        $colors = [];
        foreach ($data['colors'] as $colorData) {
            $colors[] = Color::create(['title' => $colorData['title']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Colors created successfully.',
            'data' => $colors,
        ], 201);
    }



    public function show(Color $color)
    {
        return response()->json([
            'status' => 'success',
            'data' => $color
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'colors' => 'required|array',
            'colors.*.id' => 'required|integer',
            'colors.*.title' => 'required|string|max:255',
        ]);

        $updatedColors = [];

        foreach ($data['colors'] as $colorData) {

            if (isset($colorData['id'])) {
                $color = Color::find($colorData['id']);
                if ($color) {
                    $color->update(['title' => $colorData['title']]);
                    $updatedColors[] = $color;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Colors updated successfully.',
            'data' => $updatedColors,
        ]);
    }




    public function destroy(Color $color)
    {
        $color->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Color deleted successfully.',
        ]);
    }
}

