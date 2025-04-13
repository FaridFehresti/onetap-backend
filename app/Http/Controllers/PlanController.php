<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderByDesc('id')->get();

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'best_plan' => 'required|boolean',
            'price' => 'required|numeric',
            'price_period' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);

        $plan = Plan::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $plan,
        ], 201);
    }

    public function show(Plan $plan)
    {
        return response()->json([
            'status' => 'success',
            'data' => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'best_plan' => 'required|boolean',
            'price' => 'required|numeric',
            'price_period' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);

        $plan->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $plan,
        ]);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Plan deleted successfully.',
        ]);
    }
}
