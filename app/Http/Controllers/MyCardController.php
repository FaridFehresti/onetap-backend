<?php

namespace App\Http\Controllers;

use App\Models\MyCard;
use App\Models\MyCardLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MyCardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cards = MyCard::where('user_id', $user->id)->with('links')->get();

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

        $myCard ->load('links');
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
                'email' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'address' => 'nullable|string',
                'company' => 'nullable|string',
                'company_number' => 'nullable|string',
                'postal_code' => 'nullable|integer',
                'color' => 'nullable|string',
                'text_color' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'in:active,inactive',
            ]);


            $data['user_id'] = Auth::id();

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('avatars'), $fileName);
                $data['avatar'] = 'avatars/' . $fileName;
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
            'email' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'company' => 'nullable|string',
            'company_number' => 'nullable|string',
            'postal_code' => 'nullable|integer',
            'color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'in:active,inactive',
        ]);

        $data['user_id'] = Auth::id();

        if ($request->hasFile('avatar')) {
            if ($myCard->avatar && file_exists(public_path($myCard->avatar))) {
                unlink(public_path($myCard->avatar));
            }

            $file = $request->file('avatar');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('avatars'), $fileName);
            $data['avatar'] = 'avatars/' . $fileName;
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
