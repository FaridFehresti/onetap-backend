<?php

namespace App\Http\Controllers;

use App\Models\MyCard;
use App\Models\MyCardLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MyCardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cards = MyCard::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $cards,
        ]);
    }


    public function show(MyCard $myCard)
    {
        $user = auth()->user();

        if ($myCard->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to this card.',
            ], 403);
        }

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
            'avatar'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($request->hasFile('avatar')) {
            if ($myCard->avatar) {
                Storage::disk('public')->delete($myCard->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $myCard->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCard,
        ]);
    }


    function destroy(MyCard $myCard)
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
