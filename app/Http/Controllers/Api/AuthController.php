<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'unique:users,email'],
            'password'=> ['required','string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:student,teacher'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'User Created Successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if(! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'message' => 'Login Successful',
            'token' => $token,
            'user' => Auth::guard('api')->user(),
        ]);
    }

    public function me()
    {
        return response()->json([
            'user' => Auth::guard('api')->user()
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logged Out Successfully'
        ]);
    }
}
