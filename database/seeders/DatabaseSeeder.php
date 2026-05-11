<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hoaibao2284@gmail.com'],
            [
                'name' => 'Hoai Bao Owner',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'trungbao2285@gmail.com'],
            [
                'name' => 'Trung Bao Receiver',
                'password' => Hash::make('password456'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'quocthang2286@gmail.com'],
            [
                'name' => 'Quoc Thang Receiver',
                'password' => Hash::make('password789'),
                'email_verified_at' => now(),
            ]
        );
    }
}