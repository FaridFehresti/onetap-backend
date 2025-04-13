<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();

        return response()->json([
            'status' => 'success',
            'data' => $contacts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'phone_number' => 'nullable|string',
            'email' => 'required|email',
            'address' => 'nullable|string',
        ]);

        $contact = Contact::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ], 201);
    }

    public function show(Contact $contact)
    {
        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'phone_number' => 'nullable|string',
            'email' => 'required|email',
            'address' => 'nullable|string',
        ]);

        $contact->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ]);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Contact deleted successfully.',
        ]);
    }
}
