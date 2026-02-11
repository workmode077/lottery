<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function index(Request $request)
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

        $games = Game::where('status', true)->get();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Data fetched successfully",
            "errorCode" => 0,
            "data" => $games
        ], 200);
    }

    public function show(Request $request, $id)
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

        $game = Game::find($id);

        if (!$game) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Game not found",
                "errorCode" => 1,
                "data" => null
            ], 404);
        }

        return response()->json([
            "message" => "Success",
            "toast_message" => "Data fetched successfully",
            "errorCode" => 0,
            "data" => $game
        ], 200);
    }
}
