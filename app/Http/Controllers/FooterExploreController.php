<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FooterExplore;
use Illuminate\Http\Request;

class FooterExploreController extends Controller
{
    public function index()
    {
        return response()->json(FooterExplore::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string',
            'link' => 'required|string',
        ]);

        $item = FooterExplore::create($data);
        return response()->json($item, 201);
    }

    public function show(FooterExplore $footerExplore)
    {
        return response()->json($footerExplore);
    }

    public function update(Request $request, FooterExplore $footerExplore)
    {
        $data = $request->validate([
            'label' => 'required|string',
            'link' => 'required|string',
        ]);

        $footerExplore->update($data);
        return response()->json($footerExplore);
    }

    public function destroy(FooterExplore $footerExplore)
    {
        $footerExplore->delete();
        return response()->json(['message' => 'Deleted successfully.']);
    }
}
