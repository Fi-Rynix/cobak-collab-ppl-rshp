<?php

namespace Tests\Feature\rekam_medis;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PerawatRekamMedisTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithRole(int $roleId)
    {
        $user = User::create([
            'nama' => 'Test User '.$roleId,
            'email' => 'test'.$roleId.'@example.test',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleUserId = DB::table('role_user')->insertGetId([
            'iduser' => $user->iduser,
            'idrole' => $roleId,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$user, $roleUserId];
    }

    protected function createOwnerAndPet()
    {
        $owner = User::create([
            'nama' => 'Owner Test',
            'email' => 'owner@example.test',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $owner->iduser,
            'no_wa' => '08123456789',
            'alamat' => 'Test Address',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpet = DB::table('pet')->insertGetId([
            'nama' => 'Bobby',
            'tanggal_lahir' => now(),
            'warna_tanda' => 'brown',
            'jenis_kelamin' => 'M',
            'idpemilik' => $idpemilik,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$owner, $idpemilik, $idpet];
    }

    public function test_perawat_can_create_rekam_medis_when_temu_dokter_status_is_D()
    {
        [$perawat, $perawatRole] = $this->createUserWithRole(3); // perawat role
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2); // dokter

        [$owner, $idpemilik, $idpet] = $this->createOwnerAndPet();

        $idreservasi = DB::table('temu_dokter')->insertGetId([
            'no_urut' => 1,
            'waktu_daftar' => now(),
            'status' => 'D',
            'idpet' => $idpet,
            'idrole_user' => $dokterRoleUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idkode = DB::table('kode_tindakan_terapi')->insertGetId([
            'kode' => 'T-PERAWAT-1',
            'deskripsi_tindakan_terapi' => 'Test tindakan perawat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa perawat',
            'temuan_klinis' => 'Temuan perawat',
            'diagnosa' => 'Diagnosa perawat',
            'idkode_tindakan_terapi' => $idkode,
        ];

        $response = $this->actingAs($perawat)
            ->withSession(['idrole' => 4, 'iduser' => $perawat->iduser])
            ->post('/Perawat/RekamMedis/store-rekam-medis', $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('rekam_medis', [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa perawat',
        ]);
    }

    public function test_perawat_cannot_create_rekam_medis_when_temu_dokter_status_is_W()
    {
        [$perawat, $perawatRole] = $this->createUserWithRole(3);
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2);
        [$owner, $idpemilik, $idpet] = $this->createOwnerAndPet();

        $idreservasi = DB::table('temu_dokter')->insertGetId([
            'no_urut' => 2,
            'waktu_daftar' => now(),
            'status' => 'W',
            'idpet' => $idpet,
            'idrole_user' => $dokterRoleUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idkode = DB::table('kode_tindakan_terapi')->insertGetId([
            'kode' => 'T-PERAWAT-2',
            'deskripsi_tindakan_terapi' => 'Test tindakan perawat 2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa should fail',
            'temuan_klinis' => 'Temuan should fail',
            'diagnosa' => 'Diagnosa should fail',
            'idkode_tindakan_terapi' => $idkode,
        ];

        $response = $this->actingAs($perawat)
            ->withSession(['idrole' => 4, 'iduser' => $perawat->iduser])
            ->post('/Perawat/RekamMedis/store-rekam-medis', $payload);

        // Expectation: application should prevent creation for status 'W'
        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa should fail',
        ]);

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $idreservasi,
            'status' => 'W',
        ]);
    }

    public function test_perawat_validation_empty_and_overlength()
    {
        [$perawat, $perawatRole] = $this->createUserWithRole(3);
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2);
        [$owner, $idpemilik, $idpet] = $this->createOwnerAndPet();

        $idreservasi = DB::table('temu_dokter')->insertGetId([
            'no_urut' => 5,
            'waktu_daftar' => now(),
            'status' => 'D',
            'idpet' => $idpet,
            'idrole_user' => $dokterRoleUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idkode = DB::table('kode_tindakan_terapi')->insertGetId([
            'kode' => 'T-PERAWAT-3',
            'deskripsi_tindakan_terapi' => 'Test tindakan perawat 3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // empty fields
        $payloadEmpty = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => '',
            'temuan_klinis' => '',
            'diagnosa' => '',
            'idkode_tindakan_terapi' => $idkode,
        ];

        $resp1 = $this->actingAs($perawat)
            ->withSession(['idrole' => 4, 'iduser' => $perawat->iduser])
            ->post('/Perawat/RekamMedis/store-rekam-medis', $payloadEmpty);

        $resp1->assertStatus(302);
        $resp1->assertSessionHasErrors(['anamnesa', 'temuan_klinis', 'diagnosa']);

        // overlength
        $long = str_repeat('a', 1200);
        $payloadLong = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => $long,
            'temuan_klinis' => $long,
            'diagnosa' => $long,
            'idkode_tindakan_terapi' => $idkode,
        ];

        $resp2 = $this->actingAs($perawat)
            ->withSession(['idrole' => 4, 'iduser' => $perawat->iduser])
            ->post('/Perawat/RekamMedis/store-rekam-medis', $payloadLong);

        $resp2->assertStatus(302);
        $resp2->assertSessionHasErrors(['anamnesa', 'temuan_klinis', 'diagnosa']);
    }
}
