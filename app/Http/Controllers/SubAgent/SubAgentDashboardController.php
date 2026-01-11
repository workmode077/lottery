<?php

namespace App\Http\Controllers\SubAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubAgentDashboardController extends Controller
{
  public function index(Request $request)
    {
        $user = $request->user();
       if (!$user || $user->user_type !== 'sub_agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Load user's games and parent (agent)
        $user->load(['games', 'agent']);

        return response()->json([
            "message" => "Success",
            "toast_message" => "",
            "errorCode" => 0,
            "data" => [
                "user" => $user,
                 ]
        ], 200);
    }


}
