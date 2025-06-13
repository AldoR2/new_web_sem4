<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ruangan>
 */
class RuanganFactory extends Factory
{
    public function definition(): array
    {
        $jenis = ['Lab Komputer', 'Laboratorium Kimia', 'Ruang Kuliah', 'Ruang Dosen', 'Studio Multimedia'];
        $lantai = ['Lantai 1', 'Lantai 2', 'Lt. 3', 'Lantai 4', 'Lt. 5'];
        $nomor = $this->faker->numberBetween(101, 410); // untuk variasi ruang kuliah

        return [
            'nama_ruangan' => $this->faker->randomElement($jenis) . ' ' .
                             $this->faker->randomElement($lantai) . ' - ' . $nomor,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
