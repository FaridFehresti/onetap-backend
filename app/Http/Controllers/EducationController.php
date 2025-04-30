<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index()
    {
        $items = Education::with('action')->get();

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('education'), $logoName);
            $data['logo'] = 'education/' . $logoName;
        }

        $item = Education::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ], 201);
    }

    public function show(Education $education)
    {
        return response()->json([
            'status' => 'success',
            'data' => $education->load('action'),
        ], 200);
    }

    public function update(Request $request, Education $education)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_id' => 'required|exists:actions,id'
        ]);

        if ($request->hasFile('logo')) {
            if ($education->logo && file_exists(public_path($education->logo))) {
                unlink(public_path($education->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('education'), $logoName);
            $data['logo'] = 'education/' . $logoName;
        }

        $education->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Education updated successfully.',
            'data' => $education,
        ]);
    }

    public function destroy(Education $education)
    {
        if ($education->logo && file_exists(public_path($education->logo))) {
            unlink(public_path($education->logo));
        }

        $education->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Education deleted successfully.',
        ]);
    }
}
