<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResultEntry;
use Carbon\Carbon;

class ResultController extends Controller
{
     public function index(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'date'    => 'required|date'
        ]);

        $result = ResultEntry::with(['game:id,time'])->where('game_id', $request->game_id)
                    ->whereDate('date', Carbon::parse($request->date))
                    ->where('status', true)
                    ->orderBy('date', 'desc')
                    ->first();

        if (!$result) {
            return response()->json([
                "message" => "Success",
                "toast_message" => "No result found for selected date",
                "errorCode" => 1,
                "data" => null
            ], 200);
        }

        return response()->json([
            "message" => "Success",
            "toast_message" => "Result fetched successfully",
            "errorCode" => 0,
            "data" => [
                "id" => $result->id,
                "date" => $result->date,
                "game" => $result->game,

                "prizes" => [
                    [
                        "label" => "First Prize",
                        "number" => $result->prize_one,
                        "color" => "#FF0000" // Red
                    ],
                    [
                        "label" => "Second Prize",
                        "number" => $result->prize_two,
                        "color" => "#0000FF" // Blue
                    ],
                    [
                        "label" => "Third Prize",
                        "number" => $result->prize_three,
                        "color" => "#008000" // Green
                    ],
                    [
                        "label" => "Fourth Prize",
                        "number" => $result->prize_four,
                        "color" => "#FFA500" // Orange
                    ],
                    [
                        "label" => "Fifth Prize",
                        "number" => $result->prize_five,
                        "color" => "#800080" // Purple
                    ]
                ],

                "numbers" => [
                    $result->one, $result->two, $result->three, $result->four, $result->five,
                    $result->six, $result->seven, $result->eight, $result->nine, $result->ten,
                    $result->eleven, $result->twelve, $result->thirteen, $result->fourteen, $result->fifteen,
                    $result->sixteen, $result->seventeen, $result->eighteen, $result->nineteen, $result->twenty,
                    $result->twenty_one, $result->twenty_two, $result->twenty_three, $result->twenty_four, $result->twenty_five,
                    $result->twenty_six, $result->twenty_seven, $result->twenty_eight, $result->twenty_nine, $result->thirty,
                ],

                "link" => $result->link
            ]
        ], 200);
    }
}
