<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\{
    AdminController,
    AdminSettingsController,
    AuthController,
    RoleController,
    YearController,
    DashboardController,
    GameController,
    SuperAgentController,
    AgentController,
    SubAgentController,
    ResultEntryController,
    UserGameController,
    PrizeEntryController
};


Route::prefix(app('backend.prefix'))->group(function () {
    Route::get('login', [AuthController::class, 'show'])->name('admin.show');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login');

    Route::middleware('admin.auth')->group(function () {
        Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/', fn() => redirect()->route('dashboard.index'));
        Route::resource('dashboard', DashboardController::class)->only('index')->names('dashboard');

        // Admins
        Route::middleware('permission:admins,admin')->group(function () {
            Route::get('admins/{admin}/change-password', [AdminController::class, 'editPassword'])->name('admin.change-password.edit');
            Route::put('admins/{admin}/change-password', [AdminController::class, 'updatePassword'])->name('admin.change-password.update');
            Route::resource('admins', AdminController::class)->names('admin')->except('show');
        });

        // Roles
        Route::resource('roles', RoleController::class)->names('admin.roles')->except('show')->middleware('permission:roles,admin');

        // Admin Settings
        Route::post('admin-settings/update-sort-order', [AdminSettingsController::class, 'updateSortOrder'])->name('admin-settings.updateSortOrder');
        Route::post('admin-settings/update-toggle-status', [AdminSettingsController::class, 'updateToggleStatus'])->name('admin-settings.updateToggleStatus');
        Route::get('admin-settings/check-slug', [AdminSettingsController::class, 'checkSlug'])->name('admin-settings.checkSlug');
        Route::resource('admin-settings', AdminSettingsController::class)->only(['index', 'edit', 'update'])->names('admin-settings')->middleware('permission:admin-settings,admin');

        

       
      

       

        

      

      
        Route::resource('year', YearController::class)->except(['show'])->names('year');
        Route::resource('game', GameController::class)->except(['show'])->names('game');
        Route::resource('super-agent', SuperAgentController::class)->except(['show'])->names('super-agent');
        Route::resource('agent', AgentController::class)->except(['show'])->names('agent');
        Route::resource('sub-agent', SubAgentController::class)->except(['show'])->names('sub-agent');
        // Route::resource('sub-agent.user-games', UserGameController::class)->except(['show'])->names('sub-agent.user-games');

        Route::resource('result-entry', ResultEntryController::class)->except(['show'])->names('result-entry');
        Route::resource('prize-entry', PrizeEntryController::class)->only(['index'])->names('prize-entry');
        Route::put('prize-entry/{prizeEntry}', [PrizeEntryController::class, 'update'])->name('prize-entry.update');
       
    });
});
