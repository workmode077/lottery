<?php

namespace App\Http\Controllers\PrivateAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserListController extends Controller
{
    public function superAgentList(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'private_agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only private_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $superAgents = User::where('user_type', 'super_agent')
            ->get(['id', 'username', 'user_type', 'status', 'created_at']);

        return response()->json([
            "message" => "Success",
            "toast_message" => "Super agent list fetched successfully",
            "errorCode" => 0,
            "data" => $superAgents
        ], 200);
    }

    public function agentList(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'private_agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only private_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $query = User::where('user_type', 'agent');

        // Optional filter by parent super_agent
        if ($request->filled('super_agent_id')) {
            $query->where('parent_id', $request->super_agent_id);
        }

        $agents = $query->get(['id', 'username', 'user_type', 'parent_id', 'status', 'created_at']);

        return response()->json([
            "message" => "Success",
            "toast_message" => "Agent list fetched successfully",
            "errorCode" => 0,
            "data" => $agents
        ], 200);
    }

    public function subAgentList(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'private_agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only private_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $query = User::where('user_type', 'sub_agent');

        // Optional filter by parent agent
        if ($request->filled('agent_id')) {
            $query->where('parent_id', $request->agent_id);
        }

        $subAgents = $query->get(['id', 'username', 'user_type', 'parent_id', 'status', 'created_at']);

        return response()->json([
            "message" => "Success",
            "toast_message" => "Sub-agent list fetched successfully",
            "errorCode" => 0,
            "data" => $subAgents
        ], 200);
    }
}
