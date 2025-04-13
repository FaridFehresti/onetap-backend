<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FooterMedia;
use Illuminate\Http\Request;

class FooterMediaController extends Controller
{
    public function index()
    {
        return response()->json(FooterMedia::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'required|string',
        ]);

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cms/footer/media'), $fileName);
            $data['icon'] = 'cms/footer/media/' . $fileName;
        }

        $item = FooterMedia::create($data);

        return response()->json($item, 201);
    }

    public function show(FooterMedia $footerMedia)
    {
        return response()->json($footerMedia);
    }

    public function update(Request $request, FooterMedia $footerMedia)
    {
        $data = $request->validate([
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'required|string',
        ]);

        if ($request->hasFile('icon')) {
            if ($footerMedia->icon && file_exists(public_path($footerMedia->icon))) {
                unlink(public_path($footerMedia->icon));
            }

            $file = $request->file('icon');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cms/footer/media'), $fileName);
            $data['icon'] = 'cms/footer/media/' . $fileName;
        }

        $footerMedia->update($data);

        return response()->json($footerMedia);
    }

    public function destroy(FooterMedia $footerMedia)
    {
        if ($footerMedia->icon && file_exists(public_path($footerMedia->icon))) {
            unlink(public_path($footerMedia->icon));
        }

        $footerMedia->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
