<?php

namespace App\Http\Controllers\Api;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\Card;
use App\Models\CardColor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CardController extends Controller
{
    public function index(): JsonResponse
    {
        $cards = Card::where('status', 'active')
            ->orderBy('id', 'desc')
            ->with('colors')
            ->get();

        return response()->json(['success' => true, 'message' => 'Data fetched', 'data' => $cards]);
    }

    public function store(CreateCardRequest $request): JsonResponse
    {
        $imagePath = $request->hasFile('image')
            ? ImageHelper::handleImageUpload($request->file('image'), null, 'card')
            : null;

        $lastCard = Card::orderBy('id', 'desc')->first();
        $nextSerial = $lastCard ? intval(substr($lastCard->code, 5)) + 1 : 1;
        $code = 'CARD-' . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);

        $card = Card::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'code' => $code,
            'status' => 'active'
        ]);

        if ($request->has('color') && is_array($request->color)) {
            foreach ($request->color as $color) {
                CardColor::create(['card_id' => $card->id, 'name' => $color]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Card created successfully!', 'data' => $card], 201);
    }

    public function show($id): JsonResponse
    {
        $card = Card::with('colors')->find($id);
        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $card]);
    }

    public function update($id, UpdateCardRequest $request): JsonResponse
    {
        $card = Card::findOrFail($id);
        $card->name = $request->name;
        $card->price = $request->price;

        if ($request->hasFile('image')) {
            if ($card->image) {
                \Storage::delete($card->image);
            }
            $card->image = ImageHelper::handleImageUpload($request->file('image'), null, 'card');
        }

        $card->save();
        $card->colors()->delete();

        if ($request->has('color') && is_array($request->color)) {
            foreach ($request->color as $color) {
                CardColor::create(['card_id' => $card->id, 'name' => $color]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Card updated successfully!', 'data' => $card]);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $card = Card::find($id);
            if (!$card) {
                return response()->json(['success' => false, 'message' => 'Card not found'], 404);
            }

            if ($card->image) {
                \Storage::delete($card->image);
            }

            $card->colors()->delete();
            $card->delete();

            return response()->json(['success' => true, 'message' => 'Card deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', 'error' => $e->getMessage()], 500);
        }
    }

    public function status($id): JsonResponse
    {
        $card = Card::find($id);
        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Card not found'], 404);
        }

        $card->status = $card->status === 'active' ? 'inactive' : 'active';
        $card->save();

        return response()->json([
            'success' => true,
            'message' => $card->status === 'active' ? 'Published Successfully.' : 'Unpublished Successfully.',
            'data' => $card,
        ]);
    }
}
