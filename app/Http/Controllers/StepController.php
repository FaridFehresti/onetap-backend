<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function index()
    {
        $steps = Step::orderBy('order')->get();

        return response()->json([
            'status' => 'success',
            'data' => $steps,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'header' => 'required|string',
            'paragraph' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cms/steps'), $fileName);
            $data['image'] = 'cms/steps/' . $fileName;
        }

        $step = Step::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $step,
        ], 201);
    }

    public function show(Step $step)
    {
        return response()->json([
            'status' => 'success',
            'data' => $step,
        ]);
    }

    public function update(Request $request, Step $step)
    {
        $data = $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'header' => 'required|string',
            'paragraph' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($step->image && file_exists(public_path($step->image))) {
                unlink(public_path($step->image));
            }

            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cms/steps'), $fileName);
            $data['image'] = 'cms/steps/' . $fileName;
        }

        $step->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $step,
        ]);
    }

    public function destroy(Step $step)
    {
        if ($step->image && file_exists(public_path($step->image))) {
            unlink(public_path($step->image));
        }

        $step->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Step deleted successfully.',
        ]);
    }
}
