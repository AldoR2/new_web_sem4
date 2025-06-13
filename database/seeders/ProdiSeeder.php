<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prodi;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_prodi' => 'MIF', 'jenjang' => 'D3', 'nama_prodi' => 'Manajemen Informatika'],
            ['kode_prodi' => 'TKK', 'jenjang' => 'D3', 'nama_prodi' => 'Teknik Komputer'],
            ['kode_prodi' => 'TET', 'jenjang' => 'D3', 'nama_prodi' => 'Teknik Elektronika'],
        ];

        foreach ($data as $item) {
            Prodi::create($item);
        }
    }
}

