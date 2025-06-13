<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VillageSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = database_path('data/villages.json');
        if (!File::exists($jsonPath)) {
            throw new \Exception("File villages.json tidak ditemukan di: $jsonPath");
        }

        $json = File::get($jsonPath);
        $villages = json_decode($json, true);

        if (!is_array($villages)) {
            throw new \Exception("Format JSON tidak valid pada villages.json.");
        }

        // Ambil daftar district_id yang valid dari tabel kecamatans
        $validDistrictIds = DB::table('kecamatans')->pluck('id')->toArray();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kelurahans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $chunks = array_chunk($villages, 1000);

        foreach ($chunks as $chunk) {
            $insertData = [];

            foreach ($chunk as $item) {
                if (in_array($item['kecamatan_id'], $validDistrictIds)) {
                    $insertData[] = [
                        'id' => $item['id'],
                        'kecamatan_id' => $item['kecamatan_id'],
                        'name' => $item['name'],
                        'alt_name' => $item['alt_name'] ?? null,
                        'latitude' => $item['latitude'] ?? null,
                        'longitude' => $item['longitude'] ?? null,
                    ];
                } else {
                    // Optional: log data yang diskip
                    // info("Skip kelurahan id {$item['id']} karena district_id tidak valid");
                }
            }

            if (!empty($insertData)) {
                DB::table('kelurahans')->insert($insertData);
            }
        }
    }
}
