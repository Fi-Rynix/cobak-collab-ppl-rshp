<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert 5 predefined roles
        DB::table('role')->insert([
            [
                'idrole' => 1,
                'nama_role' => 'Admin',
                'deskripsi' => 'Administrator sistem',
            ],
            [
                'idrole' => 2,
                'nama_role' => 'Dokter',
                'deskripsi' => 'Dokter hewan',
            ],
            [
                'idrole' => 3,
                'nama_role' => 'Perawat',
                'deskripsi' => 'Perawat hewan',
            ],
            [
                'idrole' => 4,
                'nama_role' => 'Resepsionis',
                'deskripsi' => 'Resepsionis klinik',
            ],
            [
                'idrole' => 5,
                'nama_role' => 'Pemilik',
                'deskripsi' => 'Pemilik hewan peliharaan',
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('role')->whereIn('idrole', [1, 2, 3, 4, 5])->delete();
    }
};
