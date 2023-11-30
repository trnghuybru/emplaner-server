<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                "email" => "The provided credentials are incorrect."
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "email" => "The provided credentials are incorrect."
            ]);
        }

        if ($user->email_verified_at == null){
            throw ValidationException::withMessages([
                "email"=> "Your email address is not verified."
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        $user->token = $token;
        $user->save();

        return response()->json([
            "status" => 200,
            "data" => new  UserResource($user)
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Register successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'job' => 'nullable|string|max:255'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'job' => $request->job
        ]);
        $user->token = strval(random_int(100000, 999999));
        $user->save();

        $this->sendVerificationMail($user);

        return response()->json([
            'status' => 201,
            'id' => $user->id,
            'message' => 'Register successfully, please check your email to verify'
        ], 201);
    }

    protected function sendVerificationMail(User $user)
    {
        Mail::to($user->email)->send(new VerificationMail($user));
    }

    public function verify(Request $request, User $user)
    {
        $request->validate([
            'token' => 'required|numeric|min:100000|max:999999'
        ]);
        if ($request->token != $user->token) {
            return response()->json([
                'status' => 400,
                'message' => 'Incorrect code'
            ]);
        } else {
            $user->token = null;
            $user->email_verified_at = date("Y-m-d H:i:s");
            $user->save();
            return response()->json([
                "status" => 200,
                "message" => "Verified Successfully!"
            ]);
        }
    }
}
