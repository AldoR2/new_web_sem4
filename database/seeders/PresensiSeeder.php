<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        Presensi::factory()->count(20)->create();
    }
}
