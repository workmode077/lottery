<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\SubAgent\SubAgentDashboardController;





// Authentication Routes
Route::post('/user-login', [AuthController::class, 'login']);

// Agent
Route::middleware('auth:api')->get('/agent-dashboard', [AgentDashboardController::class, 'index']);



// Sub-Agent
Route::middleware('auth:api')->get('/sub-agent-dashboard', [SubAgentDashboardController::class, 'index']);


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
