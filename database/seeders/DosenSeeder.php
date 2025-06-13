<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosenUsers = User::where('role', 'dosen')->get();

        foreach ($dosenUsers as $user) {
            if (!$user->dosen) {
                Dosen::factory()->create(['user_id' => $user->id]);
            }
        }
    }
}
