<?php

namespace App\Http\Controllers;

use App\Models\ActionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActionImageController extends Controller
{
    public function index()
    {
        $images = ActionImage::with('action')->get();

        return response()->json([
            'status' => 'success',
            'data' => $images,
        ]);
    }

    public function show(ActionImage $actionImage)
    {
        return response()->json([
            'status' => 'success',
            'data' => $actionImage->load('action'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'action_id' => 'required|exists:actions,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('action_images'), $fileName);
            $data['image'] = 'action_images/' . $fileName;
        }

        $image = ActionImage::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $image->load('action'),
        ], 201);
    }

    public function update(Request $request, ActionImage $actionImage)
    {
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id'
        ]);

        if ($request->hasFile('image')) {
            if ($actionImage->image && file_exists(public_path($actionImage->image))) {
                unlink(public_path($actionImage->image));
            }

            $file = $request->file('image');
            $fileName = time() . '_img_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('action_images'), $fileName);
            $data['image'] = 'action_images/' . $fileName;
        }

        $actionImage->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $actionImage,
        ]);
    }

    public function destroy(ActionImage $actionImage)
    {
        if ($actionImage->image && file_exists(public_path($actionImage->image))) {
            unlink(public_path($actionImage->image));
        }

        $actionImage->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Image deleted successfully.',
        ]);
    }
}
