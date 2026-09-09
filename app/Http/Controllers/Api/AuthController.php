<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // email ve sifre dogruysa token uret
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json(['error' => 'email veya sifre yanlis'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer'
        ]);
    }
}