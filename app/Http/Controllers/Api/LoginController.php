<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            /*$token = Str::random(60);
            $user->api_token = $token;
            $user->save();*/

            $token = $user->createToken($request->device_name, [
                'products'
            ]);

            return [
                'token' => $token,
            ];
        }

        return response()->json([
            'error' => 'Invalid username or password',
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        /*$user->api_token = null;
        $user->save();*/

        //$user->tokens()->delete();
        $user->currentAccesssToken()->delete();
        return [
            'logout' => 1,
        ];
    }
}
