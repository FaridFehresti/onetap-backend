<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FooterResource;
use Illuminate\Http\Request;

class FooterResourceController extends Controller
{
    public function index()
    {
        return response()->json(FooterResource::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string',
            'link' => 'required|string',
        ]);

        $item = FooterResource::create($data);
        return response()->json($item, 201);
    }

    public function show(FooterResource $footerResource)
    {
        return response()->json($footerResource);
    }

    public function update(Request $request, FooterResource $footerResource)
    {
        $data = $request->validate([
            'label' => 'required|string',
            'link' => 'required|string',
        ]);

        $footerResource->update($data);
        return response()->json($footerResource);
    }

    public function destroy(FooterResource $footerResource)
    {
        $footerResource->delete();
        return response()->json(['message' => 'Deleted successfully.']);
    }
}
