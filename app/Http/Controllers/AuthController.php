<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully. You can now Log In !',
            'token' => $token,
            'user' => $user,
        ], 201);
    }
    public function getUser(){
        $user = auth()->user();
        return response()->json([
            'message' => 'Get user successfully',
            'user' => $user,
        ], 201);
    }
}
