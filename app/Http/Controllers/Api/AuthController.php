<?php

namespace App\Http\Controllers\Api;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetRequest;
use App\Http\Requests\OtpRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Mail\OtpMail;
use App\Models\C_M_S;
use App\Models\Cart;
use App\Models\SecretToken;
use App\Models\Subscription;
use App\Models\CartItems;
use App\Models\User;
use App\Models\UserLog;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use apiresponse;

    public function loginWithSecretToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret_token' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $secretToken = SecretToken::where('token', $request->input('secret_token'))->first();

        if (!$secretToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid secret token',
            ], 401);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        if ($secretToken->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Secret token does not belong to this user',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;


        UserLog::create([
            'user_id' => $user->id,
            'activity_type' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful',
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;


        UserLog::create([
            'user_id' => $user->id,
            'activity_type' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        if ($request->has('cart') && is_array($request->cart)) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['created_at' => now(), 'updated_at' => now()]
            );

            foreach ($request->cart as $item) {
                CartItems::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'card_id' => $item['card_id'],
                        'color_id' => $item['color_id'],
                    ],
                    [
                        'quantity' => $item['quantity'],
                    ]
                );
            }
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->error([], 'Invalid credentials', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;


        UserLog::create([
            'user_id' => $user->id,
            'activity_type' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        if ($request->has('cart') && is_array($request->cart)) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['created_at' => now(), 'updated_at' => now()]
            );

            foreach ($request->cart as $item) {
                CartItems::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'card_id' => $item['card_id'],
                        'color_id' => $item['color_id'],
                    ],
                    [
                        'quantity' => $item['quantity'],
                    ]
                );
            }
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function check(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not authenticated',
            ], 401);
        }

        $order = $user->orders()->latest()->first();
        $subcription = Subscription::where('user_id', $user->id)->first();
        if ($order) {
            $lastPayment = $order->payments()->latest()->first();

            $paymentStatus = $lastPayment ? $lastPayment->status : 'pending';

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'has_order' => true,
                'payment_status' => $paymentStatus,
                'subscription' => $subcription->plan ?? null,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User has no orders',
            'user' => $user,
            'has_order' => false,
            'payment_status' => 'pending',
        ]);
    }

    public function checkOtp(OtpRequest $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');

        if (!$email || !$otp) {
            return response()->json([
                'status' => false,
                'errors' => 'Email or OTP not provided',
                'code' => 400,
            ], 400);
        }
        $user = User::whereEmail($email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'errors' => 'User not found',
                'code' => 404,
            ], 404);
        }
        if (strval($user->otp) === strval($otp)) {
            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully',
                'code' => 200,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'errors' => 'Invalid OTP',
            'code' => 401,
        ], 401);
    }

    public function forgetPassword(ForgetRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $email = $request->input('email');
            $user = User::whereEmail($email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                    'code' => 404,
                ]);
            }

            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $user->otp = $otp;

            if (!$user->save()) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to save OTP. Please try again.',
                ], 500);
            }

            try {
                Mail::to($user->email)->send(new OtpMail($otp));
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'OTP sent successfully',
                    'otp' => $otp, // ✅ For testing only
                ], 200);
            } catch (\Exception $mailException) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP. Please try again.',
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }

    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::whereEmail($request->input('email'))->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                    'code' => 404,
                ], 404);
            }

            if (strval($request->otp) !== strval($user->otp)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                    'code' => 401,
                ], 401);
            }

            $user->password = Hash::make($request->input('new_password'));
            $user->otp = null;
            $user->email_verified_at = now();
            $user->update();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'code' => 500,
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        UserLog::create([
            'user_id' => $user->id,
            'activity_type' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        UserLog::create([
            'user_id' => $user->id,
            'activity_type' => 'account_deletion',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully'
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'email' => 'nullable|string|email|unique:users',
            'avatar' => 'nullable|image',
            'occupation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('avatar')) {
            $imagePath = ImageHelper::handleImageUpload($request->file('avatar'), null, 'avatar');
        }

        $user = User::find(auth()->user()->id);

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->avatar = $imagePath ?? $user->avatar;
        $user->occupation = $request->occupation ?? $user->occupation;
        $user->save();

        return $this->success($user, 'Information Update Successfully!', 200);
    }
}
