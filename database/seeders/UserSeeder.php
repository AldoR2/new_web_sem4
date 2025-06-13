<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'nim' => null,
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // Dosen
        User::firstOrCreate(
            ['email' => 'dosen@example.com'],
            [
                'name' => 'Dosen 1',
                'nim' => null,
                'role' => 'dosen',
                'password' => Hash::make('password'),
            ]
        );

        // Mahasiswa (gunakan factory)
        if (User::where('role', 'mahasiswa')->count() < 10) {
            User::factory()->count(10)->create([
                'role' => 'mahasiswa',
            ]);
        }
    }
}


