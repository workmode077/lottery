<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;


class DashboardController extends Controller
{
   public function index()
    {
        if (!auth('admin')->user()->can('view-dashboard-data')) {
            return view('admin::dashboard.no-permission');
        }

        $superAdminCount = User::where('user_type', 'super_admin')->count();
        $agentCount = User::where('user_type', 'agent')->count();
        $subAgentCount = User::where('user_type', 'sub_agent')->count();
        $gameCount = Game::count();

        return view('admin::dashboard.index', compact('superAdminCount', 'agentCount', 'subAgentCount', 'gameCount'));
    }

}
