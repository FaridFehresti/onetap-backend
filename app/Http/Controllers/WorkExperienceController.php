<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
{
    public function index()
    {
        $items = WorkExperience::with('action')->get();

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
            $logo->move(public_path('work'), $logoName);
            $data['logo'] = 'work/' . $logoName;
        }

        $item = WorkExperience::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ], 201);
    }

    public function show(WorkExperience $workExperience)
    {
        return response()->json([
            'status' => 'success',
            'data' => $workExperience->load('action'),
        ], 200);
    }

    public function update(Request $request, WorkExperience $workExperience)
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
            if ($workExperience->logo && file_exists(public_path($workExperience->logo))) {
                unlink(public_path($workExperience->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('work'), $logoName);
            $data['logo'] = 'work/' . $logoName;
        }

        $workExperience->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Work experience updated successfully.',
            'data' => $workExperience,
        ]);
    }

    public function destroy(WorkExperience $workExperience)
    {
        if ($workExperience->logo && file_exists(public_path($workExperience->logo))) {
            unlink(public_path($workExperience->logo));
        }

        $workExperience->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Work experience deleted successfully.',
        ]);
    }
}
