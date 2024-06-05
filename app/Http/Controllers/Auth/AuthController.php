<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::firstWhere('email', $request['email']);

        if ($user && Hash::check($request['password'], $user->password)) 
        {
            return response()->streamJson([
                'user' => $user,
                'token' => $user->createToken('remember_token')->plainTextToken,
                'message' => 'User logged in successfully'
            ], 200);
        } 
        else {
            return response()->streamJson([
                'message' => 'failed'
            ], 400);
        }
    }

    public function signup(SignUpRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => RoleEnum::PRODUCT_OWNER->value,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password)
        ]);

        return response()->streamJson([
            'message' => 'User signed up successfully'
        ], 201);
    }

    public function logout()
    {
        auth('sanctum')->user()->tokens()->delete();

        return response()->streamJson([
            'message' => 'User logged out successfully'
        ]);
    }
}
