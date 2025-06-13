<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{

    public function run(): void
    {
        $mahasiswaUsers = User::where('role', 'mahasiswa')->get();

        foreach ($mahasiswaUsers as $user) {
            if (!$user->mahasiswa) {
                Mahasiswa::factory()->create(['user_id' => $user->id]);
            }
        }
    }
}
