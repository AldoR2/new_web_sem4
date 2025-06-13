<?php

namespace Database\Factories;

use App\Models\Kelurahan;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaFactory extends Factory
{
    public function definition(): array
    {
        $village = Kelurahan::whereHas('kecamatan.kota.provinsi')->inRandomOrder()->first();

        if (!$village) {
            throw new \Exception("Tidak ditemukan kelurahan dengan relasi lokasi lengkap.");
        }

        $district = $village->kecamatan;
        $regency = $district->kota;
        $province = $regency->provinsi;

        $jenisKelaminFull = $this->faker->randomElement(['Laki-laki', 'Perempuan']);
        $jenisKelamin = $jenisKelaminFull === 'Laki-laki' ? 'L' : 'P';

        $agama = $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);

        return [
            'user_id' => User::where('role', 'mahasiswa')
                ->whereDoesntHave('mahasiswa')
                ->inRandomOrder()
                ->first()?->id,

            'nim' => $this->faker->unique()->numerify('##########'),
            'rfid' => $this->faker->optional()->regexify('[A-Z0-9]{10,30}'),
            'nama' => $this->faker->name($jenisKelamin === 'L' ? 'male' : 'female'),
            'jenis_kelamin' => $jenisKelamin,
            'agama' => $agama,
            'tempat_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '2004-12-31'),
            'email' => $this->faker->unique()->userName() . '@gmail.com',
            'no_telp' => '+62' . $this->faker->numerify('8#########'),
            'alamat' => "$village->name, $district->name, $regency->name, $province->name",

            // ✅ Kolom lokasi dengan nama yang sudah disesuaikan
            'provinsi_id' => $province->id,
            'kota_id' => $regency->id,
            'kecamatan_id' => $district->id,
            'kelurahan_id' => $village->id,

            'prodi_id' => Prodi::inRandomOrder()->first()?->id ?? Prodi::factory()->create()->id,
            'tahun_masuk' => $this->faker->year(),
            'tahun_ajaran_id' => TahunAjaran::inRandomOrder()->first()?->id ?? TahunAjaran::factory()->create()->id,
            'semester' => $this->faker->numberBetween(1, 8),
            'email_verified_at' => now(),
            'foto' => null,
        ];
    }
}
