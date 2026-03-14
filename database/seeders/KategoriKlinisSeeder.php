<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriKlinisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_klinis')->insert([
            [
                'idkategori_klinis' => 1,
                'nama_kategori_klinis' => 'Infeksi',
            ],
            [
                'idkategori_klinis' => 2,
                'nama_kategori_klinis' => 'Dermatologi',
            ],
            [
                'idkategori_klinis' => 3,
                'nama_kategori_klinis' => 'Orthopedi',
            ],
            [
                'idkategori_klinis' => 4,
                'nama_kategori_klinis' => 'Oftalmologi',
            ],
            [
                'idkategori_klinis' => 5,
                'nama_kategori_klinis' => 'Gastroenterologi',
            ],
            [
                'idkategori_klinis' => 6,
                'nama_kategori_klinis' => 'Neurologi',
            ],
            [
                'idkategori_klinis' => 7,
                'nama_kategori_klinis' => 'Kardiologi',
            ],
        ]);
    }
}
