<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kecamatans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json = File::get(database_path('data/districts.json'));
        $districts = json_decode($json, true);

        $insertData = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'kota_id' => $item['kota_id'],
                'name' => $item['name'],
                'alt_name' => $item['alt_name'] ?? null,
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
            ];
        }, $districts);

        DB::table('kecamatans')->insert($insertData);
    }
}

