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
        

        return response()->json([
            "message" => "Success",
            "toast_message" => "Login successful",
            "errorCode" => 0,
            "data" => [
                "user_type" => $user->user_type,
                "user_name" => $user->username,
                "user_id" => $user->id,
                "token" => $token
            ]
        ], 200);
    }

    
}
