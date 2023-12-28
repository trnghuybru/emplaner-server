<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update([
            'avatar' => $avatarPath,
        ]);

        return response()->json([
            'status' => 200,
            'data' => new UserResource($user),
        ]);
    }
    //delete image
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update([
                'avatar' => null,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Avatar deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No avatar found to delete',
            ], 404);
        }
    }
}
