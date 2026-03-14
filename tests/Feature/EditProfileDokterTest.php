<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class EditProfileDokterTest extends TestCase
{

    public function test_update_dokter_berhasil(): void
    {
        $this->withoutMiddleware();

        $admin = User::find(6);
        $this->actingAs($admin);

        $response = $this->put(
            route('Admin.Dokter.update-dokter', 3),
            [
                'alamat' => 'Alamat Baru',
                'no_hp' => '08123456789',
                'bidang_dokter' => 'Spesialis Karbit',
                'jenis_kelamin' => 'L'
            ]
        );

        $response->assertStatus(302);
    }


    public function test_update_dokter_gagal_email_sudah_ada(): void
    {
        $this->withoutMiddleware();

        $admin = User::find(6);
        $this->actingAs($admin);

        $response = $this->put(
            route('Admin.Dokter.update-dokter', 3),
            [
                'alamat' => 'Alamat Baru',
                'no_hp' => '08123456789',
                'bidang_dokter' => 'Spesialis Karbit',
                'jenis_kelamin' => 'L'
            ]
        );

        $response->assertStatus(302);
    }


    public function test_update_dokter_gagal_jenis_kelamin_salah(): void
    {
        $this->withoutMiddleware();

        $admin = User::find(6);
        $this->actingAs($admin);

        $response = $this->put(
            route('Admin.Dokter.update-dokter', 3),
            [
                'alamat' => 'Alamat Baru',
                'no_hp' => '08123456789',
                'bidang_dokter' => 'Spesialis Karbit',
                'jenis_kelamin' => 'X'
            ]
        );

        $response->assertSessionHasErrors('jenis_kelamin');
    }

}