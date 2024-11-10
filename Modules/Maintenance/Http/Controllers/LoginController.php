<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $phoneNumber = $request->phone;

        $user = User::where('phone', $phoneNumber)->first();

        if (!$user) {
            $client = Client::where('phone', $phoneNumber)->first();

            if (!$client) {
                throw ValidationException::withMessages([
                    'phone' => ['لم يتم العثور على مستخدم بهذا الرقم.'],
                ]);
            }

            $clientToken = $client->createToken('client-token')->plainTextToken;

            return response()->json(['data' => [
                'user' => [
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'level' => 'customer',
                    'otp' => null,
                    // 'otp' => 123456,
                    'token' => $clientToken
                ]
            ]]);
        }


        $userToken = $user->createToken('user-token')->plainTextToken;

        return response()->json(['data' => [
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone,
                // 'level' => 'customer',
                'level' => 'technician',
                'completedJobs' => 10,
                'otp' => null,
                // 'otp' => 123456,
                'rating' => 4.8,
                'token' => $userToken
            ]
        ]]);
    }

    public function otp(Request $request)
    {

        // set new otp code  for auth user
        $user = auth('sanctum')->user();

        $user->otp = 123456;
        $user->save();

        return [
            'data' => [
                'otp' => 123456,
                'user' => auth('sanctum')->user()
            ]
        ];
    }

    public function verifyOtp(Request $request)
    {

        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = auth('sanctum')->user();

        if ($user->otp != $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['كود التحقق غير صحيح.'],
            ]);
        }
        $user->otp = null;
        // $user->save();

        return $this->formatUserResponse($user);
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    private function formatUserResponse($user)
    {
        $userData = [
            'name' => $user->name,
            'phone' => $user->phone,
            'level' => $user->level ?? 'technician',
            'token' => $user->currentAccessToken()->token ?? $user->tokens->last()->token,
            'otp' => $user->otp
        ];

        // إضافة الحقول الإضافية للفني
        if ($userData['level'] === 'technician') {
            $userData['completedJobs'] = 10;
            $userData['rating'] = 4.8;
        }

        return response()->json([
            'data' => [
                'user' => $userData
            ]
        ]);
    }
}