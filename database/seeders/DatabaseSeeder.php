<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            PermissionSeeder::class,
            AdminSettingsSeeder::class,
            YearSeeder::class,
            GameSeeder::class,
            UserSeeder::class,
            ResultEntrySeeder::class,
        ]);
    }
}
