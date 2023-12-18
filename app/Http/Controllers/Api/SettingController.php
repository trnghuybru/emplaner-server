<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Traits\CanLoadRelationships;
class SettingController extends Controller
{

    use CanLoadRelationships;

    private $relations = []; // Thêm các quan hệ nếu cần

    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }
  
    public function update(Request $request,string $id)
    {
        $authUser = auth()->user();
    
        if ($authUser && $authUser->id == $id) {
            $request->validate([
                'name' => 'required|string',
                'job' => 'nullable|string',
                'avatar' => 'nullable|string',
            ]);
    
            $user = User::findOrFail($id);
    
            $this->authorize('update', $user);
    
            $user->update([
                'name' => $request->input('name'),
                'job' => $request->input('job'),
                'avatar' => $request->input('avatar'),
            ]);
    
            $userResource = new UserResource($user);
    
            return response()->json([
                'status' => 200,
                'message' => 'User information updated successfully',
                'data' => $userResource->toArray($request),
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden: You do not have permission to update this user',
            ], 403);
        }
    }
}
