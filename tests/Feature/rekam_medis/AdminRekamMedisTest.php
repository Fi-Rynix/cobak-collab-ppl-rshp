<?php

namespace Tests\Feature\rekam_medis;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\Pet;
use App\Models\TemuDokter;

class AdminRekamMedisTest extends TestCase
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

        // return both user and role_user id
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

    public function test_admin_can_create_rekam_medis_when_temu_dokter_status_is_D()
    {
        // arrange: admin and dokter + owner/pet + temu dokter with status D + kode tindakan
        [$admin, $adminRole] = $this->createUserWithRole(1); // Administrator
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2); // Dokter role

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
            'kode' => 'T-TEST',
            'deskripsi_tindakan_terapi' => 'Test tindakan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa test',
            'temuan_klinis' => 'Temuan klinis test',
            'diagnosa' => 'Diagnosa test',
            'detail' => 'Detail terapi',
            'idkode_tindakan_terapi' => $idkode,
        ];

        // act: act as admin and post
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1, 'iduser' => $admin->iduser])
            ->post('/Admin/RekamMedis/store-rekam-medis', $payload);

        // assert: redirected and DB has rekam_medis and detail_rekam_medis
        $response->assertRedirect();

        $this->assertDatabaseHas('rekam_medis', [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa test',
            'temuan_klinis' => 'Temuan klinis test',
            'diagnosa' => 'Diagnosa test',
        ]);

        $rekam = DB::table('rekam_medis')->where('idreservasi_dokter', $idreservasi)->first();
        $this->assertNotNull($rekam);

        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekam->idrekam_medis,
            'detail' => 'Detail terapi',
        ]);

        // temu_dokter status should now be 'D'
        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $idreservasi,
            'status' => 'D',
        ]);
    }

    public function test_admin_cannot_create_rekam_medis_when_temu_dokter_status_is_W()
    {
        // arrange: admin and dokter + owner/pet + temu dokter with status W + kode tindakan
        [$admin, $adminRole] = $this->createUserWithRole(1); // Administrator
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2); // Dokter role

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
            'kode' => 'T-TEST-2',
            'deskripsi_tindakan_terapi' => 'Test tindakan 2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa should fail',
            'temuan_klinis' => 'Temuan klinis should fail',
            'diagnosa' => 'Diagnosa should fail',
            'detail' => 'Detail should fail',
            'idkode_tindakan_terapi' => $idkode,
        ];

        // act: act as admin and post
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1, 'iduser' => $admin->iduser])
            ->post('/Admin/RekamMedis/store-rekam-medis', $payload);

        // expected behavior: creation should NOT be allowed because temu_dokter is still waiting (W)
        // the application should prevent creating rekam medis for reservations with status 'W'.

        // assert: ensure no rekam_medis was created for this reservation
        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => 'Anamnesa should fail',
        ]);

        // Also ensure temu_dokter still has status 'W'
        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $idreservasi,
            'status' => 'W',
        ]);
    }

    public function test_validation_fails_when_required_fields_are_empty()
    {
        [$admin, $adminRole] = $this->createUserWithRole(1);
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2);
        [$owner, $idpemilik, $idpet] = $this->createOwnerAndPet();

        $idreservasi = DB::table('temu_dokter')->insertGetId([
            'no_urut' => 3,
            'waktu_daftar' => now(),
            'status' => 'D',
            'idpet' => $idpet,
            'idrole_user' => $dokterRoleUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idkode = DB::table('kode_tindakan_terapi')->insertGetId([
            'kode' => 'T-EMPTY',
            'deskripsi_tindakan_terapi' => 'Empty test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // send empty strings for required text fields
        $payload = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => '',
            'temuan_klinis' => '',
            'diagnosa' => '',
            'detail' => '',
            'idkode_tindakan_terapi' => $idkode,
        ];

        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1, 'iduser' => $admin->iduser])
            ->post('/Admin/RekamMedis/store-rekam-medis', $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['anamnesa', 'temuan_klinis', 'diagnosa', 'detail']);

        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $idreservasi,
        ]);
    }

    public function test_validation_fails_when_fields_exceed_max_length()
    {
        [$admin, $adminRole] = $this->createUserWithRole(1);
        [$dokterUser, $dokterRoleUserId] = $this->createUserWithRole(2);
        [$owner, $idpemilik, $idpet] = $this->createOwnerAndPet();

        $idreservasi = DB::table('temu_dokter')->insertGetId([
            'no_urut' => 4,
            'waktu_daftar' => now(),
            'status' => 'D',
            'idpet' => $idpet,
            'idrole_user' => $dokterRoleUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idkode = DB::table('kode_tindakan_terapi')->insertGetId([
            'kode' => 'T-LONG',
            'deskripsi_tindakan_terapi' => 'Long test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // create strings longer than 1000 chars
        $long = str_repeat('a', 1200);

        $payload = [
            'idreservasi_dokter' => $idreservasi,
            'anamnesa' => $long,
            'temuan_klinis' => $long,
            'diagnosa' => $long,
            'detail' => $long,
            'idkode_tindakan_terapi' => $idkode,
        ];

        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1, 'iduser' => $admin->iduser])
            ->post('/Admin/RekamMedis/store-rekam-medis', $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['anamnesa', 'temuan_klinis', 'diagnosa', 'detail']);

        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $idreservasi,
        ]);
    }
}
