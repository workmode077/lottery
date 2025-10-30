<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'lottery@gmail.com'],
            [
                'name' => 'Admin',
                'password' => 'lottery@123',
                'email_verified_at' => now(),
            ]
        );
    }
}
