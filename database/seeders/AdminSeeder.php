<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing admins (optional - remove if you want to keep existing data)
        // Admin::truncate();

        // Create first admin
        Admin::updateOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'Admin One',
                'email' => 'admin1@example.com',
                'password' => Hash::make('password123'),
            ]
        );

        // Create second admin
        Admin::updateOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@example.com',
                'password' => Hash::make('password123'),
            ]
        );

        $this->command->info('3 admins seeded successfully!');
    }
}