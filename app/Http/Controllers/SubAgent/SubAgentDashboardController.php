<?php

namespace App\Http\Controllers\SubAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubAgentDashboardController extends Controller
{
  public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Prepare game plans only if sub_agent
        $gamePlans = [];
        if ($user->user_type === 'sub_agent' || $user->user_type == 2) {
            // load relation safely
            $user->load(['games' => function ($query) {
                $query->where('games.status', true)
                      ->wherePivot('status', true);
            }]);

            $gamePlans = $user->games->map(function ($g) {
                return [
                    'id' => $g->id,
                    'name' => $g->time
                ];
            })->values()->all();
        }

        // Dummy banners (live online placeholders)
        $banners = [
            "https://via.placeholder.com/600x200.png?text=Banner+1",
            "https://via.placeholder.com/600x200.png?text=Banner+2"
        ];

        return response()->json([
            "message" => "Success",
            "toast_message" => "",
            "errorCode" => 0,
            "data" => [
                "user_type" => $user->user_type,
                "user_id" => $user->id,
                "game_plan" => $gamePlans,
                "banner" => $banners
            ]
        ], 200);
    }


}
