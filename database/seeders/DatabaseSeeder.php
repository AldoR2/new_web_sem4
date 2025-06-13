<?php

namespace Database\Seeders;

use \Laravolt\Indonesia\Seeds\IndonesiaSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->count(5)->state(['role' => 'admin'])->create();
        User::factory()->count(5)->state(['role' => 'dosen'])->create();
        User::factory()->count(10)->state(['role' => 'mahasiswa'])->create();

        $this->call([
            ProvinceSeeder::class,
            RegencySeeder::class,
            DistrictSeeder::class,
            VillageSeeder::class,
            UserSeeder::class,
            ProdiSeeder::class,
            TahunAjaranSeeder::class,
            RuanganSeeder::class,
            MatkulSeeder::class,
            MahasiswaSeeder::class,
            DosenSeeder::class,
            PresensiSeeder::class,
        ]);
}
}