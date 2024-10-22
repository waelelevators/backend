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
                    'completedJobs' => 10,
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
                'rating' => 4.8,
                'token' => $userToken
            ]
        ]]);
    }

    public function otp(Request $request)
    {
        return $request->phone;
    }

    public function verifyOtp(Request $request)
    {
        return $request->otp;
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}