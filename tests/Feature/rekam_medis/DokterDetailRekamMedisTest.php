<?php

namespace Tests\Feature\rekam_medis;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DokterDetailRekamMedisTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithRole(int $roleId)
    {
        $user = User::create([
            'nama' => 'Test User '.$roleId.'-'.uniqid(),
            'email' => 'test'.$roleId.'-'.uniqid().'@example.test',
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

    protected function createOwnerPetReservasi($dokterRoleUserId)
    {
        $owner = User::create([
            'nama' => 'Owner '.uniqid(),
            'email' => 'owner'.uniqid().'@example.test',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $owner->iduser,
            'no_wa' => '081234567890',
            'alamat' => 'Addr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpet = DB::table('pet')->insertGetId([
            'nama' => 'Pet '.uniqid(),
            'tanggal_lahir' => now(),
            'warna_tanda' => 'brown',
            'jenis_kelamin' => 'M',
            'idpemilik' => $idpemilik,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idreservasi = DB::table('temu_dokter')->insertGetId([
            'no_urut' => 1,
            'waktu_daftar' => now(),
            'status' => 'D',
            'idpet' => $idpet,
            'idrole_user' => $dokterRoleUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$owner, $idpemilik, $idpet, $idreservasi];
    }

    public function test_dokter_can_add_detail_only_for_their_rekam_medis()
    {
        // dokter A (owner of rekam_medis)
        [$dokterA, $dokterARole] = $this->createUserWithRole(2);
        // dokter B (another doctor)
        [$dokterB, $dokterBRole] = $this->createUserWithRole(2);

        // create owner/pet/reservasi assigned to dokterA
        [$owner, $idpemilik, $idpet, $idreservasi] = $this->createOwnerPetReservasi($dokterARole);

        // create rekam_medis tied to that reservasi and dokterA
        $idrekam = DB::table('rekam_medis')->insertGetId([
            'created_at' => now(),
            'anamnesa' => 'test',
            'temuan_klinis' => 'test',
            'diagnosa' => 'test',
            'dokter_pemeriksa' => $dokterARole,
            'idreservasi_dokter' => $idreservasi,
        ]);

        // kode tindakan
        $idkode = DB::table('kode_tindakan_terapi')->insertGetId([
            'kode' => 'KD1',
            'deskripsi_tindakan_terapi' => 'desc',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // dokterA adds detail -> should succeed
        $payload = ['idkode_tindakan_terapi' => $idkode, 'detail' => 'treatment A'];

        $respA = $this->actingAs($dokterA)
            ->withSession(['idrole' => 2, 'iduser' => $dokterA->iduser])
            ->post('/Dokter/RekamMedis/store-detail/'.$idrekam, $payload);

        $respA->assertRedirect();
        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $idrekam,
            'detail' => 'treatment A',
        ]);

        // dokterB attempts to add detail -> should be denied
        $payloadB = ['idkode_tindakan_terapi' => $idkode, 'detail' => 'treatment B'];

        $respB = $this->actingAs($dokterB)
            ->withSession(['idrole' => 2, 'iduser' => $dokterB->iduser])
            ->post('/Dokter/RekamMedis/store-detail/'.$idrekam, $payloadB);

        // controller redirects back with error message
        $respB->assertRedirect();
        $respB->assertSessionHas('error');

        // ensure dokterB detail not inserted
        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $idrekam,
            'detail' => 'treatment B',
        ]);
    }

    public function test_validation_blocks_invalid_detail_submission()
    {
        [$dokter, $dokterRole] = $this->createUserWithRole(2);
        [$owner, $idpemilik, $idpet, $idreservasi] = $this->createOwnerPetReservasi($dokterRole);

        $idrekam = DB::table('rekam_medis')->insertGetId([
            'created_at' => now(),
            'anamnesa' => 'test',
            'temuan_klinis' => 'test',
            'diagnosa' => 'test',
            'dokter_pemeriksa' => $dokterRole,
            'idreservasi_dokter' => $idreservasi,
        ]);

        // missing fields
        $resp = $this->actingAs($dokter)
            ->withSession(['idrole' => 2, 'iduser' => $dokter->iduser])
            ->post('/Dokter/RekamMedis/store-detail/'.$idrekam, []);

        $resp->assertStatus(302);
        $resp->assertSessionHasErrors(['idkode_tindakan_terapi', 'detail']);

        // overlength detail
        $long = str_repeat('a', 1200);
        $idkode = DB::table('kode_tindakan_terapi')->insertGetId(['kode'=>'KX','deskripsi_tindakan_terapi'=>'x','created_at'=>now(),'updated_at'=>now()]);

        $resp2 = $this->actingAs($dokter)
            ->withSession(['idrole' => 2, 'iduser' => $dokter->iduser])
            ->post('/Dokter/RekamMedis/store-detail/'.$idrekam, ['idkode_tindakan_terapi' => $idkode, 'detail' => $long]);

        $resp2->assertStatus(302);
        $resp2->assertSessionHasErrors(['detail']);
    }
}
