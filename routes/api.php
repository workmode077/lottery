<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubAgent\SubAgentDashboardController;




// Authentication Routes
Route::post('/user-login', [AuthController::class, 'login']);


//Sub-Agent
Route::middleware('auth:api')->get('/sub-agent-dashboard', [SubAgentDashboardController::class, 'index']);




// Fallback route for undefined endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'status' => 404,
        'message' => 'Endpoint not found',
        'errors' => [
            'route' => 'The requested URL does not exist',
        ],
        'meta' => [
            'version' => '1.0',
            'timestamp' => now()->toDateTimeString(),
        ],
    ], 404);
});
