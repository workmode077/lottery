<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserGame;
use App\Models\GameCountLimit;
use App\Models\NumberCountLimit;
use Illuminate\Support\Facades\DB;


class SubAgentController extends Controller
{
  public function subAgentCreate(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can create sub-agents.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Validation
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'daily_credit_limit' => 'nullable|numeric|min:0',
            'weekly_credit_limit' => 'nullable|numeric|min:0',

            // 🎮 Games
            'games' => 'nullable|array',
            'games.*' => 'integer|exists:games,id',   // ✅ exist check
        ], [
            'confirm_password.same' => 'Confirm password must match password',
        ]);

        DB::beginTransaction();

        try {

            // Create sub-agent
            $subAgent = User::create([
                'username' => $request->username,
                'user_type' => 'sub_agent',
                'parent_id' => $authUser->id,
                'password' => Hash::make($request->password),
                'plain_password' => $request->password,
                'daily_credit_limit' => $request->daily_credit_limit,
                'weekly_credit_limit' => $request->weekly_credit_limit,
            ]);

            // 🎮 Assign games
            if ($request->filled('games')) {
                $gameRows = [];

                foreach ($request->games as $gameId) {
                    $gameRows[] = [
                        'user_id' => $subAgent->id,
                        'game_id' => $gameId,
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                UserGame::insert($gameRows); // bulk insert
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Sub-agent created successfully with games",
                "errorCode" => 0,
                "data" => [
                    "sub_agent" => $subAgent,
                    "games_assigned" => $request->games ?? []
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to create sub-agent",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

  public function subAgentEdit(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can edit sub-agents.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Validation
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',
            'username' => 'nullable|string|max:255|unique:users,username,' . $request->sub_agent_id,
            'password' => 'nullable|string|min:6',
            'confirm_password' => 'nullable|string|min:6|same:password',
            'daily_credit_limit' => 'nullable|numeric|min:0',
            'weekly_credit_limit' => 'nullable|numeric|min:0',

            // 🎮 Games
            'games' => 'nullable|array',
            'games.*' => 'integer|exists:games,id',
        ], [
            'confirm_password.same' => 'Confirm password must match password',
        ]);

        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        DB::beginTransaction();

        try {

            // Update fields
            if ($request->filled('username')) {
                $subAgent->username = $request->username;
            }

            if ($request->filled('password')) {
                $subAgent->password = Hash::make($request->password);
                $subAgent->plain_password = $request->password;
            }

            if ($request->has('daily_credit_limit')) {
                $subAgent->daily_credit_limit = $request->daily_credit_limit;
            }

            if ($request->has('weekly_credit_limit')) {
                $subAgent->weekly_credit_limit = $request->weekly_credit_limit;
            }

            $subAgent->save();

            // 🎮 Sync games
            if ($request->has('games')) {

                // Soft delete old
                UserGame::where('user_id', $subAgent->id)->delete();

                // Insert new
                if (!empty($request->games)) {
                    $rows = [];
                    foreach ($request->games as $gameId) {
                        $rows[] = [
                            'user_id' => $subAgent->id,
                            'game_id' => $gameId,
                            'status' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    UserGame::insert($rows);
                }
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Sub-agent updated successfully with games",
                "errorCode" => 0,
                "data" => [
                    "sub_agent" => $subAgent,
                    "games" => $request->games ?? 'unchanged'
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update sub-agent",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

  public function subAgentSaleCommission(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can edit sub-agents.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Validation (ONLY rate fields)
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',

            'super_rate' => 'nullable|numeric|min:0',
            'super_commission_rate' => 'nullable|numeric|min:0',

            'a_rate' => 'nullable|numeric|min:0',
            'a_commission_rate' => 'nullable|numeric|min:0',

            'b_rate' => 'nullable|numeric|min:0',
            'b_commission_rate' => 'nullable|numeric|min:0',

            'c_rate' => 'nullable|numeric|min:0',
            'c_commission_rate' => 'nullable|numeric|min:0',

            'ab_rate' => 'nullable|numeric|min:0',
            'ab_commission_rate' => 'nullable|numeric|min:0',

            'bc_rate' => 'nullable|numeric|min:0',
            'bc_commission_rate' => 'nullable|numeric|min:0',

            'ac_rate' => 'nullable|numeric|min:0',
            'ac_commission_rate' => 'nullable|numeric|min:0',

            'box_rate' => 'nullable|numeric|min:0',
            'box_commission_rate' => 'nullable|numeric|min:0',
        ]);

        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        DB::beginTransaction();

        try {

            // ✅ Update only provided fields
            $fields = [
                'super_rate','super_commission_rate',
                'a_rate','a_commission_rate',
                'b_rate','b_commission_rate',
                'c_rate','c_commission_rate',
                'ab_rate','ab_commission_rate',
                'bc_rate','bc_commission_rate',
                'ac_rate','ac_commission_rate',
                'box_rate','box_commission_rate',
            ];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $subAgent->$field = $request->$field;
                }
            }

            $subAgent->save();

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Sub-agent sale commission updated successfully",
                "errorCode" => 0,
                "data" => [
                    "sub_agent_id" => $subAgent->id,
                    "rates_updated" => collect($fields)->filter(fn($f) => $request->has($f))->values()
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update sale commission",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }


    public function subAgentGameCountLimit(Request $request)
    {
        $authUser = $request->user();

        // Auth check
        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can manage sub-agents.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Validation
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',
            'limits' => 'required|array|min:1',

            'limits.*.game_id' => 'required|integer',
            'limits.*.type' => 'required|string|in:super,box,a,b,c,ab,ac,bc',
            'limits.*.number' => 'required|integer|min:0',
            'limits.*.count' => 'required|integer|min:0',
        ]);

        // Ownership check
        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        DB::beginTransaction();

        try {
            foreach ($request->limits as $limit) {

                GameCountLimit::updateOrCreate(
                    [
                        'user_id' => $subAgent->id,
                        'game_id' => $limit['game_id'],
                        'type'    => $limit['type'],
                        'number'  => $limit['number'],
                    ],
                    [
                        'count' => $limit['count'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Game count limits saved successfully",
                "errorCode" => 0,
                "data" => [
                    "total_saved" => count($request->limits)
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to save game count limits",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
    

    public function subAgentNumberCountLimit(Request $request)
    {
        $authUser = $request->user();

        // Auth check
        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can manage sub-agents.",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        // Validation
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',
            'limits' => 'required|array|min:1',

            'limits.*.game_id' => 'required|integer',
            'limits.*.type' => 'required|string|in:super,box,a,b,c,ab,ac,bc',
            'limits.*.number' => 'required|integer|min:0',
            'limits.*.count' => 'required|integer|min:0',
        ]);

        // Ownership check
        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        DB::beginTransaction();

        try {
            foreach ($request->limits as $limit) {

                NumberCountLimit::updateOrCreate(
                    [
                        'user_id' => $subAgent->id,
                        'game_id' => $limit['game_id'],
                        'type'    => $limit['type'],
                        'number'  => $limit['number'],
                    ],
                    [
                        'count' => $limit['count'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Number count limits saved successfully",
                "errorCode" => 0,
                "data" => [
                    "total_saved" => count($request->limits)
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to save number count limits",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
}
