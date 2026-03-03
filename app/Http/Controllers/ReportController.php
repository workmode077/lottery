<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   private function validateReportRequest(Request $request)
    {
        return $request->validate([
            'from_date'   => 'sometimes|date',
            'to_date'     => 'sometimes|date|after_or_equal:from_date',
            'game_id'     => 'sometimes|exists:games,id',
            'type'        => 'sometimes|string|in:SUPER,BOX,A,B,C,AB,AC,BC',
            'number'      => 'sometimes|numeric',
            'sub_dealer'  => 'sometimes|exists:users,id',
            'dealer_rate' => 'sometimes|boolean'
        ]);
    }


    public function saleReport(Request $request)
    {
        $validated = $this->validateReportRequest($request);

       $query = Bill::query()
                    ->with([
                        'billItems' => function ($q) use ($request) {
                            if ($request->filled('type')) {
                                $q->where('type', $request->type);
                            }

                            if ($request->filled('number')) {
                                $q->where('number', $request->number);
                            }
                        },
                        'user:id,username',
                        'game:id,time'
                    ]);

        // 🔹 Date Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // 🔹 Game Filter
        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        // 🔹 Sub Dealer Filter
        if ($request->filled('sub_dealer')) {
            $query->where('user_id', $request->sub_dealer);
        }

        // 🔹 Type & Number Filter
        if ($request->filled('type') || $request->filled('number')) {
            $query->whereHas('billItems', function ($q) use ($request) {

                if ($request->filled('type')) {
                    $q->where('type', $request->type);
                }

                if ($request->filled('number')) {
                    $q->where('number', $request->number);
                }
            });
        }

        $summaryQuery = clone $query;

        $bills = $query->get();

        // ✅ Summary
        $summary = [
            'total_bills'        => $summaryQuery->count(),
            'total_count'        => $summaryQuery->sum('total_count'),
            'total'              => $summaryQuery->sum('total_rate'),
            'dealer_commission'  => $summaryQuery->sum('total_commission'),
            'grand_total'        => $summaryQuery->sum(DB::raw('total_rate + total_commission')),
        ];

        // ✅ Transform Bills Response
        $formattedBills = $bills->map(function ($bill) {
            return [
                "id"                => $bill->id,
                "username"          => $bill->user->username ?? null,
                "game_time"         => $bill->game->time ?? null,
                "total_count"       => $bill->total_count,
                "total"             => $bill->total_rate,
                "dealer_commission" => $bill->total_commission,
                "grand_total"       => $bill->total_rate + $bill->total_commission,
                "bill_items"        => $bill->billItems
            ];
        });

        return response()->json([
            "message" => "Success",
            "toast_message" => "Sale report generated successfully",
            "errorCode" => 0,
            "data" => [
                "summary" => $summary,
                "bills"   => $formattedBills
            ]
        ]);
    }

    public function winningReport(Request $request)
    {
        // Logic for generating winning report
    }

    public function countReport(Request $request)
    {
        // Logic for generating count report
    }

    public function dailyReport(Request $request)
    {
        // Logic for generating daily report
    }
}
