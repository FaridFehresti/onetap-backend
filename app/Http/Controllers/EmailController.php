<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\Contact;


class EmailController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $to = env('CONTACT_RECEIVER_EMAIL', 'default@example.com'); // مقدار پیش‌فرض هم تعریف کن

        Mail::to($to)->send(new Contact(
            $request->email,
            $request->title,
            $request->description
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'Your message has been sent successfully!'
        ], 200);
    }

}
