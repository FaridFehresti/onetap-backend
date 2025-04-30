<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index()
    {
        $links = SocialLink::with('action')->get();

        return response()->json([
            'status' => 'success',
            'data' => $links->load('action'),
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'link' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id',
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('social'), $logoName);
            $data['logo'] = 'social/' . $logoName;
        }

        $socialLink = SocialLink::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $socialLink,
        ], 201);
    }

    public function show(SocialLink $socialLink)
    {
        return response()->json([
            'status' => 'success',
            'data' => $socialLink,
        ], 200);
    }

    public function update(Request $request, SocialLink $socialLink)
    {
        $data = $request->validate([
            'link' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($socialLink->logo && file_exists(public_path($socialLink->logo))) {
                unlink(public_path($socialLink->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('social'), $logoName);
            $data['logo'] = 'social/' . $logoName;
        }

        $socialLink->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Social link updated successfully.',
            'data' => $socialLink,
        ]);
    }

    public function destroy(SocialLink $socialLink)
    {
        if ($socialLink->logo && file_exists(public_path($socialLink->logo))) {
            unlink(public_path($socialLink->logo));
        }

        $socialLink->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Social link deleted successfully.',
        ]);
    }
}
