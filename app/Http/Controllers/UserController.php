<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HandlesImages;


class UserController extends Controller
{
    use HandlesImages;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(UpdateUserRequest $request, User $user)
//    {
//        $validated = $request->validated();
//
//        if ($request->hasFile('avatar')) {
//            $originalAvatar = $user->getOriginal('avatar');
//            $this->deleteImageAndSmall($originalAvatar);
//
//            $path = $request->file('avatar')->store('avatars', 'public');
//            $this->createSmallImage($path);
//            $validated['avatar'] = $path;
//        }
//
//        if (!empty($validated)) {
//            $user->update($validated);
//        }
//
//        return $this->sendResponse($user, 'User updated successfully');
//    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $originalAvatar = $user->getOriginal('avatar');
        $this->deleteImageAndSmall($originalAvatar);
        $user->delete();

        return $this->sendResponse([], 'User deleted successfully');
    }
}
