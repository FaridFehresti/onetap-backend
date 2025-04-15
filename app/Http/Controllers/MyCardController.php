<?php

namespace App\Http\Controllers;

use App\Models\MyCard;
use App\Models\MyCardLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MyCardController extends Controller
{
    public function index()
    {
        $cards = MyCard::with('links')->get();

        return response()->json([
            'status' => 'success',
            'data' => $cards,
        ]);
    }

    public function show(MyCard $myCard)
    {
        $myCard->load('links');

        return response()->json([
            'status' => 'success',
            'data' => $myCard,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'company' => 'nullable|string',
            'company_number' => 'nullable|string',
            'postal_code' => 'nullable|integer',
            'color' => 'nullable|string',
            'avatar' => 'nullable|string',
            'status' => 'in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

        $data['uuid'] = Str::uuid();

        $card = MyCard::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $card,
        ], 201);
    }

    public function update(Request $request, MyCard $myCard)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'company' => 'nullable|string',
            'company_number' => 'nullable|string',
            'postal_code' => 'nullable|integer',
            'color' => 'nullable|string',
            'avatar' => 'nullable|string',
            'status' => 'in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

        $myCard->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCard,
        ]);
    }

    public function destroy(MyCard $myCard)
    {
        $myCard->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Card deleted successfully.',
        ]);
    }


    public function getActiveLinkByUuid($uuid)
    {
        try {
            $card = MyCard::where('uuid', $uuid)->firstOrFail();

            $activeLink = MyCardLink::where('card_id', $card->id)
                ->where('status', 'active')
                ->first();

            if ($activeLink) {
                return response()->json([
                    'status' => 'success',
                    'data' => $activeLink->link,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No active link found for this card.',
            ], 404);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Card not found with this UUID.',
            ], 404);
        }
    }
}
