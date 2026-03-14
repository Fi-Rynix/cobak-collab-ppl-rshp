<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori')->insert([
            [
                'idkategori' => 1,
                'nama_kategori' => 'Pemeriksaan Rutin',
            ],
            [
                'idkategori' => 2,
                'nama_kategori' => 'Vaksinasi',
            ],
            [
                'idkategori' => 3,
                'nama_kategori' => 'Grooming',
            ],
            [
                'idkategori' => 4,
                'nama_kategori' => 'Operasi',
            ],
            [
                'idkategori' => 5,
                'nama_kategori' => 'Perawatan Gigi',
            ],
            [
                'idkategori' => 6,
                'nama_kategori' => 'Konsultasi Nutrisi',
            ],
        ]);
    }
}
