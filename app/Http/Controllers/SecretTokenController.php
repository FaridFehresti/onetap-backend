<?php

namespace App\Http\Controllers;

use App\Models\SecretToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecretTokenController extends Controller
{
    public function generateSecretToken(Request $request)
    {
        $user = $request->user();

        $existingToken = SecretToken::where('user_id', $user->id)->first();
        if ($existingToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'User already has a secret token',
            ], 400);
        }

        try {
            $token = 'tok_' . bin2hex(random_bytes(32)) . '_app';

            SecretToken::create([
                'token' => $token,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'message' => 'Secret token generated successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error generating secret token: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error generating secret token. Please try again.',
            ], 500);
        }
    }

    public function getSecretToken(Request $request)
    {
        $user = $request->user();

        $token = SecretToken::where('user_id', $user->id)->first();
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No secret token found for this user',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token->token,
        ]);
    }

    public function deleteSecretToken(Request $request)
    {
        $user = $request->user();

        $token = SecretToken::where('user_id', $user->id)->first();
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No secret token found for this user',
            ], 404);
        }

        $token->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Secret token deleted successfully',
        ]);
    }

    public function regenerateSecretToken(Request $request)
    {
        $user = $request->user();

        $existingToken = SecretToken::where('user_id', $user->id)->first();
        if (!$existingToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'No secret token found for this user',
            ], 404);
        }

        try {
            $token = 'tok_' . bin2hex(random_bytes(32)) . '_app';

            $existingToken->update([
                'token' => $token,
            ]);

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'message' => 'Secret token regenerated successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error regenerating secret token: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error regenerating secret token. Please try again.',
            ], 500);
        }
    }


}
