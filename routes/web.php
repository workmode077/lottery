<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'show'])->name('admin.show');

