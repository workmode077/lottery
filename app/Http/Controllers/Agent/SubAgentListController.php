<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SubAgentListController extends Controller
{
    public function index(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can access this.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Fetch sub-agents for this agent
        $subAgents = $authUser->subAgents()
            ->select('id', 'username', 'daily_credit_limit', 'weekly_credit_limit', 'super_rate', 'super_commission_rate', 'a_rate', 'a_commission_rate', 'b_rate', 'b_commission_rate', 'c_rate', 'c_commission_rate', 'ab_rate', 'ab_commission_rate', 'ac_rate', 'ac_commission_rate', 'bc_rate', 'bc_commission_rate', 'box_rate', 'box_commission_rate')
            ->get();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Sub-agents fetched successfully",
            "errorCode" => 0,
            "data" => [
                "sub_agents" => $subAgents,
                "total_count" => $subAgents->count()
            ]
        ], 200);
    }
}
