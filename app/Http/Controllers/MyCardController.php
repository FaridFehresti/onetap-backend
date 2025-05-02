<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\MyCard;
use App\Models\MyCardLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MyCardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cards = MyCard::where('user_id', $user->id)->with(['links', 'user', 'actions'])->get();

        foreach ($cards as $card) {
            $card->increment('total_scans');
        }

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

        $myCard->increment('total_scans');

        $myCard->load(['links', 'user', 'actions']);
        return response()->json([
            'status' => 'success',
            'data' => $myCard,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'template_id' => 'required|integer',
            'qrcode_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data['user_id'] = Auth::id();
        $data['uuid'] = Str::uuid();
        $data['total_scans'] = 0;

        if ($request->hasFile('qrcode_image')) {
            $file = $request->file('qrcode_image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('qrcodes'), $fileName);
            $data['qrcode_image'] = 'qrcodes/' . $fileName;
        }

        $card = MyCard::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $card,
        ], 201);
    }

    public function update(Request $request, MyCard $myCard)
    {
        $user = auth()->user();

        if ($myCard->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to this card.',
            ], 403);
        }

        $data = $request->validate([
            'title' => 'required|string',
            'template_id' => 'required|integer',
            'qrcode_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('qrcode_image')) {
            if ($myCard->qrcode_image && file_exists(public_path($myCard->qrcode_image))) {
                unlink(public_path($myCard->qrcode_image));
            }

            $file = $request->file('qrcode_image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('qrcodes'), $fileName);
            $data['qrcode_image'] = 'qrcodes/' . $fileName;
        }

        $myCard->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCard,
        ]);
    }

    public function destroy(MyCard $myCard)
    {
        $user = auth()->user();

        if ($myCard->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to this card.',
            ], 403);
        }

        if ($myCard->qrcode_image && file_exists(public_path($myCard->qrcode_image))) {
            unlink(public_path($myCard->qrcode_image));
        }

        $myCard->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Card deleted successfully.',
        ]);
    }

    public function getActiveActionByUuid($uuid)
    {
        try {
            $card = MyCard::where('uuid', $uuid)->firstOrFail();

            $card->increment('total_scans');

            $activeAction = Action::where('card_id', $card->id)
                ->where('status', 'active')
                ->first();

            if ($activeAction) {
                $activeAction->increment('scan_count');

                $activeAction->load(['images', 'socialLinks', 'workExperiences', 'educations', 'awards']);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'action' => $activeAction,
                        'card_total_scans' => $card->total_scans,
                    ],
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No active action found for this card.',
            ], 404);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Card not found.',
            ], 404);
        }
    }
}
