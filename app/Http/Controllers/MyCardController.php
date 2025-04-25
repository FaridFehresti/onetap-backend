<?php

namespace App\Http\Controllers;

use App\Models\MyCard;
use App\Models\MyCardLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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



    use Illuminate\Support\Facades\Log;

    public function store(Request $request)
    {
        Log::info('Storing card data', ['request_data' => $request->all()]);

        try {
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
                'text_color' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'in:active,inactive',
                'user_id' => 'required|exists:users,id',
            ]);

            Log::info('Validation passed', ['validated_data' => $data]);

            if ($request->hasFile('avatar')) {
                Log::info('Avatar file received', ['file_name' => $request->file('avatar')->getClientOriginalName()]);
                $file = $request->file('avatar');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('avatars'), $fileName);
                $data['avatar'] = 'avatars/' . $fileName;
            } else {
                Log::info('No avatar file received');
            }

            $data['uuid'] = Str::uuid();

            Log::info('Creating new card', ['data_to_create' => $data]);

            $card = MyCard::create($data);

            Log::info('Card created successfully', ['created_card' => $card]);

            return response()->json([
                'status' => 'success',
                'data' => $card,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error occurred while storing card', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while storing the card.',
            ], 500);
        }
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
            'text_color'=>'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ]);

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
