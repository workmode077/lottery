<?php

namespace App\Http\Controllers\PrivateAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ResultEntry;
use Carbon\Carbon;

class ResultEntryController extends Controller
{
    public function formSubmit(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || $authUser->user_type !== 'private_agent') {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only private_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'game_id'      => 'required|integer|exists:games,id',
            'date'         => 'required|date',
            'prize_one'    => 'required|integer|min:0|max:999',
            'prize_two'    => 'required|integer|min:0|max:999',
            'prize_three'  => 'required|integer|min:0|max:999',
            'prize_four'   => 'required|integer|min:0|max:999',
            'prize_five'   => 'required|integer|min:0|max:999',
            'one'          => 'required|integer|min:0|max:999',
            'two'          => 'required|integer|min:0|max:999',
            'three'        => 'required|integer|min:0|max:999',
            'four'         => 'required|integer|min:0|max:999',
            'five'         => 'required|integer|min:0|max:999',
            'six'          => 'required|integer|min:0|max:999',
            'seven'        => 'required|integer|min:0|max:999',
            'eight'        => 'required|integer|min:0|max:999',
            'nine'         => 'required|integer|min:0|max:999',
            'ten'          => 'required|integer|min:0|max:999',
            'eleven'       => 'required|integer|min:0|max:999',
            'twelve'       => 'required|integer|min:0|max:999',
            'thirteen'     => 'required|integer|min:0|max:999',
            'fourteen'     => 'required|integer|min:0|max:999',
            'fifteen'      => 'required|integer|min:0|max:999',
            'sixteen'      => 'required|integer|min:0|max:999',
            'seventeen'    => 'required|integer|min:0|max:999',
            'eighteen'     => 'required|integer|min:0|max:999',
            'nineteen'     => 'required|integer|min:0|max:999',
            'twenty'       => 'required|integer|min:0|max:999',
            'twenty_one'   => 'required|integer|min:0|max:999',
            'twenty_two'   => 'required|integer|min:0|max:999',
            'twenty_three' => 'required|integer|min:0|max:999',
            'twenty_four'  => 'required|integer|min:0|max:999',
            'twenty_five'  => 'required|integer|min:0|max:999',
            'twenty_six'   => 'required|integer|min:0|max:999',
            'twenty_seven' => 'required|integer|min:0|max:999',
            'twenty_eight' => 'required|integer|min:0|max:999',
            'twenty_nine'  => 'required|integer|min:0|max:999',
            'thirty'       => 'required|integer|min:0|max:999',
            'link'         => 'nullable|string',
            'status'       => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $validator->errors()->first(),
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }

        // Block updates to past dates (yesterday and before)
        $entryDate = Carbon::parse($request->date)->startOfDay();
        $today = Carbon::today();

        if ($entryDate->lt($today)) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Past result entries cannot be updated. Only today and future dates are allowed.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }

        DB::beginTransaction();

        try {
            $fields = [
                'prize_one'    => $request->prize_one,
                'prize_two'    => $request->prize_two,
                'prize_three'  => $request->prize_three,
                'prize_four'   => $request->prize_four,
                'prize_five'   => $request->prize_five,
                'one'          => $request->one,
                'two'          => $request->two,
                'three'        => $request->three,
                'four'         => $request->four,
                'five'         => $request->five,
                'six'          => $request->six,
                'seven'        => $request->seven,
                'eight'        => $request->eight,
                'nine'         => $request->nine,
                'ten'          => $request->ten,
                'eleven'       => $request->eleven,
                'twelve'       => $request->twelve,
                'thirteen'     => $request->thirteen,
                'fourteen'     => $request->fourteen,
                'fifteen'      => $request->fifteen,
                'sixteen'      => $request->sixteen,
                'seventeen'    => $request->seventeen,
                'eighteen'     => $request->eighteen,
                'nineteen'     => $request->nineteen,
                'twenty'       => $request->twenty,
                'twenty_one'   => $request->twenty_one,
                'twenty_two'   => $request->twenty_two,
                'twenty_three' => $request->twenty_three,
                'twenty_four'  => $request->twenty_four,
                'twenty_five'  => $request->twenty_five,
                'twenty_six'   => $request->twenty_six,
                'twenty_seven' => $request->twenty_seven,
                'twenty_eight' => $request->twenty_eight,
                'twenty_nine'  => $request->twenty_nine,
                'thirty'       => $request->thirty,
                'link'         => $request->link,
                'status'       => $request->input('status', true),
            ];

            // Update if exists for this game_id + date, otherwise create
            $entry = ResultEntry::whereDate('date', $entryDate)
                ->where('game_id', $request->game_id)
                ->first();

            if ($entry) {
                $entry->update($fields);
            } else {
                $entry = ResultEntry::create(array_merge($fields, [
                    'game_id' => $request->game_id,
                    'date'    => $request->date,
                ]));
            }

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Result entry saved successfully",
                "errorCode" => 0,
                "data" => $entry->fresh()
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to save result entry",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }
}
