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

        $user = User::where('username', $request->user_name)
                    ->where('status', true)
                    ->first();

        // Validate user & password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Success",
                "toast_message" => "invalid username",
                "errorCode" => 2,
                "data" => null
            ], 401);
        }

        $token = $user->createToken('auth-token')->accessToken;

        // Map user type return values
        $userTypeCode = $user->user_type === 'agent' ? 1 : 2;

        return response()->json([
            "message" => "Success",
            "toast_message" => "Login successful",
            "errorCode" => 0,
            "data" => [
                "user_type" => $userTypeCode,
                "user_name" => $user->username,
                "user_id" => $user->id,
                "token" => $token
            ]
        ], 200);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'user_name' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // Find user by username
    //     $user = User::where('username', $request->user_name)
    //                 ->where('status', true)
    //                 ->first();

    //     // Check if user exists and password is correct
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'message' => 'Success',
    //             'toast_message' => 'invalid username',
    //             'errorCode' => 0,
    //             'data' => null
    //         ], 401);
    //     }

        

       

    //     // Create token
    //     $token = $user->createToken('auth-token')->accessToken;

    //     // Load games for sub_agent users
    //     if ($user->user_type === 'sub_agent') {
    //         $user->load(['games' => function ($query) {
    //             $query->where('games.status', true)
    //                   ->wherePivot('status', true);
    //         }]);
    //     }

    //     return response()->json([
    //         'message' => 'Success',
    //         'toast_message' => 'Login successful',
    //         'errorCode' => 0,
    //         'data' => $user,
    //         'token' => $token
    //     ], 200);
    // }
}
