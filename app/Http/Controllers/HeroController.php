<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    public function index()
    {
        $heroes = Hero::all();

        return response()->json([
            'status' => 'success',
            'data' => $heroes,
        ], 200);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'first_text' => 'required|string',
            'second_text' => 'required|string',
            'card_front' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'card_back' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('card_front')) {
            $front = $request->file('card_front');
            $frontName = time() . '_front_' . uniqid() . '.' . $front->getClientOriginalExtension();
            $front->move(public_path('cms/cards'), $frontName);
            $data['card_front'] = 'cms/cards/' . $frontName;
        }


        if ($request->hasFile('card_back')) {
            $back = $request->file('card_back');
            $backName = time() . '_back_' . uniqid() . '.' . $back->getClientOriginalExtension();
            $back->move(public_path('cms/cards'), $backName);
            $data['card_back'] = 'cms/cards/' . $backName;
        }

        $hero = Hero::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $hero,
        ], 201);
    }

    public function show(Hero $hero)
    {
        return response()->json([
            'status' => 'success',
            'data' => $hero,
        ], 200);
    }


    public function update(Request $request, Hero $hero)
    {
        $data = $request->validate([
            'first_text' => 'required|string',
            'second_text' => 'required|string',
            'card_front' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'card_back' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->hasFile('card_front')) {
            if ($hero->card_front && file_exists(public_path($hero->card_front))) {
                unlink(public_path($hero->card_front));
            }

            $front = $request->file('card_front');
            $frontName = time() . '_front_' . uniqid() . '.' . $front->getClientOriginalExtension();
            $front->move(public_path('cms/cards'), $frontName);
            $data['card_front'] = 'cms/cards/' . $frontName;
        }


        if ($request->hasFile('card_back')) {
            if ($hero->card_back && file_exists(public_path($hero->card_back))) {
                unlink(public_path($hero->card_back));
            }

            $back = $request->file('card_back');
            $backName = time() . '_back_' . uniqid() . '.' . $back->getClientOriginalExtension();
            $back->move(public_path('cms/cards'), $backName);
            $data['card_back'] = 'cms/cards/' . $backName;
        }

        $hero->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Hero updated successfully.',
            'data' => $hero,
        ]);
    }

    public function destroy(Hero $hero)
    {
        Storage::disk('public')->delete([$hero->card_front, $hero->card_back]);
        $hero->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Hero deleted successfully.',
        ]);
    }

}

