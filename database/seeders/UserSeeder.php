<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAgents = [];

        // 1️⃣ Create 2 SuperAgents
        for ($i = 1; $i <= 2; $i++) {
            $superAgents[] = User::create([
                'username' => 'SA-user' . $i, // SA-user1, SA-user2
                'user_type' => 'super_agent',
                'password' => Hash::make('password123'),
                'plain_password' => 'password123',
                'status' => true,
                'parent_id' => null, // top-level
            ]);
        }

        $agents = [];

        // 2️⃣ Create 4 Agents and assign to SuperAgents
        for ($i = 1; $i <= 4; $i++) {
            // Assign parent SuperAgent in round-robin
            $parent = $superAgents[($i - 1) % count($superAgents)];

            $agents[] = User::create([
                'username' => 'AG-user' . $i, // AG-user1, AG-user2, ...
                'user_type' => 'agent',
                'password' => Hash::make('password123'),
                'plain_password' => 'password123',
                'status' => true,
                'parent_id' => $parent->id,
            ]);
        }

        // 3️⃣ Create 20 SubAgents and assign to Agents
        for ($i = 1; $i <= 20; $i++) {
            // Assign parent Agent in round-robin
            $parent = $agents[($i - 1) % count($agents)];

            User::create([
                'username' => 'SUB-user' . $i, // SUB-user1, SUB-user2, ...
                'user_type' => 'sub_agent',
                'password' => Hash::make('password123'),
                'plain_password' => 'password123',
                'status' => true,
                'parent_id' => $parent->id,
            ]);
        }
    }
}
