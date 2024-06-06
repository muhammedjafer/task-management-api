<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password)
        ]);

        return response()->streamJson([
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->name ?: $user->name,
            'email' => $request->email ?: $user->email,
            'role' => $request->role ?: $user->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password
        ]);

        return response()->streamJson([
            'message' => 'User updated successfully'
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->streamJson([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
