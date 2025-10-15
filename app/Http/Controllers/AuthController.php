<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandlesImages;


class AuthController extends Controller
{
    use HandlesImages;

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'is_available' => $validated['is_available'] ?? 0,
        ]);
        $user->assignRole('normal-user');

        if ($request->hasFile('photo')) {
            $avatarPath = $request->file('photo')->store('photos/avatars', 'public');

            $this->createSmallImage($avatarPath);

            $user->photo()->create([
                'path' => $avatarPath,
            ]);
        } else {
            $defaultPath = 'photos/avatars/default-avatar.jpg';
            $user->photo()->create([
                'path' => $defaultPath,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->load('photo'),
            'roles' => $user->getRoleNames(),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'User registered successfully');

    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->sendError('Invalid credentials');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->load('photo'),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'User logged in successfully');
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'User logged out successfully');
    }
}
