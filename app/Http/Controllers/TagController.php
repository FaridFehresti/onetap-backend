<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return response()->json([
            'status' => 'success',
            'data' => $tags,
        ]);
    }

    public function show(Tag $tag)
    {
        return response()->json([
            'status' => 'success',
            'data' => $tag,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
        ]);

        $tag = Tag::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $tag,
        ], 201);
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'title' => 'required|string',
        ]);

        $tag->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $tag,
        ]);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tag deleted successfully.',
        ]);
    }
}
