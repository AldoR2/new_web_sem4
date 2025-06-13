<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RegencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kotas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json = File::get(database_path('data/regencies.json'));
        $regencies = json_decode($json, true);

        // Mapping data JSON ke format yang sesuai dengan tabel regencies
        $insertData = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'provinsi_id' => $item['provinsi_id'],
                'name' => $item['name'],
                'alt_name' => $item['alt_name'] ?? null,
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
            ];
        }, $regencies);

        // Insert data ke tabel regencies
        DB::table('kotas')->insert($insertData);
    }
}
