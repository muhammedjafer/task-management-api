<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\AuthRequest;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
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
}
