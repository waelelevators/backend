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
                    'otp' => 123456,
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
                'otp' => 123456,
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
        if (auth('sanctum')->user()->otp != $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['كود التحقق غير صحيح.'],
            ]);
        } else {
            auth('sanctum')->user()->otp = null;
            auth('sanctum')->user()->save();
            return [
                'data' => [
                    'user' => auth('sanctum')->user()
                ]
            ];
        }
        // append phone to request
        // $request->merge(['phone' => auth('sanctum')->user()->phone]);
        // $this->login($request);
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}