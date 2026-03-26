<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bill;
use App\Models\Game;
use App\Models\NumberCountLimit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{
    /**
     * Check if game time is valid (at least 5 minutes from now)
     */
    private function isGameTimeValid($gameId)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return ['valid' => false, 'message' => 'Game not found'];
        }

        // Get current time in IST
        $now = Carbon::now('Asia/Kolkata');

        // Create game datetime for today with game's time
        $gameDateTime = Carbon::today('Asia/Kolkata')->setTimeFromTimeString($game->time);

        // If game time has already passed today, it's not valid
        if ($gameDateTime->isPast()) {
            return ['valid' => false, 'message' => 'Game time has already passed'];
        }

        // Check if game time is at least 5 minutes from now
        $minimumAllowedTime = $now->copy()->addMinutes(5);

        if ($gameDateTime->lessThan($minimumAllowedTime)) {
            return ['valid' => false, 'message' => 'Bills can only be submitted at least 5 minutes before game time'];
        }

        return ['valid' => true, 'message' => 'Game time is valid'];
    }

    /* ============ DATATABEL ============= */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        if ($authUser->user_type === 'agent') {

            $user = User::where('id', $request->user_id)
                        ->where('parent_id', $authUser->id)
                        ->first();

            if (!$user) {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Passed user is not your sub-agent of this agent",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }

        } else {

            if ($authUser->user_type !== 'sub_agent') {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Unauthenticated",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }

            $user = $authUser;
        }





        $bills = Bill::where('user_id', $user->id)
                    ->with('items', 'game')
                    ->latest()
                    ->get();

        // Add PDF URLs to each bill
        $bills->transform(function ($bill) {
            $bill->pdf_urls = [
                "download" => url("/api/bill/{$bill->id}/pdf/download"),
            ];
            return $bill;
        });

        return response()->json([
            "message" => "Success",
            "toast_message" => "data fetched successfully",
            "errorCode" => 0,
            "data" => [
                "bills" => $bills
            ]
        ], 200);
    }
    


    

    /* ============ STORE FUNCTION  ============= */
   public function store(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }
        // Validation
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'game_id' => 'required|exists:games,id',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.game' => 'required|in:SUPER,BOX,A,B,C,AB,AC,BC',
            'items.*.number' => 'required|string',
            'items.*.count' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $validator->errors()->first(),
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }


         if ($authUser->user_type === 'agent') {

            $user = User::where('id', $request->user_id)
                        ->where('parent_id', $authUser->id)
                        ->first();

            if (!$user) {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Passed user is not your sub-agent of this agent",
                    "errorCode" => 0,
                   "data" => (object)[],
                ], 200);
            }

        } else {

            if ($authUser->user_type !== 'sub_agent') {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Unauthenticated",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }

            $user = $authUser;
        }


        // ✅ Game time check
        $gameTimeCheck = $this->isGameTimeValid($request->game_id);
        if (!$gameTimeCheck['valid']) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $gameTimeCheck['message'],
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        // ✅ Game count limit check (per game_id, per day)
        $countLimitMap = [
            'SUPER' => 'game_count_super',
            'BOX'   => 'game_count_box',
            'A'     => 'game_count_a',
            'B'     => 'game_count_b',
            'C'     => 'game_count_c',
            'AB'    => 'game_count_ab',
            'AC'    => 'game_count_ac',
            'BC'    => 'game_count_bc',
        ];

        // Group requested counts by type
        $requestedCounts = [];
        foreach ($request->items as $item) {
            $type = $item['game'];
            $requestedCounts[$type] = ($requestedCounts[$type] ?? 0) + $item['count'];
        }

        // Get existing counts for today's game_id per type (excluding soft-deleted)
        $existingCounts = DB::table('bill_items')
            ->join('bills', 'bills.id', '=', 'bill_items.bill_id')
            ->where('bills.user_id', $user->id)
            ->where('bills.game_id', $request->game_id)
            ->whereDate('bills.created_at', Carbon::today('Asia/Kolkata'))
            ->whereNull('bills.deleted_at')
            ->whereNull('bill_items.deleted_at')
            ->groupBy('bill_items.type')
            ->select('bill_items.type', DB::raw('SUM(bill_items.count) as total'))
            ->pluck('total', 'type')
            ->toArray();

        $limitErrors = [];
        foreach ($requestedCounts as $type => $newCount) {
            if (!isset($countLimitMap[$type])) continue;

            $limitField = $countLimitMap[$type];
            $limit      = (int) $user->$limitField;
            $existing   = (int) ($existingCounts[$type] ?? 0);
            $total      = $existing + $newCount;

            if ($total > $limit) {
                $remaining = max(0, $limit - $existing);
                $limitErrors[] = "{$type}(limit:{$limit},used:{$existing},left:{$remaining})";
            }
        }

        if (!empty($limitErrors)) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Limit exceeded: " . implode(', ', $limitErrors),
                "errorCode" => 0,
                 "data" => (object)[],
            ], 200);
        }

        // ✅ Number count limit check (per game_id, per type, per number, today only)
        $numberLimits = NumberCountLimit::where('user_id', $user->id)
            ->where('game_id', $request->game_id)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy(fn($row) => strtoupper($row->type) . '_' . $row->number);

        if ($numberLimits->isNotEmpty()) {

            // Get today's used counts per type+number for this game_id
            $usedNumberCounts = DB::table('bill_items')
                ->join('bills', 'bills.id', '=', 'bill_items.bill_id')
                ->where('bills.user_id', $user->id)
                ->where('bills.game_id', $request->game_id)
                ->whereDate('bills.created_at', Carbon::today('Asia/Kolkata'))
                ->whereNull('bills.deleted_at')
                ->whereNull('bill_items.deleted_at')
                ->groupBy('bill_items.type', 'bill_items.number')
                ->select('bill_items.type', 'bill_items.number', DB::raw('SUM(bill_items.count) as total'))
                ->get()
                ->keyBy(fn($row) => $row->type . '_' . $row->number)
                ->toArray();

            $numberLimitErrors = [];
            foreach ($request->items as $item) {
                $key = strtoupper($item['game']) . '_' . $item['number'];
                if (!isset($numberLimits[$key])) continue;

                $limit    = (int) $numberLimits[$key]->count;
                $existing = (int) ($usedNumberCounts[$key]->total ?? 0);
                $total    = $existing + $item['count'];

                if ($total > $limit) {
                    $remaining = max(0, $limit - $existing);
                    $numberLimitErrors[] = "{$item['game']}#{$item['number']}(limit:{$limit},used:{$existing},left:{$remaining})";
                }
            }

            if (!empty($numberLimitErrors)) {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Number limit exceeded: " . implode(', ', $numberLimitErrors),
                    "errorCode" => 0,
                    "data" => (object)[],
                ], 200);
            }
        }

        DB::beginTransaction();

        try {
            $totalRate = 0;
            $totalCommission = 0;
            $totalCount = 0;
            $totalAmount = 0;

            // 🔑 Rate mapping
            $rateMap = [
                'SUPER' => ['rate' => 'super_rate', 'commission' => 'super_commission_rate'],
                'BOX'   => ['rate' => 'box_rate',   'commission' => 'box_commission_rate'],
                'A'     => ['rate' => 'a_rate',     'commission' => 'a_commission_rate'],
                'B'     => ['rate' => 'b_rate',     'commission' => 'b_commission_rate'],
                'C'     => ['rate' => 'c_rate',     'commission' => 'c_commission_rate'],
                'AB'    => ['rate' => 'ab_rate',    'commission' => 'ab_commission_rate'],
                'AC'    => ['rate' => 'ac_rate',    'commission' => 'ac_commission_rate'],
                'BC'    => ['rate' => 'bc_rate',    'commission' => 'bc_commission_rate'],
            ];

            foreach ($request->items as $item) {

                $game  = $item['game'];
                $count = $item['count'];

                if (!isset($rateMap[$game])) {
                    continue;
                }

                $rateField = $rateMap[$game]['rate'];
                $commissionField = $rateMap[$game]['commission'];

                $rate = $user->$rateField;
                $commission = $user->$commissionField;

                $totalCount += $count;

                $itemRateTotal = $rate * $count;
                $itemCommissionTotal = $commission * $count;

                $totalRate += $itemRateTotal;
                $totalCommission += $itemCommissionTotal;

                // 🔥 rate + commission together
                $totalAmount += ($rate + $commission) * $count;
            }

            

            // ✅ Create Bill
            $bill = Bill::create([
                'user_id' => $user->id,
                'game_id' => $request->game_id,
                'customer_name' => $request->customer_name,
                'total_count' => $totalCount,
                'total_rate' => $totalRate,
                'total_commission' => $totalCommission,
                'total_amount' => $totalAmount,
            ]);

            // ✅ Create Bill Items
        foreach ($request->items as $item) {

                $game = $item['game'];

                $rateField       = $rateMap[$game]['rate'];
                $commissionField = $rateMap[$game]['commission'];

                // price = user rate + user commission
                $price = $user->$rateField + $user->$commissionField;
            // dd($rateField, $commissionField);
                $bill->items()->create([
                    'type'   => $game,
                    'number' => $item['number'],
                    'count'  => $item['count'],
                    'price'  => $user->$rateField ,
                    'commission'  => $user->$commissionField,
                ]);
            }


            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Bill created successfully",
                "errorCode" => 0,
                "data" => [
                    "bill" => $bill->load('items'),
                    "pdf_urls" => [
                        "download" => url("/api/bill/{$bill->id}/pdf/download"),
                        "view" => url("/api/bill/{$bill->id}/pdf/view"),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bill Store Error: ' . $e->getMessage());

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to create bill",
                "errorCode" => 1,
               "data" => (object)[],
            ], 500);
        }
    }


    /* ============ EDIT PAGE  ============= */
    public function edit(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        $bill = Bill::with('items', 'game')->find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        return response()->json([
            "message" => "Success",
            "toast_message" => "Bill fetched successfully",
            "errorCode" => 0,
            "data" => [
                "bill" => array_merge($bill->toArray(), [
                    "game_time" => $bill->game?->time,
                ]),
            ]
        ], 200);
    }

    /* ============ UPDATE FUNCTION  ============= */
    public function update(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }
         if ($authUser->user_type === 'agent') {

            $user = User::where('id', $request->user_id)
                        ->where('parent_id', $authUser->id)
                        ->first();

            if (!$user) {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Passed user is not your sub-agent of this agent",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }

        } else {

            if ($authUser->user_type !== 'sub_agent') {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Unauthenticated",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }

            $user = $authUser;
        }


        $bill = Bill::where('user_id',$user->id)->where('id',$id)->first();

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        // ✅ Check if bill is from a previous date (only today's bills can be updated)
        $billDate = Carbon::parse($bill->created_at)->timezone('Asia/Kolkata')->toDateString();
        $today = Carbon::today('Asia/Kolkata')->toDateString();

        if ($billDate !== $today) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bills from previous dates cannot be updated, only viewed",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

       

         // Validation
        $validator = Validator::make($request->all(), [
           'game_id' => 'required|exists:games,id',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.game' => 'required|in:SUPER,BOX,A,B,C,AB,AC,BC',
            'items.*.number' => 'required|integer|min:1',
            'items.*.count' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $validator->errors()->first(),
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }

        // ✅ Game time check
        $gameTimeCheck = $this->isGameTimeValid($request->game_id);
        if (!$gameTimeCheck['valid']) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $gameTimeCheck['message'],
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        DB::beginTransaction();

        try {
            $user = User::findOrFail($bill->user_id);

            $totalRate = 0;
            $totalCommission = 0;
            $totalCount = 0;
            $totalAmount = 0;

            // 🔑 Rate mapping
            $rateMap = [
                'SUPER' => ['rate' => 'super_rate', 'commission' => 'super_commission_rate'],
                'BOX'   => ['rate' => 'box_rate',   'commission' => 'box_commission_rate'],
                'A'     => ['rate' => 'a_rate',     'commission' => 'a_commission_rate'],
                'B'     => ['rate' => 'b_rate',     'commission' => 'b_commission_rate'],
                'C'     => ['rate' => 'c_rate',     'commission' => 'c_commission_rate'],
                'AB'    => ['rate' => 'ab_rate',    'commission' => 'ab_commission_rate'],
                'AC'    => ['rate' => 'ac_rate',    'commission' => 'ac_commission_rate'],
                'BC'    => ['rate' => 'bc_rate',    'commission' => 'bc_commission_rate'],
            ];

            foreach ($request->items as $item) {

                $game  = $item['game'];
                $count = $item['count'];

                if (!isset($rateMap[$game])) {
                    continue;
                }

                $rateField = $rateMap[$game]['rate'];
                $commissionField = $rateMap[$game]['commission'];

                $rate = $user->$rateField;
                $commission = $user->$commissionField;

                $totalCount += $count;

                $itemRateTotal = $rate * $count;
                $itemCommissionTotal = $commission * $count;

                $totalRate += $itemRateTotal;
                $totalCommission += $itemCommissionTotal;

                // 🔥 rate + commission together
                $totalAmount += ($rate + $commission) * $count;
            }

            // ✅ Update Bill
            $bill->update([
                'game_id' => $request->game_id,
                'customer_name' => $request->customer_name,
                'total_count' => $totalCount,
                'total_rate' => $totalRate,
                'total_commission' => $totalCommission,
                'total_amount' => $totalAmount,
            ]);

            // ✅ Delete existing items and create new ones
            $bill->items()->delete();

            foreach ($request->items as $item) {

                $game = $item['game'];

                $rateField       = $rateMap[$game]['rate'];
                $commissionField = $rateMap[$game]['commission'];

                // price = user rate + user commission
                $price = $user->$rateField + $user->$commissionField;

                $bill->items()->create([
                    'type'   => $game,
                    'number' => $item['number'],
                    'count'  => $item['count'],
                    'price'  => $price,
                ]);
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Bill updated successfully",
                "errorCode" => 0,
                "data" => [
                    "bill" => $bill->load('items'),
                    "pdf_urls" => [
                        "download" => url("/api/bill/{$bill->id}/pdf/download"),
                        "view" => url("/api/bill/{$bill->id}/pdf/view")
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update bill",
                "errorCode" => 1,
               "data" => (object)[],
            ], 500);
        }
    }

    /* ============ DELETE FUNCTION  ============= */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        $bill = Bill::where('id', $id)
                ->where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->first();

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found or not created today",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        DB::beginTransaction();

        try {
            // Soft delete bill items
            $bill->items()->delete();

            // Soft delete bill
            $bill->delete();

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Bill deleted successfully",
                "errorCode" => 0,
               "data" => (object)[],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to delete bill",
                "errorCode" => 1,
               "data" => (object)[],
            ], 500);
        }
    }

    /* ============ COUNT LIMIT STATUS  ============= */
    public function countLimitStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'game_id' => 'required|exists:games,id',
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        if ($authUser->user_type === 'agent') {
            $user = User::where('id', $request->user_id)
                        ->where('parent_id', $authUser->id)
                        ->first();
            if (!$user) {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Passed user is not your sub-agent",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }
        } else {
            if ($authUser->user_type !== 'sub_agent') {
                return response()->json([
                    "message" => "Error",
                    "toast_message" => "Unauthenticated",
                    "errorCode" => 1,
                   "data" => (object)[],
                ], 200);
            }
            $user = $authUser;
        }

        $countLimitMap = [
            'super' => 'game_count_super',
            'box'   => 'game_count_box',
            'a'     => 'game_count_a',
            'b'     => 'game_count_b',
            'c'     => 'game_count_c',
            'ab'    => 'game_count_ab',
            'ac'    => 'game_count_ac',
            'bc'    => 'game_count_bc',
        ];

        // Get today's used counts per type for this game_id
        $usedCounts = DB::table('bill_items')
            ->join('bills', 'bills.id', '=', 'bill_items.bill_id')
            ->where('bills.user_id', $user->id)
            ->where('bills.game_id', $request->game_id)
            ->whereDate('bills.created_at', Carbon::today('Asia/Kolkata'))
            ->whereNull('bills.deleted_at')
            ->whereNull('bill_items.deleted_at')
            ->groupBy('bill_items.type')
            ->select('bill_items.type', DB::raw('SUM(bill_items.count) as total'))
            ->pluck('total', 'type')
            ->toArray();

        // Build game_count_limit
        $gameCountLimit = [];
        foreach ($countLimitMap as $key => $field) {
            $total_limit = (int) $user->$field;
            $used_limit  = (int) ($usedCounts[strtoupper($key)] ?? 0);
            $left_limit  = max(0, $total_limit - $used_limit);

            $gameCountLimit[strtoupper($key)] = [
                'total_limit' => $total_limit,
                'used_limit'  => $used_limit,
                'left_limit'  => $left_limit,
            ];
        }

        // Get today's used counts per type+number for this game_id
        $usedNumberCounts = DB::table('bill_items')
            ->join('bills', 'bills.id', '=', 'bill_items.bill_id')
            ->where('bills.user_id', $user->id)
            ->where('bills.game_id', $request->game_id)
            ->whereDate('bills.created_at', Carbon::today('Asia/Kolkata'))
            ->whereNull('bills.deleted_at')
            ->whereNull('bill_items.deleted_at')
            ->groupBy('bill_items.type', 'bill_items.number')
            ->select('bill_items.type', 'bill_items.number', DB::raw('SUM(bill_items.count) as total'))
            ->get()
            ->keyBy(fn($row) => $row->type . '_' . $row->number);

        // Build number_count_limit
        $numberCountLimit = NumberCountLimit::where('user_id', $user->id)
            ->where('game_id', $request->game_id)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($row) use ($usedNumberCounts) {
                $key        = strtoupper($row->type) . '_' . $row->number;
                $total_limit = (int) $row->count;
                $used_limit  = (int) ($usedNumberCounts[$key]->total ?? 0);
                $left_limit  = max(0, $total_limit - $used_limit);

                return [
                    'type'        => strtoupper($row->type),
                    'number'      => $row->number,
                    'total_limit' => $total_limit,
                    'used_limit'  => $used_limit,
                    'left_limit'  => $left_limit,
                ];
            })
            ->values();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Limit status fetched successfully",
            "errorCode" => 0,
            "data" => [
                "game_count_limit"   => $gameCountLimit,
                "number_count_limit" => $numberCountLimit,
            ]
        ], 200);
    }

    /* ============ GENERATE PDF  ============= */
    public function generatePDF(Request $request, $id)
    {
       

        $bill = Bill::with(['items', 'game', 'user'])->find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('bills.pdf', ['bill' => $bill]);

            // Set paper size - 80mm thermal receipt size (in points: 1mm = 2.83465 points)
            $pdf->setPaper([0, 0, 226.77, 680], 'portrait'); // 80mm width x 240mm height

            // Generate filename
            $filename = 'bill_' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) . '_' . date('YmdHis') . '.pdf';

            // Return PDF download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to generate PDF: " . $e->getMessage(),
                "errorCode" => 1,
               "data" => (object)[],
            ], 500);
        }
    }

    /* ============ VIEW PDF IN BROWSER  ============= */
    public function viewPDF(Request $request, $id)
    {
        

        $bill = Bill::with(['items', 'game', 'user'])->find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
               "data" => (object)[],
            ], 200);
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('bills.pdf', ['bill' => $bill]);

            // Set paper size - 80mm thermal receipt size (in points: 1mm = 2.83465 points)
            $pdf->setPaper([0, 0, 226.77, 680], 'portrait'); // 80mm width x 240mm height

            // Return PDF for inline viewing in browser
            return $pdf->stream('bill_' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF View Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to generate PDF: " . $e->getMessage(),
                "errorCode" => 1,
               "data" => (object)[],
            ], 500);
        }
    }
}
