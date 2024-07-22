<?php

namespace App\Http\Controllers;

use App\Models\RuleCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;
            $setting =  DB::table('settings')->first();

            $user->getPermissionNames  = $this->getPermissionNames($user->rule_category_id);
            return response()->json(['user' => $user, "token" => $token, 'setting' => $setting]);
        } else  return response()->json(['message' => 'بيانات الدخول خاطئه'], 401);
    }


    public function logout()
    {
        Auth::guard('sanctum')->user()->tokens()->delete();
        return response()->json(['message' => 'logout success'], 200);
    }

    function getPermissionNames($id)
    {
        return RuleCategory::with('rules.rule')
            ->where('id', $id)
            ->get()
            ->flatMap(fn ($category) => $category->rules->pluck('rule.name'))
            ->toArray();
    }
}
