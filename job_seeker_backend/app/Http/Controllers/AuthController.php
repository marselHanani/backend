<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteUnverifiedUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Mail\ResetPass;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        Mail::to($user->email)->send(new VerifyEmail($user));
        DeleteUnverifiedUser::dispatch($user->id)->delay(now()->addMinutes(10));
        return response()->json([
            'message' => 'Verification email sent, Please verify your email within 10 minutes',
        ], 201);
    }
    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->is_verified = true;
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function Login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);
        $user = User::where('username', $credentials['username'])->first();
        if (!$user || !$user->is_verified) {
            return response()->json(['message' => 'Invalid credentials or user not verified.'], 401);
        }
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }
        return response()->json(['token' => $token,'message'=>'Login successfully']);
    }
    public function forgetPass(Request $request){
        $user = User::where("email",$request->email)->first();
        if($user){
            Mail::to($user->email)->send(new ResetPass($user));
            return response()->json(["message"=> "Reset password email sent"]);
        }
        return response()->json(["message"=> "Email not found"]);
    }
    public function resetPass(Request $request){
        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(["message"=> "Password reset successfully"]);
    }
    public function googleRegister(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string',
            'image' => 'nullable|string',
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                "first_name" => "Google",
                "last_name" => "User",
                'username' => $request->username,
                'email' => $request->email,
                'image' => $request->image ?? null,
                'password' => bcrypt(uniqid()),
                'is_verified' => true,
                'email_verified_at' => now(),
                'role_id' => '3',
            ]);
        }
        $token = auth('api')->login($user) ?? null;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function googleLogin(Request $request){
        $request->validate([
            'email' =>'required|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 401);
        }
        $token = auth('api')->login($user)?? null;
        return response()->json([
            'token' => $token,
        ]);
    }
}
