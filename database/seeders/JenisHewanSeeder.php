<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisHewanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_hewan')->insert([
            [
                'idjenis_hewan' => 1,
                'nama_jenis_hewan' => 'Anjing',
            ],
            [
                'idjenis_hewan' => 2,
                'nama_jenis_hewan' => 'Kucing',
            ],
            [
                'idjenis_hewan' => 3,
                'nama_jenis_hewan' => 'Burung',
            ],
            [
                'idjenis_hewan' => 4,
                'nama_jenis_hewan' => 'Kelinci',
            ],
            [
                'idjenis_hewan' => 5,
                'nama_jenis_hewan' => 'Hamster',
            ],
        ]);
    }
}
