<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\Matkul;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresensiFactory extends Factory
{
    public function definition(): array
    {
        $jamAwal = $this->faker->time('H:i:s');
        $jamAkhir = date('H:i:s', strtotime($jamAwal) + 3600); // +1 jam

        return [
            'presensi_id' => 'PRS-' . $this->faker->unique()->numerify('#####'),
            'tgl_presensi' => $this->faker->date(),
            'jam_awal' => $jamAwal,
            'jam_akhir' => $jamAkhir,
            'dosen_id' => Dosen::inRandomOrder()->first()?->id ?? 1,
            'prodi_id' => Prodi::inRandomOrder()->first()?->id ?? 1,
            'semester' => $this->faker->numberBetween(1, 8),
            'matkul_id' => Matkul::inRandomOrder()->first()?->id ?? 1,
            'ruangan_id' => Ruangan::inRandomOrder()->first()?->id ?? 1,
            'tahun_ajaran_id' => TahunAjaran::inRandomOrder()->first()?->id ?? 1,
            'link_zoom' => $this->faker->optional()->url(),
        ];
    }
}
