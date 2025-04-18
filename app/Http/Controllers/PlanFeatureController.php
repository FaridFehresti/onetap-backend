<?php

namespace App\Http\Controllers;

use App\Models\PlanFeature;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    public function index()
    {
        $features = PlanFeature::with('plan')->get();

        return response()->json([
            'status' => 'success',
            'data' => $features,
        ]);
    }

    public function show(PlanFeature $planFeature)
    {
        $planFeature->load('plan');

        return response()->json([
            'status' => 'success',
            'data' => $planFeature,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string',
            'status' => 'in:active,inactive',
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($data['status'] === 'active') {
            PlanFeature::where('plan_id', $data['plan_id'])->update(['status' => 'inactive']);
        }

        $feature = PlanFeature::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $feature,
        ], 201);
    }

    public function update(Request $request, PlanFeature $planFeature)
    {
        $data = $request->validate([
            'text' => 'required|string',
            'status' => 'in:active,inactive',
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($data['status'] === 'active') {
            PlanFeature::where('plan_id', $data['plan_id'])
                ->where('id', '!=', $planFeature->id)
                ->update(['status' => 'inactive']);
        }

        $planFeature->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $planFeature,
        ]);
    }

    public function destroy(PlanFeature $planFeature)
    {
        $planFeature->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Feature deleted successfully.',
        ]);
    }
}
