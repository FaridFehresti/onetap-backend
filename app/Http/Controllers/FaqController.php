<?php

namespace App\Http\Controllers;


use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all();

        return response()->json([
            'status' => 'success',
            'data' => $faqs,
        ]);
    }

    public function activefaq()
    {
        $faqs = Faq::where('status', 'active')->orderBy('id','DESC')->get();
        if (!$faqs) {
            return $this->error([], 'Faq Not Found!');
        }

        return response()->json([
            'status' => 'success',
            'message'=> 'Faq Fetch Success!',
            'data' => $faqs,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ques' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $faq = Faq::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $faq,
        ], 201);
    }

    public function show(Faq $faq)
    {
        return response()->json([
            'status' => 'success',
            'data' => $faq,
        ]);
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'ques' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $faq->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $faq,
        ]);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Faq deleted successfully.',
        ]);
    }
}
