<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Kelurahan;
use App\Models\Prodi;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Database\Seeders\VillageSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravolt\Indonesia\Models\Village;

class DosenFactory extends Factory
{
    public function definition(): array
    {
        $village = Kelurahan::inRandomOrder()->first();

        if (!$village || !$village->kecamatan || !$village->kecamatan->kota || !$village->kecamatan->kota->provinsi) {
            throw new \Exception("Data lokasi tidak lengkap atau tidak nyambung (relasi tidak valid).");
        }

        $district = $village->kecamatan;
        $regency = $district->kota;
        $province = $regency->provinsi;

        $jenisKelaminFull = $this->faker->randomElement(['Laki-laki', 'Perempuan']);
        $jenisKelamin = $jenisKelaminFull === 'Laki-laki' ? 'L' : 'P';        
        $agama = $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);

        return [
            'user_id' => User::where('role', 'dosen')->inRandomOrder()->first()?->id ?? User::factory(),
            'nip' => $this->faker->unique()->numerify('##########'),
            'jenis_kelamin' => $jenisKelamin,
            'nama' => $this->faker->name($jenisKelaminFull === 'Laki-laki' ? 'male' : 'female'),
            'agama' => $agama,
            'tempat_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '1980-01-01'),
            'email' => $this->faker->unique()->userName() . '@gmail.com',
            'no_telp' => '08' . $this->faker->numerify('##########'),
            'alamat' => "$village->name, $district->name, $regency->name, $province->name",

            'provinsi_id' => $province->id,
            'kota_id' => $regency->id,
            'kecamatan_id' => $district->id,
            'kelurahan_id' => $village->id,


            'prodi_id' => Prodi::inRandomOrder()->first()?->id ?? 1,
            'foto' => null,
        ];
    }
}
