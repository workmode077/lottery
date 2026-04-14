<?php

namespace App\Http\Controllers\PrivateAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Price;

class PrizeEntryController extends Controller
{
    public function index(Request $request)
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

        $prize = Price::first();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Prize entry fetched successfully",
            "errorCode" => 0,
            "data" => $prize
        ], 200);
    }

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
            'lsk_super_first_price'       => 'required|integer|min:0',
            'lsk_super_second_price'      => 'required|integer|min:0',
            'lsk_super_third_price'       => 'required|integer|min:0',
            'lsk_super_fourth_price'      => 'required|integer|min:0',
            'lsk_super_fifth_price'       => 'required|integer|min:0',
            'lsk_super_sixth_price'       => 'required|integer|min:0',
            'lsk_super_30_enabled'        => 'required|boolean',
            'lsk_super_seventh_price'     => 'required|integer|min:0',
            'lsk_super_50_enabled'        => 'required|boolean',
            'box_three_diff_first_price'  => 'required|integer|min:0',
            'box_three_diff_second_price' => 'required|integer|min:0',
            'box_two_same_first_price'    => 'required|integer|min:0',
            'box_two_same_second_price'   => 'required|integer|min:0',
            'box_three_same_first_price'  => 'required|integer|min:0',
            'abc_first_price'             => 'required|integer|min:0',
            'abc_second_price'            => 'required|integer|min:0',
            'ab_ac_bc_first_price'        => 'required|integer|min:0',
            'ab_ac_bc_second_price'       => 'required|integer|min:0',
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
            $prize = Price::first();

            $prize->update([
                'lsk_super_first_price'       => $request->lsk_super_first_price,
                'lsk_super_second_price'      => $request->lsk_super_second_price,
                'lsk_super_third_price'       => $request->lsk_super_third_price,
                'lsk_super_fourth_price'      => $request->lsk_super_fourth_price,
                'lsk_super_fifth_price'       => $request->lsk_super_fifth_price,
                'lsk_super_sixth_price'       => $request->lsk_super_sixth_price,
                'lsk_super_30_enabled'        => $request->lsk_super_30_enabled,
                'lsk_super_seventh_price'     => $request->lsk_super_seventh_price,
                'lsk_super_50_enabled'        => $request->lsk_super_50_enabled,
                'box_three_diff_first_price'  => $request->box_three_diff_first_price,
                'box_three_diff_second_price' => $request->box_three_diff_second_price,
                'box_two_same_first_price'    => $request->box_two_same_first_price,
                'box_two_same_second_price'   => $request->box_two_same_second_price,
                'box_three_same_first_price'  => $request->box_three_same_first_price,
                'abc_first_price'             => $request->abc_first_price,
                'abc_second_price'            => $request->abc_second_price,
                'ab_ac_bc_first_price'        => $request->ab_ac_bc_first_price,
                'ab_ac_bc_second_price'       => $request->ab_ac_bc_second_price,
            ]);

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Prize entry updated successfully",
                "errorCode" => 0,
                "data" => $prize->fresh()
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update prize entry",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }
}
