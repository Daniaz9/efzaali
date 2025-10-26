<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterCustomerRequest;
use App\Http\Requests\RegisterDriverRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandlesImages;


class AuthController extends Controller
{
    use HandlesImages;

    public function registerCustomer(RegisterCustomerRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'lat' => $validated['lat'] ?? null,
            'long' => $validated['long'] ?? null,
            'address' => $validated['address'] ?? null,        ]);

        $user->assignRole(\Spatie\Permission\Models\Role::findByName('customer', 'sanctum'));

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

        $user->load('photo', 'roles');

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully');
    }

    public function registerDriver(RegisterDriverRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'vehicle_type' => $validated['vehicle_type'],
            'license_plate' => $validated['license_plate'],
            'is_available' => $validated['is_available'] ?? 0,
            'lat' => $validated['lat'] ?? null,
            'long' => $validated['long'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $user->assignRole('driver');

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

        $user->load('photo', 'roles');

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Driver registered successfully');
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
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'User logged in successfully');
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'User logged out successfully');
    }
}
