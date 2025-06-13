<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProdiFactory extends Factory
{
    protected static $kodeProdiList = ['MIF', 'TKK', 'TET'];

    public function definition(): array
    {
        $jenjang = $this->faker->randomElement(['D3', 'D4']);
        $kode = array_shift(self::$kodeProdiList); // ambil satu, lalu hapus dari list

        return [
            'kode_prodi' => $kode,
            'jenjang' => $jenjang,
            'nama_prodi' => $jenjang . ' ' . $this->faker->word(),
        ];
    }
}



