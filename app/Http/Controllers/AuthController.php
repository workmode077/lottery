<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username
        $user = User::where('username', $request->user_name)
                    ->where('status', true)
                    ->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Success',
                'toast_message' => 'invalid username',
                'errorCode' => 0,
                'data' => null
            ], 401);
        }

        // Map user_type to numeric value
        $userTypeMap = [
            'super_agent' => 1,
            'agent' => 2,
            'sub_agent' => 3,
        ];

        // Create token
        $token = $user->createToken('auth-token')->accessToken;

        return response()->json([
            'message' => 'Success',
            'toast_message' => 'Login successful',
            'errorCode' => 0,
            'data' => [
                'user_type' => $userTypeMap[$user->user_type] ?? 0,
                'user_name' => $user->username,
                'user_id' => $user->id,
                'token' => $token
            ]
        ], 200);
    }
}
