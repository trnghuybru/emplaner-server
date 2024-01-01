<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

//     public function updateProfile(Request $request)
// {
//     $request->validate([
//         'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
//         'name' => 'string|max:255',
//         'job' => 'string|max:255',
//     ]);

//     $user = Auth::user();

//     if ($request->hasFile('avatar')) {
//         $request->validate([
//             'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//         ]);

//         // Xử lý ảnh đại diện
//         $avatarPath = $request->file('avatar')->store('avatars', 'public');

//         // Xóa ảnh cũ (nếu có)
//         if ($user->avatar) {
//             Storage::disk('public')->delete($user->avatar);
//         }

//         // Cập nhật đường dẫn ảnh mới
//         $user->avatar = $avatarPath;
//     }

//     // Cập nhật tên và công việc
//     if ($request->filled('name')) {
//         $user->name = $request->input('name');
//     }

//     if ($request->filled('job')) {
//         $user->job = $request->input('job');
//     }

//     // Lưu các thay đổi
//     $user->save();

//     return response()->json([
//         'status' => 200,
//         'data' => new UserResource($user),
//     ]);
// }

public function updateProfile(Request $request)
{
    $request->validate([
        'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'name' => 'string|max:255',
        'job' => 'string|max:255',
    ]);

    $user = Auth::user();

    try {
        // Check if the request has the 'avatar' file
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Generate a unique filename
            $avatarPath = Str::random(32) . '.' . $request->file('avatar')->getClientOriginalExtension();

            // Xóa ảnh cũ (nếu có)
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Save the image in the storage folder
            Storage::disk('public')->put($avatarPath, file_get_contents($request->file('avatar')->getRealPath()));

            // Cập nhật đường dẫn ảnh mới
            $user->avatar = $avatarPath;
        }

        // Cập nhật tên và công việc
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        if ($request->filled('job')) {
            $user->job = $request->input('job');
        }

        // Lưu các thay đổi
        $user->save();
        return response()->json([
            'status' => 200,
            'data' => [
                'user' => new UserResource($user),
                'avatar' => asset("storage/{$user->avatar}"), 
                        ],
        ]);

    } catch (\Exception $e) {
        // Return JSON response for any exception
        return response()->json([
            'status' => 500,
            'message' => 'Something went really wrong',
            'error' => $e->getMessage(), // Add the error message for debugging
        ], 500);
    }
}
// public function updateProfile(PostStoreRequest $request)
// {
//     try {
//         $user = Auth::user();

//         // Check if the request has the 'image' file
//         if ($request->hasFile('image')) {
//             $image = $request->file('image');

//             // Generate a unique filename
//             $imageName = Str::random(32) . "." . $image->getClientOriginalExtension();

//             // Update user profile
//             $user->update([
//                 'name' => $request->name,
//                 'job' => $request->job,
//                 'avatar' => $imageName
//             ]);

//             // Save the image in the storage folder
//             Storage::disk('public')->put($imageName, file_get_contents($image->getRealPath()));

//             // Return JSON response
//             return response()->json([
//                 'message' => 'Profile updated successfully',
//             ], 200);
//         } else {
//             // Return JSON response if no 'image' in the request
//             return response()->json([
//                 'message' => 'No image file provided',
//             ], 400);
//         }
//     } catch (Exception $e) {
//         // Return JSON response for any exception
//         return response()->json([
//             'message' => 'Something went really wrong',
//         ], 500);
//     }
// }

// public function updateProfile1(PostStoreRequest $request)
// {
//     try {
//         $user = Auth::user();

//         // Check if the request has the 'image' file
//         if ($request->hasFile('image')) {
//             $image = $request->file('image');

//             // Generate a unique filename
//             $imageName = Str::random(32) . "." . $image->getClientOriginalExtension();

//             // Delete the old image if it exists
//             if ($user->avatar) {
//                 Storage::disk('public')->delete($user->avatar);
//             }

//             // Save the image in the storage folder
//             $isSaved = Storage::disk('public')->put($imageName, file_get_contents($image->getRealPath()));

//             // Check if the image was saved successfully
//             if ($isSaved) {
//                 // Update user profile
//                 $user->update([
//                     'name' => $request->name,
//                     'job' => $request->job,
//                     'avatar' => $imageName
//                 ]);

//                 // Return JSON response
//                 return response()->json([
//                     'message' => 'Profile updated successfully',
//                     'avatar_url' => asset("storage/{$imageName}")
//                 ], 200);
//             } else {
//                 // Return JSON response if the image couldn't be saved
//                 return response()->json([
//                     'message' => 'Failed to save the image',
//                 ], 500);
//             }
//         } else {
//             // Return JSON response if no 'image' in the request
//             return response()->json([
//                 'message' => 'No image file provided',
//             ], 400);
//         }
//     } catch (\Exception $e) {
//         // Return JSON response for any exception
//         return response()->json([
//             'message' => 'Something went really wrong',
//             'error' => $e->getMessage(), // Add the error message for debugging
//         ], 500);
//     }
// }

}
