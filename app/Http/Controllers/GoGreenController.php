<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GoGreen;
use Illuminate\Http\Request;

class GoGreenController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => GoGreen::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'header_text' => 'required|string',
            'header_paragraph' => 'required|string',
            'planted_trees_number' => 'required|integer',
        ]);

        $goGreen = GoGreen::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $goGreen,
        ], 201);
    }

    public function show(GoGreen $goGreen)
    {
        return response()->json([
            'status' => 'success',
            'data' => $goGreen,
        ]);
    }

    public function update(Request $request, GoGreen $goGreen)
    {
        $data = $request->validate([
            'header_text' => 'required|string',
            'header_paragraph' => 'required|string',
            'planted_trees_number' => 'required|integer',
        ]);

        $goGreen->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $goGreen,
        ]);
    }

    public function destroy(GoGreen $goGreen)
    {
        $goGreen->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'GoGreen data deleted successfully.',
        ]);
    }
}

