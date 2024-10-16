<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        // $user = User::where('phone', $request->phone)->first();
        $user = User::find(1);
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout()
    {
        Auth::guard('sanctum')->user()->tokens()->delete();
        return response()->json(['message' => 'logout success'], 200);
    }
}