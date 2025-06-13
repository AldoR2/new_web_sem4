<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TahunAjaranFactory extends Factory
{
    public function definition(): array
    {
        $tahunAwal = $this->faker->numberBetween(2020, 2025);
        $tahunAkhir = $tahunAwal + 1;

        return [
            'tahun_awal' => $tahunAwal,
            'tahun_akhir' => $tahunAkhir,
            'keterangan' => $this->faker->randomElement([
                'Ganjil', 'Genap'
            ]),
            'status' => $this->faker->boolean ? 1 : 0, // 1 = aktif, 0 = nonaktif
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
