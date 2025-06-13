<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matkul;
use App\Models\Prodi;
use App\Models\TahunAjaran;

class MatkulSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::inRandomOrder()->first();
        $prodis = Prodi::all();

        $templateMatkuls = [
            ['nama_matkul' => 'Algoritma dan Pemrograman', 'semester' => 1, 'durasi_matkul' => 3],
            ['nama_matkul' => 'Pengantar TI', 'semester' => 1, 'durasi_matkul' => 2],
            ['nama_matkul' => 'Struktur Data', 'semester' => 2, 'durasi_matkul' => 3],
            ['nama_matkul' => 'Sistem Operasi', 'semester' => 2, 'durasi_matkul' => 3],
        ];

        foreach ($prodis as $prodi) {
            $counter = 1;
            foreach ($templateMatkuls as $template) {
                $kode_matkul = $prodi->kode_prodi . date('y') . str_pad($counter, 3, '0', STR_PAD_LEFT);

                Matkul::create([
                    'kode_matkul' => $kode_matkul,
                    'nama_matkul' => $template['nama_matkul'],
                    'semester' => $template['semester'],
                    'durasi_matkul' => $template['durasi_matkul'],
                    'prodi_id' => $prodi->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ]);

                $counter++;
            }
        }
    }
}
