<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Load subagents with their games
        $user->load(['subAgents.games']);

        return response()->json([
            "message" => "Success",
            "toast_message" => "",
            "errorCode" => 0,
            "data" => [
                "user" => $user,
                "sub_agents" => $user->subAgents
            ]
        ], 200);
    }
}
