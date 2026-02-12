<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\SubAgentListController;
use App\Http\Controllers\Agent\SubAgentController;
use App\Http\Controllers\SubAgent\SubAgentDashboardController;
use App\Http\Controllers\SubAgent\SubAgentLimitCheckController;





// Authentication Routes
Route::post('/user-login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/game', [GameController::class, 'index']);
    Route::get('/game/{id}', [GameController::class, 'show']);
});


// Agent
Route::middleware('auth:api')->get('/agent-dashboard', [AgentDashboardController::class, 'index']);
Route::middleware('auth:api')->get('/sub-agent-list', [SubAgentController::class, 'index']);
Route::middleware('auth:api')->get('/view-sub-agent/{id}', [SubAgentController::class, 'viewSingleSubAgent']);
Route::middleware('auth:api')->post('/sub-agent-create', [SubAgentController::class, 'subAgentCreate']);
Route::middleware('auth:api')->post('/sub-agent-edit', [SubAgentController::class, 'subAgentEdit']);
Route::middleware('auth:api')->post('/sub-agent-sale-commission', [SubAgentController::class, 'subAgentSaleCommission']);
Route::middleware('auth:api')->post('/sub-agent-price-commission', [SubAgentController::class, 'subAgentPriceCommission']);
Route::middleware('auth:api')->post('/sub-agent-game-count-limit', [SubAgentController::class, 'subAgentGameCountLimit']);
Route::middleware('auth:api')->post('/sub-agent-number-count-limit', [SubAgentController::class, 'subAgentNumberCountLimit']);



// Sub-Agent
Route::middleware('auth:api')->get('/sub-agent-dashboard', [SubAgentDashboardController::class, 'index']);
Route::middleware('auth:api')->get('/sub-agent-limit-check', [SubAgentLimitCheckController::class, 'index']);





// Bill - PDF routes must come BEFORE resource routes
Route::middleware('auth:api')->get('bill/{id}/pdf/download', [BillController::class, 'generatePDF'])->name('bill.pdf.download');
Route::middleware('auth:api')->get('bill/{id}/pdf/view', [BillController::class, 'viewPDF'])->name('bill.pdf.view');
Route::middleware('auth:api')->resource('bill', BillController::class)->except(['show'])->names('bill');

// Route::middleware('auth:api')->get('/', [SubAgentDashboardController::class, 'index']);

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
