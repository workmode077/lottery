<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game; 

class GameSeeder extends Seeder
{
   public function run(): void
    {
        $times = [
            '09:00:00', // 9 AM
            '12:00:00', // 12 PM
            '15:00:00', // 3 PM
        ];

        foreach ($times as $time) {
            Game::create([
                'time' => $time,
                'status' => true,
            ]);
        }
    }
}
