<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;




// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);





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
