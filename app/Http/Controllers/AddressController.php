<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::all();

        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ]);
    }

    public function show(Address $address)
    {
        return response()->json([
            'status' => 'success',
            'data' => $address,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'postal_code' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $address = Address::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $address,
        ], 201);
    }

    public function update(Request $request, Address $address)
    {
        $data = $request->validate([
            'postal_code' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $address->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $address,
        ]);
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully.',
        ]);
    }
}
