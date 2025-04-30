<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Award;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index()
    {
        $awards = Award::with('action')->get();

        return response()->json([
            'status' => 'success',
            'data' => $awards,
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('awards'), $logoName);
            $data['logo'] = 'awards/' . $logoName;
        }

        $award = Award::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $award,
        ], 201);
    }

    public function show(Award $award)
    {
        return response()->json([
            'status' => 'success',
            'data' => $award->load('action'),
        ], 200);
    }

    public function update(Request $request, Award $award)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id'
        ]);

        if ($request->hasFile('logo')) {
            if ($award->logo && file_exists(public_path($award->logo))) {
                unlink(public_path($award->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('awards'), $logoName);
            $data['logo'] = 'awards/' . $logoName;
        }

        $award->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Award updated successfully.',
            'data' => $award,
        ]);
    }

    public function destroy(Award $award)
    {
        if ($award->logo && file_exists(public_path($award->logo))) {
            unlink(public_path($award->logo));
        }

        $award->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Award deleted successfully.',
        ]);
    }
}
