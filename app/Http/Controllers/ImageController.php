<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::all();

        return response()->json([
            'status' => 'success',
            'data' => $images,
        ]);
    }

    public function show(Image $image)
    {
        return response()->json([
            'status' => 'success',
            'data' => $image,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alt_text' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_cover' => 'nullable|in:true,false,1,0',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $data['is_cover'] = filter_var($request->input('is_cover'), FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $data['file'] = 'images/' . $fileName;
        }

        $image = Image::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $image,
        ], 201);
    }

    public function update(Request $request, Image $image)
    {
        $data = $request->validate([
            'alt_text' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_cover' => 'nullable|in:true,false,1,0',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $data['is_cover'] = filter_var($request->input('is_cover'), FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('file')) {
            if ($image->file && file_exists(public_path($image->file))) {
                unlink(public_path($image->file));
            }

            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $data['file'] = 'images/' . $fileName;
        }

        $image->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $image,
        ]);
    }

    public function destroy(Image $image)
    {
        if ($image->file && file_exists(public_path($image->file))) {
            unlink(public_path($image->file));
        }

        $image->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Image deleted successfully.',
        ]);
    }
}
