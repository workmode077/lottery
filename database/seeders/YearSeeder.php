<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class YearSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        foreach (range(2021, 2025) as $year) {
            DB::table('years')->insert([
                'year'       => $year,
                'slug'       => Str::slug($year),
                'status'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
