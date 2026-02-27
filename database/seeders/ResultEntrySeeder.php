<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Game;
use Carbon\Carbon;

class ResultEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch only active/published games
        $games = Game::where('status', true)->get();

        // Loop over last 7 days
        for ($daysAgo = 0; $daysAgo < 7; $daysAgo++) {
            $date = Carbon::now()->subDays($daysAgo)->startOfDay();

            foreach ($games as $game) {
                DB::table('result_entries')->insert([
                    'game_id' => $game->id,
                    'date' => $date,
                    'prize_one'   => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'prize_two'   => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'prize_three' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'prize_four'  => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'prize_five'  => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    // Numbers 1 to 30
                    'one' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'two' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'three' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'four' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'five' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'six' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'seven' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'eight' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'nine' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'ten' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'eleven' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twelve' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'thirteen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'fourteen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'fifteen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'sixteen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'seventeen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'eighteen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'nineteen' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_one' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_two' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_three' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_four' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_five' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_six' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_seven' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_eight' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'twenty_nine' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'thirty' => str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
