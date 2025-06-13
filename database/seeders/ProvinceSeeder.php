<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan cek foreign key untuk truncate tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('provinsis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Baca file JSON provinsi dari folder database/data
        $json = File::get(database_path('data/provinces.json'));
        $provinces = json_decode($json, true);

        // Map data JSON ke format tabel provinsis
        $insertData = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'alt_name' => $item['alt_name'] ?? null,
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
            ];
        }, $provinces);

        // Insert ke tabel provinsis
        DB::table('provinsis')->insert($insertData);
    }
}
