<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Game;
use App\Models\Price;
use App\Models\UserGame;
use App\Models\GameCountLimit;
use App\Models\NumberCountLimit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class SubAgentController extends Controller
{
   public function index(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $subAgents = $authUser->subAgents()
            // ->where('status', true)
            ->select('id', 'username', 'status')
            ->get();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Active sub-agents fetched successfully",
            "errorCode" => 0,
            "data" => [
                "sub_agents" => $subAgents,
                "total_count" => $subAgents->count()
            ]
        ], 200);
    }


    public function viewSingleSubAgent(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $subAgent = User::where('id', $id)
            ->where('user_type', 'sub_agent')
            ->where('status', true)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found",
                "errorCode" => 1,
                 "data" => (object)[],
            ], 404);
        }

        /* ✅ GAME TIMING */
        $games = Game::where('status', true)->get();

        $assignedGameIds = UserGame::where('user_id', $subAgent->id)
            ->pluck('game_id')
            ->toArray();

        $subAgent->game_timeing = $games->map(function ($game) use ($assignedGameIds) {
            return [
                "game_id" => $game->id,
                "time"    => $game->time,
                "active"  => in_array($game->id, $assignedGameIds)
            ];
        });

        $price = Price::first();

        /* ✅ PRICE COMMISSION */
        $subAgent->price_commission = [
            "editable" => true,
            "lsk_super" => [
                "first_price" => $price->lsk_super_first_price,
                "first_price_commission" => $subAgent->lsk_super_first_price_commission,
                "second_price" => $price->lsk_super_second_price,
                "second_price_commission" => $subAgent->lsk_super_second_price_commission,
                "third_price" => $price->lsk_super_third_price,
                "third_price_commission" => $subAgent->lsk_super_third_price_commission,
                "fourth_price" => $price->lsk_super_fourth_price,
                "fourth_price_commission" => $subAgent->lsk_super_fourth_price_commission,
                "fifth_price" => $price->lsk_super_fifth_price,
                "fifth_price_commission" => $subAgent->lsk_super_fifth_price_commission,
                "sixth_price" => $price->lsk_super_sixth_price,
                "sixth_price_commission" => $subAgent->lsk_super_sixth_price_commission,
                "lsk_30" => $price->lsk_super_lsk_30,
                "seventh_price" => $price->lsk_super_seventh_price,
                "seventh_price_commission" => $subAgent->lsk_super_seventh_price_commission,
                "lsk_50" => $price->lsk_super_lsk_50,
            ],
            "box" => [
                "three_different_number" => [
                    "first_price" => $price->box_three_diff_first_price,
                    "first_price_commission" => $subAgent->box_three_diff_first_price_commission,
                    "second_price" => $price->box_three_diff_second_price,
                    "second_price_commission" => $subAgent->box_three_diff_second_price_commission,
                ],

                "two_same_number" => [
                    "first_price" => $price->box_two_same_first_price,
                    "first_price_commission" => $subAgent->box_two_same_first_price_commission,
                    "second_price" => $price->box_two_same_second_price,
                    "second_price_commission" => $subAgent->box_two_same_second_price_commission,
                ],

                "three_same_number" => [
                    "first_price" => $price->box_three_same_first_price,
                    "first_price_commission" => $subAgent->box_three_same_first_price_commission,
                ],
            ],

            "abc" => [
                    "first_price" => $price->abc_first_price,
                    "first_price_commission" => $subAgent->abc_first_price_commission,
                    "second_price" => $price->abc_second_price,
                    "second_price_commission" => $subAgent->abc_second_price_commission,
            ],
            "ab_ac_bc" => [
                    "first_price" => $price->ab_ac_bc_first_price,
                    "first_price_commission" => $subAgent->ab_ac_bc_first_price_commission,
                    "second_price" => $price->ab_ac_bc_second_price,
                    "second_price_commission" => $subAgent->ab_ac_bc_second_price_commission,
            ],
        ];

        /* ✅ SALES COMMISSION */
        $subAgent->sales_commission = [
            "editable" => true,
            "super_rate" => $subAgent->super_rate,
            "super_commission_rate" => $subAgent->super_commission_rate,
            "a_rate"       => $subAgent->a_rate,
            "a_commission_rate"       => $subAgent->a_commission_rate,
            "b_rate"       => $subAgent->b_rate,
            "b_commission_rate"  => $subAgent->b_commission_rate,
            "c_rate"       => $subAgent->c_rate,
            "c_commission_rate"       => $subAgent->c_commission_rate,
            "ab_rate"       => $subAgent->ab_rate,
            "ab_commission_rate"       => $subAgent->ab_commission_rate,
            "bc_rate"       => $subAgent->bc_rate,
            "bc_commission_rate"       => $subAgent->bc_commission_rate,
            "ac_rate"       => $subAgent->ac_rate,
            "ac_commission_rate"       => $subAgent->ac_commission_rate,
            "box_rate"       => $subAgent->box_rate,
            "box_commission_rate"       => $subAgent->box_commission_rate,
        ];

        /* ✅ GAME COUNT LIMIT */
        $subAgent->game_count_limit = [
            "editable" => $subAgent->game_count_editable,
            "all_dear" => $subAgent->game_count_all_dear,
            "all_game" => $subAgent->game_count_all_game,
            "super"    => $subAgent->game_count_super,
            "box"      => $subAgent->game_count_box,
            "a"        => $subAgent->game_count_a,
            "b"        => $subAgent->game_count_b,
            "c"        => $subAgent->game_count_c,
            "ab"       => $subAgent->game_count_ab,
            "ac"       => $subAgent->game_count_ac,
            "bc"       => $subAgent->game_count_bc,
        ];

        /* ✅ NUMBER COUNT LIMIT (NEW PART) */
        $numberLimits = NumberCountLimit::where('user_id', $subAgent->id)
            ->get()
            ->map(function ($row) {
                return [
                    "game_id" => $row->game_id,
                    "type"    => $row->type,
                    "number"  => $row->number,
                    "count"   => $row->count,
                ];
            });

        $subAgent->number_count_limit = [
            "editable" => true,
            "limits"   => $numberLimits
        ];

        return response()->json([
        "message" => "Success",
        "toast_message" => "Sub-agent fetched successfully",
        "errorCode" => 0,
        "data" => [
            "id" => $subAgent->id,
            "username" => $subAgent->username,
            "user_type" => $subAgent->user_type,
            "parent_id" => $subAgent->parent_id,
            "agent" => $authUser->username,
            'plain_password' => $subAgent->plain_password,
            "daily_credit_limit" => $subAgent->daily_credit_limit,
            "weekly_credit_limit" => $subAgent->weekly_credit_limit,
            "monthly_credit_limit" => $subAgent->monthly_credit_limit,
            "yearly_credit_limit" => $subAgent->yearly_credit_limit,
            "status" => $subAgent->status,
            "created_at" => $subAgent->created_at,
            "updated_at" => $subAgent->updated_at,

            "game_timeing" => $subAgent->game_timeing,
            "price_commission" => $subAgent->price_commission,
            "sales_commission" => $subAgent->sales_commission,
            "game_count_limit" => $subAgent->game_count_limit,
            "number_count_limit" => $subAgent->number_count_limit,
        ]
    ], 200);
    }







  public function subAgentCreate(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can create sub-agents.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 401);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'daily_credit_limit' => 'nullable|numeric|min:0',
            'weekly_credit_limit' => 'nullable|numeric|min:0',
            'games' => 'nullable|array',
            'games.*' => 'integer|exists:games,id',
        ], [
            'confirm_password.same' => 'Confirm password must match password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $validator->errors()->first(),
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }

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
                "errorCode" => 0,
                "error" => $e->getMessage(),
                "data" => (object)[]
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
                 "data" => (object)[],
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
                "data" => (object)[]
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
                "sub_agent_id" => $subAgent->id,
                "username" => $subAgent->username,
                 "plain_password" => $subAgent->plain_password,
                "daily_credit_limit" => $subAgent->daily_credit_limit,
                "weekly_credit_limit" => $subAgent->weekly_credit_limit,
                "games" => UserGame::where('user_id', $subAgent->id)
                            ->pluck('game_id')
                            ->toArray(),
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update sub-agent",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
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
                "data" => (object)[]
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
                "data" => (object)[]
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

                "super_rate" => $subAgent->super_rate,
                "super_commission_rate" => $subAgent->super_commission_rate,

                "a_rate" => $subAgent->a_rate,
                "a_commission_rate" => $subAgent->a_commission_rate,

                "b_rate" => $subAgent->b_rate,
                "b_commission_rate" => $subAgent->b_commission_rate,

                "c_rate" => $subAgent->c_rate,
                "c_commission_rate" => $subAgent->c_commission_rate,

                "ab_rate" => $subAgent->ab_rate,
                "ab_commission_rate" => $subAgent->ab_commission_rate,

                "bc_rate" => $subAgent->bc_rate,
                "bc_commission_rate" => $subAgent->bc_commission_rate,

                "ac_rate" => $subAgent->ac_rate,
                "ac_commission_rate" => $subAgent->ac_commission_rate,

                "box_rate" => $subAgent->box_rate,
                "box_commission_rate" => $subAgent->box_commission_rate,
            ]
        ], 200);


        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update sale commission",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }


    public function subAgentPriceCommission(Request $request)
    {
        $authUser = $request->user();

        // ✅ Only agent can update
        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can edit sub-agents.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 401);
        }

        // ✅ Validation
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',

            // LSK SUPER
            'lsk_super_first_price_commission' => 'nullable|numeric|min:0',
            'lsk_super_second_price_commission' => 'nullable|numeric|min:0',
            'lsk_super_third_price_commission' => 'nullable|numeric|min:0',
            'lsk_super_fourth_price_commission' => 'nullable|numeric|min:0',
            'lsk_super_fifth_price_commission' => 'nullable|numeric|min:0',
            'lsk_super_sixth_price_commission' => 'nullable|numeric|min:0',

            'lsk_super_lsk_30' => 'nullable|boolean',
            'lsk_super_seventh_price_commission' => 'nullable|numeric|min:0',
            'lsk_super_lsk_50' => 'nullable|boolean',

            // BOX THREE DIFFERENT
            'box_three_diff_first_price_commission' => 'nullable|numeric|min:0',
            'box_three_diff_second_price_commission' => 'nullable|numeric|min:0',

            // BOX TWO SAME
            'box_two_same_first_price_commission' => 'nullable|numeric|min:0',
            'box_two_same_second_price_commission' => 'nullable|numeric|min:0',

            // BOX THREE SAME
            'box_three_same_first_price_commission' => 'nullable|numeric|min:0',

            // ABC
            'abc_first_price_commission' => 'nullable|numeric|min:0',
            'abc_second_price_commission' => 'nullable|numeric|min:0',

            // AB_AC_BC
            'ab_ac_bc_first_price_commission' => 'nullable|numeric|min:0',
            'ab_ac_bc_second_price_commission' => 'nullable|numeric|min:0',
        ]);

        // ✅ Check sub-agent belongs to this agent
        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 404);
        }

        DB::beginTransaction();

        try {

            // ✅ All commission fields list
            $fields = [
                'lsk_super_first_price_commission',
                'lsk_super_second_price_commission',
                'lsk_super_third_price_commission',
                'lsk_super_fourth_price_commission',
                'lsk_super_fifth_price_commission',
                'lsk_super_sixth_price_commission',
                'lsk_super_lsk_30',
                'lsk_super_seventh_price_commission',
                'lsk_super_lsk_50',

                'box_three_diff_first_price_commission',
                'box_three_diff_second_price_commission',

                'box_two_same_first_price_commission',
                'box_two_same_second_price_commission',

                'box_three_same_first_price_commission',

                'abc_first_price_commission',
                'abc_second_price_commission',

                'ab_ac_bc_first_price_commission',
                'ab_ac_bc_second_price_commission',
            ];

            // ✅ Update only sent fields
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $subAgent->$field = $request->$field;
                }
            }

            $subAgent->save();

            DB::commit();

            return response()->json([
            "message" => "Success",
            "toast_message" => "Sub-agent price commission updated successfully",
            "errorCode" => 0,
            "data" => [
                "sub_agent_id" => $subAgent->id,

                // ✅ LSK SUPER
                "lsk_super_first_price_commission" => $subAgent->lsk_super_first_price_commission,

                "lsk_super_second_price_commission" => $subAgent->lsk_super_second_price_commission,

                "lsk_super_third_price_commission" => $subAgent->lsk_super_third_price_commission,

                "lsk_super_fourth_price_commission" => $subAgent->lsk_super_fourth_price_commission,

                "lsk_super_fourth_price_commission" => $subAgent->lsk_super_fourth_price_commission,

                "lsk_super_fifth_price_commission" => $subAgent->lsk_super_fifth_price_commission,

                "lsk_super_sixth_price_commission" => $subAgent->lsk_super_sixth_price_commission,

                "lsk_super_lsk_30" => $subAgent->lsk_super_lsk_30,

                "lsk_super_seventh_price_commission" => $subAgent->lsk_super_seventh_price_commission,

                "lsk_super_lsk_50" => $subAgent->lsk_super_lsk_50,

                // ✅ BOX THREE DIFFERENT
                "box_three_diff_first_price_commission" => $subAgent->box_three_diff_first_price_commission,

                "box_three_diff_second_price_commission" => $subAgent->box_three_diff_second_price_commission,

                // ✅ BOX TWO SAME
                "box_two_same_first_price_commission" => $subAgent->box_two_same_first_price_commission,

                "box_two_same_second_price_commission" => $subAgent->box_two_same_second_price_commission,

                // ✅ BOX THREE SAME
                "box_three_same_first_price_commission" => $subAgent->box_three_same_first_price_commission,

                // ✅ ABC
                "abc_first_price_commission" => $subAgent->abc_first_price_commission,

                "abc_second_price_commission" => $subAgent->abc_second_price_commission,

                // ✅ AB AC BC
                "ab_ac_bc_first_price_commission" => $subAgent->ab_ac_bc_first_price_commission,

                "ab_ac_bc_second_price_commission" => $subAgent->ab_ac_bc_second_price_commission,
            ]
        ], 200);


        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update price commission",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }



    public function subAgentGameCountLimit(Request $request)
    {
        $authUser = $request->user();

        // ✅ Only agent can update
        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can manage sub-agents.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 401);
        }

        // ✅ Validation
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',

            'game_count_editable' => 'nullable|boolean',

            'game_count_all_dear' => 'nullable|boolean',
            'game_count_all_game' => 'nullable|boolean',

            'game_count_super' => 'nullable|numeric|min:0',
            'game_count_box'   => 'nullable|numeric|min:0',

            'game_count_a' => 'nullable|numeric|min:0',
            'game_count_b' => 'nullable|numeric|min:0',
            'game_count_c' => 'nullable|numeric|min:0',

            'game_count_ab' => 'nullable|numeric|min:0',
            'game_count_ac' => 'nullable|numeric|min:0',
            'game_count_bc' => 'nullable|numeric|min:0',
        ]);

        // ✅ Check sub-agent belongs to this agent
        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 404);
        }

        DB::beginTransaction();

        try {

            // ✅ Fields list
            $fields = [
                'game_count_editable',
                'game_count_all_dear',
                'game_count_all_game',

                'game_count_super',
                'game_count_box',

                'game_count_a',
                'game_count_b',
                'game_count_c',

                'game_count_ab',
                'game_count_ac',
                'game_count_bc',
            ];

            // ✅ Update only provided fields
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $subAgent->$field = $request->$field;
                }
            }

            $subAgent->save();

            DB::commit();

            // ✅ Return full saved values
            $responseData = [
                "sub_agent_id" => $subAgent->id,
            ];

            foreach ($fields as $field) {
                $responseData[$field] = $subAgent->$field;
            }

            return response()->json([
                "message" => "Success",
                "toast_message" => "Game count limits updated successfully",
                "errorCode" => 0,
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update game count limits",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }


    

 public function subAgentNumberCountLimit(Request $request)
    {
        $authUser = $request->user();

        // ✅ Auth check
        if (!$authUser || $authUser->user_type !== 'agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only agents can manage sub-agents.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 401);
        }

        // ✅ Validation
        $request->validate([
            'sub_agent_id' => 'required|exists:users,id',
            'limits' => 'required|array|min:1',

            'limits.*.game_id' => 'required|integer',
            'limits.*.type' => 'required|string|in:super,box,a,b,c,ab,ac,bc',
            'limits.*.number' => 'required|integer|min:0',
            'limits.*.count' => 'required|integer|min:0',
        ]);

        // ✅ Ownership check
        $subAgent = User::where('id', $request->sub_agent_id)
            ->where('user_type', 'sub_agent')
            ->where('parent_id', $authUser->id)
            ->first();

        if (!$subAgent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Sub-agent not found or access denied.",
                "errorCode" => 1,
                "data" => (object)[]
            ], 404);
        }

        DB::beginTransaction();

        try {

            $savedLimits = [];

            // ✅ Save each limit
            foreach ($request->limits as $limit) {

                $record = NumberCountLimit::updateOrCreate(
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

                // ✅ Collect updated record for response
                $savedLimits[] = [
                    "id"      => $record->id,
                    "game_id" => $record->game_id,
                    "type"    => $record->type,
                    "number"  => $record->number,
                    "count"   => $record->count,
                ];
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Number count limits saved successfully",
                "errorCode" => 0,
                "data" => [
                    "sub_agent_id" => $subAgent->id,
                    "total_saved"  => count($savedLimits),
                    "limits"       => $savedLimits
                ]
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to save number count limits",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }

}
