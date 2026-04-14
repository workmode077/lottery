<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Game;
use App\Models\UserGame;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    $games = Game::where('status', true)->get();

    // 1️⃣ Create ONLY 1 Private Agent (FIRST)
    $privateUser = User::create([
        'username' => 'PA-user1',
        'user_type' => 'private_agent',
        'password' => Hash::make('password123'),
        'plain_password' => 'password123',
        'status' => true,
        'parent_id' => null, // top-level
    ]);

    // Assign games
    foreach ($games as $game) {
        UserGame::create([
            'user_id' => $privateUser->id,
            'game_id' => $game->id,
            'status' => true,
        ]);
    }

    $superAgents = [];

    // 2️⃣ Create 2 SuperAgents (under Private Agent)
    for ($i = 1; $i <= 2; $i++) {
        $superAgents[] = User::create([
            'username' => 'SA-user' . $i,
            'user_type' => 'super_agent',
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'status' => true,
            'parent_id' => $privateUser->id,
        ]);
    }

    $agents = [];

    // 3️⃣ Create 2 Agents
    for ($i = 1; $i <= 2; $i++) {
        $parent = $superAgents[($i - 1) % count($superAgents)];

        $agents[] = User::create([
            'username' => 'AG-user' . $i,
            'user_type' => 'agent',
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'status' => true,
            'parent_id' => $parent->id,
        ]);
    }

    // 4️⃣ Create 2 SubAgents
    for ($i = 1; $i <= 2; $i++) {
        $parent = $agents[($i - 1) % count($agents)];

        $subAgent = User::create([
            'username' => 'SUB-user' . $i,
            'user_type' => 'sub_agent',
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'status' => true,
            'parent_id' => $parent->id,

            // Limits
            'daily_credit_limit'   => 5000,
            'weekly_credit_limit'  => 35000,
            'monthly_credit_limit' => 150000,
            'yearly_credit_limit'  => 1800000,

            // Rates
            'super_rate' => 8,
            'super_commission_rate' => 2,
            'a_rate' => 8,
            'a_commission_rate' => 2,
            'b_rate' => 8,
            'b_commission_rate' => 2,
            'c_rate' => 8,
            'c_commission_rate' => 2,
            'ab_rate' => 8,
            'ab_commission_rate' => 2,
            'bc_rate' => 8,
            'bc_commission_rate' => 2,
            'ac_rate' => 8,
            'ac_commission_rate' => 2,
            'box_rate' => 8,
            'box_commission_rate' => 2,
        ]);

        // Assign games
        foreach ($games as $game) {
            UserGame::create([
                'user_id' => $subAgent->id,
                'game_id' => $game->id,
                'status' => true,
            ]);
        }
    }
}
}
