<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['idrole' => 1, 'nama_role' => 'Admin'],
            ['idrole' => 2, 'nama_role' => 'Dokter'],
            ['idrole' => 3, 'nama_role' => 'Perawat'],
            ['idrole' => 4, 'nama_role' => 'Resepsionis'],
            ['idrole' => 5, 'nama_role' => 'Pemilik'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['idrole' => $role['idrole']],
                ['nama_role' => $role['nama_role']]
            );
        }
    }
}
