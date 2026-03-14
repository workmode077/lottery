<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\ResultEntry;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   private function validateReportRequest(Request $request)
    {
        return $request->validate([
            'from_date'   => 'sometimes|date',
            'to_date'     => 'sometimes|date|after_or_equal:from_date',
            'game_id'     => 'sometimes|exists:games,id',
            'type_key'    => 'sometimes|integer|in:0,1,2,3',
            'type'        => 'sometimes|string|in:SUPER,BOX,A,B,C,AB,AC,BC',
            'number'      => 'sometimes|numeric',
            'sub_dealer'  => 'sometimes|exists:users,id',
            // 'dealer_rate' => 'sometimes|boolean'
        ]);
    }


    public function saleReport(Request $request)
    {
        $validated = $this->validateReportRequest($request);

        // 🔹 Type Key Mapping
        $typeKeyMap = [
            0 => ['A', 'B', 'C','AB', 'AC', 'BC','SUPER', 'BOX'],
            1 => ['A', 'B', 'C'],
            2 => ['AB', 'AC', 'BC'],
            3 => ['SUPER', 'BOX'],
        ];

        $query = Bill::query()
            ->with([
                'billItems' => function ($q) use ($request, $typeKeyMap) {

                    // 🔹 type_key filter
                    if ($request->filled('type_key') && isset($typeKeyMap[$request->type_key])) {
                        $q->whereIn('type', $typeKeyMap[$request->type_key]);
                    }

                    // 🔹 single type filter
                    if ($request->filled('type')) {
                        $q->where('type', $request->type);
                    }

                    // 🔹 number filter
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

        // 🔹 Type / Number / Type Key Filter
        if ($request->filled('type') || $request->filled('number') || $request->filled('type_key')) {

            $query->whereHas('billItems', function ($q) use ($request, $typeKeyMap) {

                if ($request->filled('type_key') && isset($typeKeyMap[$request->type_key])) {
                    $q->whereIn('type', $typeKeyMap[$request->type_key]);
                }

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
        $validated = $this->validateReportRequest($request);

        $typeKeyMap = [
            0 => ['A', 'B', 'C', 'AB', 'AC', 'BC', 'SUPER', 'BOX'],
            1 => ['A', 'B', 'C'],
            2 => ['AB', 'AC', 'BC'],
            3 => ['SUPER', 'BOX'],
        ];

        // Prize columns mapped to their position number and label
        $prizeColumnMap = [
            'prize_one'   => ['position' => 1, 'label' => 'First Prize'],
            'prize_two'   => ['position' => 2, 'label' => 'Second Prize'],
            'prize_three' => ['position' => 3, 'label' => 'Third Prize'],
            'prize_four'  => ['position' => 4, 'label' => 'Fourth Prize'],
            'prize_five'  => ['position' => 5, 'label' => 'Fifth Prize'],
        ];
        $specialColumns = [
            'one','two','three','four','five','six','seven','eight','nine','ten',
            'eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen',
            'eighteen','nineteen','twenty','twenty_one','twenty_two','twenty_three',
            'twenty_four','twenty_five','twenty_six','twenty_seven','twenty_eight',
            'twenty_nine','thirty',
        ];
        foreach ($specialColumns as $col) {
            $prizeColumnMap[$col] = ['position' => 6, 'label' => 'Special Prize'];
        }

        // Load prices (single row)
        $prices = DB::table('prices')->first();

        // Load result entries keyed by game_id + date
        $resultQuery = ResultEntry::query();
        if ($request->filled('game_id')) {
            $resultQuery->where('game_id', $request->game_id);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $resultQuery->whereBetween('date', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }
        $resultEntries = $resultQuery->get()->keyBy(function ($r) {
            return $r->game_id . '_' . date('Y-m-d', strtotime($r->date));
        });

        // Load bills with items (load all user commission fields)
        $userColumns = 'id,username,lsk_super_first_price_commission,lsk_super_second_price_commission,'
            . 'lsk_super_third_price_commission,lsk_super_fourth_price_commission,lsk_super_fifth_price_commission,'
            . 'lsk_super_sixth_price_commission,box_three_diff_first_price_commission,'
            . 'box_three_diff_second_price_commission,box_two_same_first_price_commission,'
            . 'box_two_same_second_price_commission,box_three_same_first_price_commission,'
            . 'abc_first_price_commission,abc_second_price_commission,'
            . 'ab_ac_bc_first_price_commission,ab_ac_bc_second_price_commission';

        $query = Bill::query()
            ->with(['billItems', 'user:' . $userColumns, 'game:id,time']);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }
        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }
        if ($request->filled('sub_dealer')) {
            $query->where('user_id', $request->sub_dealer);
        }
        if ($request->filled('type') || $request->filled('number') || $request->filled('type_key')) {
            $query->whereHas('billItems', function ($q) use ($request, $typeKeyMap) {
                if ($request->filled('type_key') && isset($typeKeyMap[$request->type_key])) {
                    $q->whereIn('type', $typeKeyMap[$request->type_key]);
                }
                if ($request->filled('type')) {
                    $q->where('type', $request->type);
                }
                if ($request->filled('number')) {
                    $q->where('number', $request->number);
                }
            });
        }

        $bills = $query->get();

        // Type display order: SUPER → BOX → AB → BC → AC → A → B → C
        $typeOrder     = ['SUPER', 'BOX', 'AB', 'BC', 'AC', 'A', 'B', 'C'];
        $prizeOrder    = ['First Prize', 'Second Prize', 'Third Prize', 'Fourth Prize', 'Fifth Prize', 'Special Prize'];

        // Pre-build nested structure: type → prize_label → []
        $grouped = [];
        foreach ($typeOrder as $t) {
            foreach ($prizeOrder as $p) {
                $grouped[$t][$p] = [];
            }
        }

        $totalCount      = 0;
        $grandTotal      = 0;
        $totalCommission = 0;

        foreach ($bills as $bill) {
            $billDate    = date('Y-m-d', strtotime($bill->created_at));
            $key         = $bill->game_id . '_' . $billDate;
            $resultEntry = $resultEntries->get($key);

            if (!$resultEntry) {
                continue;
            }

            $billInfo = [
                'bill_id'       => $bill->id,
                'username'      => $bill->user->username ?? null,
                'game_time'     => $bill->game->time ?? null,
                'customer_name' => $bill->customer_name,
            ];

            foreach ($bill->billItems as $item) {
                if (!in_array($item->type, $typeOrder)) {
                    continue;
                }

                // Check each prize column individually to get position + label
                foreach ($prizeColumnMap as $col => $prizeInfo) {
                    if (!isset($resultEntry->$col) || $resultEntry->$col === null) {
                        continue;
                    }
                    $resultNumber = (int) $resultEntry->$col;

                    if (!$this->checkMatch($item->type, (int) $item->number, $resultNumber)) {
                        continue;
                    }

                    $winAmount = $this->calculateWinAmount(
                        $item->type,
                        (int) $item->number,
                        $prizeInfo['position'],
                        (int) $item->count,
                        $prices
                    );

                    $commissionRate = $this->getUserCommissionRate(
                        $item->type,
                        (int) $item->number,
                        $prizeInfo['position'],
                        $bill->user
                    );

                    $priceCommission = (int) round($winAmount * $commissionRate / 100);

                    $totalCount      += (int) $item->count;
                    $grandTotal      += $winAmount;
                    $totalCommission += $priceCommission;

                    $grouped[$item->type][$prizeInfo['label']][] = array_merge($billInfo, [
                        'item_id'          => $item->id,
                        'number'           => $item->number,
                        'count'            => $item->count,
                        'result_number'    => $resultNumber,
                        'total'            => $winAmount,
                        'price_commission' => $priceCommission,
                    ]);
                }
            }
        }

        return response()->json([
            'message'       => 'Success',
            'toast_message' => 'Winning report generated successfully',
            'errorCode'     => 0,
            'data'          => [
                'summary' => [
                    'total_count'      => $totalCount,
                    'total'            => $grandTotal,
                    'total_commission' => $totalCommission,
                    'grand_total'      => $grandTotal - $totalCommission,
                ],
                'results' => $grouped,
            ],
        ]);
    }

    /**
     * Get the commission rate (%) for the sub-agent user based on type + prize position.
     * Returns a float representing the percentage (e.g. 2 = 2%).
     */
    private function getUserCommissionRate(string $type, int $number, int $position, $user): float
    {
        if (!$user) {
            return 0;
        }

        $positionKey = match ($position) {
            1 => 'first',
            2 => 'second',
            3 => 'third',
            4 => 'fourth',
            5 => 'fifth',
            6 => 'sixth',
            default => null,
        };

        $field = match (true) {
            $type === 'SUPER' && $positionKey !== null
                => 'lsk_super_' . $positionKey . '_price_commission',

            $type === 'BOX' => $this->getBoxCommissionField($number, $position),

            in_array($type, ['A', 'B', 'C']) && in_array($positionKey, ['first', 'second'])
                => 'abc_' . $positionKey . '_price_commission',

            in_array($type, ['AB', 'AC', 'BC']) && in_array($positionKey, ['first', 'second'])
                => 'ab_ac_bc_' . $positionKey . '_price_commission',

            default => null,
        };

        if (!$field || !isset($user->$field)) {
            return 0;
        }

        return (float) $user->$field;
    }

    private function getBoxCommissionField(int $number, int $position): ?string
    {
        $digits = str_split(str_pad((string) $number, 3, '0', STR_PAD_LEFT));
        $unique = count(array_unique($digits));

        if ($unique === 3) {
            return match ($position) {
                1 => 'box_three_diff_first_price_commission',
                2 => 'box_three_diff_second_price_commission',
                default => null,
            };
        }

        if ($unique === 2) {
            return match ($position) {
                1 => 'box_two_same_first_price_commission',
                2 => 'box_two_same_second_price_commission',
                default => null,
            };
        }

        return $position === 1 ? 'box_three_same_first_price_commission' : null;
    }

    /**
     * Check if a number matches a single result number for the given type.
     * A/B/C = digit at position 1/2/3 of the 3-digit result.
     * AB/AC/BC = two-digit combo from those positions.
     * SUPER = exact 3-digit match.
     * BOX = same three digits in any order.
     */
    private function checkMatch(string $type, int $number, int $r): bool
    {
        $rA  = intdiv($r, 100);        // hundreds digit
        $rB  = intdiv($r % 100, 10);  // tens digit
        $rC  = $r % 10;               // units digit

        return match ($type) {
            'A'     => $number === $rA,
            'B'     => $number === $rB,
            'C'     => $number === $rC,
            'AB'    => $number === intdiv($r, 10),
            'AC'    => $number === ($rA * 10 + $rC),
            'BC'    => $number === ($r % 100),
            'SUPER' => $number === $r,
            'BOX'   => $this->isBoxMatch($number, $r),
            default => false,
        };
    }

    /**
     * Calculate the win amount based on type, prize position, and the prices table.
     * BOX win amount depends on how many unique digits the number has.
     */
    private function calculateWinAmount(string $type, int $number, int $position, int $count, $prices): int
    {
        if (!$prices) {
            return 0;
        }

        $pricePerUnit = match (true) {
            $type === 'SUPER' => match ($position) {
                1 => $prices->lsk_super_first_price,
                2 => $prices->lsk_super_second_price,
                3 => $prices->lsk_super_third_price,
                4 => $prices->lsk_super_fourth_price,
                5 => $prices->lsk_super_fifth_price,
                6 => $prices->lsk_super_30_enabled ? $prices->lsk_super_sixth_price : 0,
                default => 0,
            },

            $type === 'BOX' => $this->getBoxPrice($number, $position, $prices),

            in_array($type, ['A', 'B', 'C']) => match ($position) {
                1 => $prices->abc_first_price,
                2 => $prices->abc_second_price,
                default => 0,
            },

            in_array($type, ['AB', 'AC', 'BC']) => match ($position) {
                1 => $prices->ab_ac_bc_first_price,
                2 => $prices->ab_ac_bc_second_price,
                default => 0,
            },

            default => 0,
        };

        return (int) $pricePerUnit * $count;
    }

    /**
     * BOX prize depends on the digit pattern of the selected number:
     * - 3 different digits (e.g. 123): box_three_diff_*
     * - 2 same digits   (e.g. 112): box_two_same_*
     * - 3 same digits   (e.g. 111): box_three_same_first only
     */
    private function getBoxPrice(int $number, int $position, $prices): int
    {
        $digits  = str_split(str_pad((string) $number, 3, '0', STR_PAD_LEFT));
        $unique  = count(array_unique($digits));

        if ($unique === 3) {
            return match ($position) {
                1 => $prices->box_three_diff_first_price,
                2 => $prices->box_three_diff_second_price,
                default => 0,
            };
        }

        if ($unique === 2) {
            return match ($position) {
                1 => $prices->box_two_same_first_price,
                2 => $prices->box_two_same_second_price,
                default => 0,
            };
        }

        // 3 same digits — only first prize exists
        return $position === 1 ? $prices->box_three_same_first_price : 0;
    }

    private function isBoxMatch(int $number, int $result): bool
    {
        $nDigits = str_split(str_pad((string) $number, 3, '0', STR_PAD_LEFT));
        $rDigits = str_split(str_pad((string) $result, 3, '0', STR_PAD_LEFT));
        sort($nDigits);
        sort($rDigits);
        return $nDigits === $rDigits;
    }


    

   public function countReport(Request $request)
    {
        $validated = $this->validateReportRequest($request);

         $typeKeyMap = [
            0 => ['A', 'B', 'C','AB', 'AC', 'BC','SUPER', 'BOX'],
            1 => ['A', 'B', 'C'],
            2 => ['AB', 'AC', 'BC'],
            3 => ['SUPER', 'BOX'],
        ];


        $query = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->whereNull('bills.deleted_at')
            ->whereNull('bill_items.deleted_at');

        // 🔹 Date Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('bills.created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // 🔹 Game Filter
        if ($request->filled('game_id')) {
            $query->where('bills.game_id', $request->game_id);
        }

        // 🔹 Sub Dealer Filter
        if ($request->filled('sub_dealer')) {
            $query->where('bills.user_id', $request->sub_dealer);
        }

       if ($request->filled('type_key') ||  $request->filled('type') || $request->filled('number') ) {
            $query->whereExists(function ($q) use ($request, $typeKeyMap) {
                $q->select(DB::raw(1))
                    ->from('bill_items')
                    ->whereColumn('bill_items.bill_id', 'bills.id')
                    ->whereNull('bill_items.deleted_at');

                if ($request->filled('type_key') && isset($typeKeyMap[$request->type_key])) {
                    $q->whereIn('bill_items.type', $typeKeyMap[$request->type_key]);
                }

                if ($request->filled('type')) {
                    $q->where('bill_items.type', $request->type);
                }

                if ($request->filled('number')) {
                    $q->where('bill_items.number', $request->number);
                }
            });
        }

        // ✅ Group By Type
        $report = $query->select(
                'bill_items.type',
                DB::raw('SUM(bill_items.count) as total_count'),
                DB::raw('SUM(bill_items.price * bill_items.count) as total_price'),
                DB::raw('SUM(bills.total_commission) as total_commission')
            )
            ->groupBy('bill_items.type')
            ->get();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Count report generated successfully",
            "errorCode" => 0,
            "data" => $report
        ]);
    }

    public function dailyReport(Request $request)
    {
        $validated = $this->validateReportRequest($request);

        $typeKeyMap = [
            0 => ['A', 'B', 'C','AB', 'AC', 'BC','SUPER', 'BOX'],
            1 => ['A', 'B', 'C'],
            2 => ['AB', 'AC', 'BC'],
            3 => ['SUPER', 'BOX'],
        ];

        $query = DB::table('bills')
            ->join('users', 'bills.user_id', '=', 'users.id')
            ->whereNull('bills.deleted_at');

        // Date Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('bills.created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // Game Filter
        if ($request->filled('game_id')) {
            $query->where('bills.game_id', $request->game_id);
        }

        // Sub Dealer Filter
        if ($request->filled('sub_dealer')) {
            $query->where('bills.user_id', $request->sub_dealer);
        }

        // Type / Number / Type Key Filter
        if ($request->filled('type') || $request->filled('number') || $request->filled('type_key')) {
            $query->whereExists(function ($q) use ($request, $typeKeyMap) {
                $q->select(DB::raw(1))
                    ->from('bill_items')
                    ->whereColumn('bill_items.bill_id', 'bills.id')
                    ->whereNull('bill_items.deleted_at');

                if ($request->filled('type_key') && isset($typeKeyMap[$request->type_key])) {
                    $q->whereIn('bill_items.type', $typeKeyMap[$request->type_key]);
                }

                if ($request->filled('type')) {
                    $q->where('bill_items.type', $request->type);
                }

                if ($request->filled('number')) {
                    $q->where('bill_items.number', $request->number);
                }
            });
        }

        $report = $query->select(
                'users.username',
                DB::raw('SUM(bills.total_count) as total_count'),
                DB::raw('SUM(bills.total_rate) as total_sale'),
                DB::raw('SUM(bills.total_commission) as sale_commission'),
                DB::raw('SUM(bills.total_rate + bills.total_commission) as total')
            )
            ->groupBy('bills.user_id', 'users.username')
            ->get();

        // Games in summary
        if ($request->filled('game_id')) {
            $games = DB::table('games')
                ->where('id', $request->game_id)
                ->select('id', 'time')
                ->first();
        } else {
            $games = 'All Game';
        }

        $summary = [
            'from_date'        => $request->filled('from_date') ? $request->from_date : null,
            'to_date'          => $request->filled('to_date') ? $request->to_date : null,
            'games'            => $games,
            'total_count'      => $report->sum('total_count'),
            'total_sale'       => $report->sum('total_sale'),
            'sale_commission'  => $report->sum('sale_commission'),
            'total'            => $report->sum('total'),
        ];

        return response()->json([
            "message"       => "Success",
            "toast_message" => "Daily report generated successfully",
            "errorCode"     => 0,
            "data"          => [
                "summary" => $summary,
                "users"   => $report
            ]
        ]);
    }
}
