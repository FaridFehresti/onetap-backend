<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $items = Skill::with('action')->get();

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'percentage' => 'nullable|integer|min:0|max:100',
            'action_id' => 'required|exists:actions,id',
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('skills'), $logoName);
            $data['logo'] = 'skills/' . $logoName;
        }

        $item = Skill::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ], 201);
    }

    public function show(Skill $skill)
    {
        return response()->json([
            'status' => 'success',
            'data' => $skill->load('action'),
        ], 200);
    }

    public function update(Request $request, Skill $skill)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'percentage' => 'nullable|integer|min:0|max:100',
            'action_id' => 'required|exists:actions,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($skill->logo && file_exists(public_path($skill->logo))) {
                unlink(public_path($skill->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('skills'), $logoName);
            $data['logo'] = 'skills/' . $logoName;
        }

        $skill->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Skill updated successfully.',
            'data' => $skill,
        ]);
    }

    public function destroy(Skill $skill)
    {
        if ($skill->logo && file_exists(public_path($skill->logo))) {
            unlink(public_path($skill->logo));
        }

        $skill->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Skill deleted successfully.',
        ]);
    }
}
