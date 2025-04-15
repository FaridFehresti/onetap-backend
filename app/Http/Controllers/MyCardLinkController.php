<?php

namespace App\Http\Controllers;

use App\Models\MyCardLink;
use Illuminate\Http\Request;

class MyCardLinkController extends Controller
{
    public function index()
    {
        $links = MyCardLink::with('card')->get();

        return response()->json([
            'status' => 'success',
            'data' => $links,
        ]);
    }

    public function show(MyCardLink $myCardLink)
    {
        $myCardLink->load('card');

        return response()->json([
            'status' => 'success',
            'data' => $myCardLink,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'link' => 'required|string',
            'status' => 'in:active,inactive',
            'card_id' => 'required|exists:my_cards,id',
        ]);

        if ($data['status'] === 'active') {
            MyCardLink::where('card_id', $data['card_id'])->update(['status' => 'inactive']);
        }

        $link = MyCardLink::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $link,
        ], 201);
    }

    public function update(Request $request, MyCardLink $myCardLink)
    {
        $data = $request->validate([
            'link' => 'required|string',
            'status' => 'in:active,inactive',
            'card_id' => 'required|exists:my_cards,id',
        ]);

        if ($data['status'] === 'active') {
            MyCardLink::where('card_id', $data['card_id'])
                ->where('id', '!=', $myCardLink->id)
                ->update(['status' => 'inactive']);
        }

        $myCardLink->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCardLink,
        ]);
    }

    public function destroy(MyCardLink $myCardLink)
    {
        $myCardLink->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Link deleted successfully.',
        ]);
    }

}
