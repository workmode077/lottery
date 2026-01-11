<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bill;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $user = $request->user();
        if (!$user) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthenticated",
                "errorCode" => 1,
                "data" => null
            ], 401);
        }

        $bills = $user->bills()
                    ->with('items')
                    ->latest()
                    ->get();

        // Add PDF URLs to each bill
        $bills->transform(function ($bill) {
            $bill->pdf_urls = [
                "download" => url("/api/bill/{$bill->id}/pdf/download"),
                "view" => url("/api/bill/{$bill->id}/pdf/view")
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
                "data" => null
            ], 401);
        }

        // ✅ Validation
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'game_id' => 'required|exists:games,id',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.game' => 'required|in:SUPER,BOX,A,B,C,AB,AC,BC',
            'items.*.number' => 'required|integer|min:1',
            'items.*.count' => 'required|integer|min:1',
        ]);

        // ✅ Game time check
        $gameTimeCheck = $this->isGameTimeValid($request->game_id);
        if (!$gameTimeCheck['valid']) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $gameTimeCheck['message'],
                "errorCode" => 1,
                "data" => null
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);

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
                'user_id' => $request->user_id,
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
                "toast_message" => "Bill created successfully",
                "errorCode" => 0,
                "data" => [
                    "bill" => $bill->load('items'),
                    "pdf_urls" => [
                        "download" => url("/api/bill/{$bill->id}/pdf/download"),
                        "view" => url("/api/bill/{$bill->id}/pdf/view"),
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bill Store Error: ' . $e->getMessage());

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to create bill",
                "errorCode" => 1,
                "data" => null
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
                "data" => null
            ], 401);
        }

        $bill = Bill::with('items')->find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        return response()->json([
            "message" => "Success",
            "toast_message" => "Bill fetched successfully",
            "errorCode" => 0,
            "data" => [
                "bill" => $bill,
                "pdf_urls" => [
                    "download" => url("/api/bill/{$bill->id}/pdf/download"),
                    "view" => url("/api/bill/{$bill->id}/pdf/view")
                ]
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
                "data" => null
            ], 401);
        }

        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        // ✅ Check if bill is from a previous date (only today's bills can be updated)
        $billDate = Carbon::parse($bill->created_at)->timezone('Asia/Kolkata')->toDateString();
        $today = Carbon::today('Asia/Kolkata')->toDateString();

        if ($billDate !== $today) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bills from previous dates cannot be updated, only viewed",
                "errorCode" => 1,
                "data" => null
            ], 422);
        }

        // ✅ Validation
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.game' => 'required|in:SUPER,BOX,A,B,C,AB,AC,BC',
            'items.*.number' => 'required|integer|min:1',
            'items.*.count' => 'required|integer|min:1',
        ]);

        // ✅ Game time check
        $gameTimeCheck = $this->isGameTimeValid($request->game_id);
        if (!$gameTimeCheck['valid']) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $gameTimeCheck['message'],
                "errorCode" => 1,
                "data" => null
            ], 422);
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
                "data" => null
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
                "data" => null
            ], 401);
        }

        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
                "data" => null
            ], 404);
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
                "data" => null
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to delete bill",
                "errorCode" => 1,
                "data" => null
            ], 500);
        }
    }

    /* ============ GENERATE PDF  ============= */
    public function generatePDF(Request $request, $id)
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

        $bill = Bill::with(['items', 'game', 'user'])->find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
                "data" => null
            ], 404);
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
                "data" => null
            ], 500);
        }
    }

    /* ============ VIEW PDF IN BROWSER  ============= */
    public function viewPDF(Request $request, $id)
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

        $bill = Bill::with(['items', 'game', 'user'])->find($id);

        if (!$bill) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Bill not found",
                "errorCode" => 1,
                "data" => null
            ], 404);
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
                "data" => null
            ], 500);
        }
    }
}
