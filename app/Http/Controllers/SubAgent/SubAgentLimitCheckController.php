<?php

namespace App\Http\Controllers\SubAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bill;

class SubAgentLimitCheckController extends Controller
{
    public function index(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }
         $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
         $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                "message" => "Error",
                "toast_message" => "User not found.",
                "errorCode" => 1,
                "data" => null
            ], 403);
        }

        // Get limits
        $dailyLimit = $user->daily_credit_limit ?? 0;
        $weeklyLimit = $user->weekly_credit_limit ?? 0;

        // Calculate daily usage (today's bills)
        $dailyBills = Bill::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->get();

        $dailyUsed = $dailyBills->sum('total_amount') ?? 0;

        // Calculate weekly usage (this week's bills)
        $weeklyBills = Bill::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        $weeklyUsed = $weeklyBills->sum('total_amount') ?? 0;

        return response()->json([
            "message" => "Success",
            "toast_message" => "Limit details fetched successfully",
            "errorCode" => 0,
            "data" => [
               
                "daily_limit" => $dailyLimit,
                "daily_used" => $dailyUsed,
                "daily_remaining" => max(0, $dailyLimit - $dailyUsed),
                "weekly_limit" => $weeklyLimit,
                "weekly_used" => $weeklyUsed,
                "weekly_remaining" => max(0, $weeklyLimit - $weeklyUsed),
            ]
        ], 200);
    }
}
