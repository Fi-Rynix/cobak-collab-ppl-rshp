<?php

namespace Tests\Feature\IntegrationTest;

use Tests\TestCase;
use App\Models\RoleUser;
use App\Models\RekamMedis;
use App\Models\DetailRekamMedis;
use App\Models\TemuDokter;
use App\Models\KodeTindakanTerapi;
use App\Models\Pet;
use App\Models\Pemilik;
use App\Models\RasHewan;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RekamMedisDokter extends TestCase
{
    use RefreshDatabase;
    use WithRole;

    private function createPetSetup()
    {
        $ras_hewan = RasHewan::factory()->create();
        $pemilik = Pemilik::factory()->create();

        $pet = Pet::factory()
            ->forPemilik($pemilik)
            ->forRasHewan($ras_hewan)
            ->create();

        return compact('ras_hewan', 'pemilik', 'pet');
    }

    private function createRekamMedisWithDokter($dokter_role)
    {
        $petSetup = $this->createPetSetup();

        $temu_dokter = TemuDokter::factory()
            ->forPet($petSetup['pet'])
            ->forRoleUser($dokter_role)
            ->create();

        $rekam_medis = RekamMedis::factory()
            ->forDokter($dokter_role)
            ->forTemuDokter($temu_dokter)
            ->create();

        return array_merge($petSetup, compact('temu_dokter', 'rekam_medis'));
    }

    private function createRekamMedisSetup()
    {
        $user_dokter = self::dokter();

        $dokter_role = RoleUser::where('iduser', $user_dokter->iduser)
            ->where('idrole', 2)
            ->first();

        $data = $this->createRekamMedisWithDokter($dokter_role);

        $kode_tindakan_terapi = KodeTindakanTerapi::factory()->create();

        return array_merge(
            compact('user_dokter', 'dokter_role', 'kode_tindakan_terapi'),
            $data
        );
    }

    public function test_dokter_berhasil_tambah_detail_ke_rekam_medis_miliknya()
    {
        $setup = $this->createRekamMedisSetup();
        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $detail_text = 'Berikan 500mg Amoxicillin setiap 12 jam selama 10 hari';
        $detail_data = [
            'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
            'detail' => $detail_text
        ];

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $setup['rekam_medis']->idrekam_medis),
            $detail_data
        );

        $response->assertRedirect(
            route('Dokter.RekamMedis.detail-rekam-medis', $setup['rekam_medis']->idrekam_medis)
        );
        $response->assertSessionHas('success', 'Tindakan berhasil ditambahkan');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
            'detail' => $detail_text,
            'deleted_at' => null,
            'deleted_by' => null
        ]);

        $created_detail = DetailRekamMedis::where('idrekam_medis', $setup['rekam_medis']->idrekam_medis)
            ->where('idkode_tindakan_terapi', $setup['kode_tindakan_terapi']->idkode_tindakan_terapi)
            ->first();

        $this->assertNotNull($created_detail);
        $this->assertEquals($setup['rekam_medis']->idrekam_medis, $created_detail->idrekam_medis);
        $this->assertEquals($setup['kode_tindakan_terapi']->idkode_tindakan_terapi, $created_detail->idkode_tindakan_terapi);
        $this->assertEquals($detail_text, $created_detail->detail);

        $loaded_therapy = $created_detail->kodeTindakanTerapi;
        $this->assertEquals($setup['kode_tindakan_terapi']->kode, $loaded_therapy->kode);
        $this->assertEquals($setup['kode_tindakan_terapi']->deskripsi_tindakan_terapi, $loaded_therapy->deskripsi_tindakan_terapi);

        $this->assertDatabaseHas('rekam_medis', [
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'anamnesa' => $setup['rekam_medis']->anamnesa,
            'temuan_klinis' => $setup['rekam_medis']->temuan_klinis,
            'diagnosa' => $setup['rekam_medis']->diagnosa,
            'dokter_pemeriksa' => $setup['dokter_role']->idrole_user
        ]);

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $setup['temu_dokter']->idreservasi_dokter,
            'idpet' => $setup['pet']->idpet,
            'idrole_user' => $setup['dokter_role']->idrole_user
        ]);
    }

    public function test_dokter_gagal_tambah_detail_deskripsi_kosong()
    {
        $setup = $this->createRekamMedisSetup();
        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $setup['rekam_medis']->idrekam_medis),
            [
                'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
                'detail' => ''
            ]
        );

        $response->assertSessionHasErrors('detail');
        
        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
        ]);
    }

    public function test_dokter_gagal_tambah_detail_karena_rekam_medis_tidak_ada()
    {
        $setup = $this->createRekamMedisSetup();
        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', 99999),
            [
                'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
                'detail' => 'Some detail'
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Rekam medis tidak ditemukan.');
    }

    public function test_dokter_gagal_tambah_detail_ke_rekam_medis_bukan_miliknya()
    {
        $dokter_a = self::dokter();
        $dokter_b = self::dokter();

        $dokter_b_role = RoleUser::where('iduser', $dokter_b->iduser)
            ->where('idrole', 2)
            ->first();

        $data_b = $this->createRekamMedisWithDokter($dokter_b_role);

        $kode_tindakan_terapi = KodeTindakanTerapi::factory()->create();

        $this->actingAs($dokter_a);
        session(['idrole' => 2]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $data_b['rekam_medis']->idrekam_medis),
            [
                'idkode_tindakan_terapi' => $kode_tindakan_terapi->idkode_tindakan_terapi,
                'detail' => 'Unauthorized detail'
            ]
        );

        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk menambah tindakan.');
        
        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $data_b['rekam_medis']->idrekam_medis,
        ]);
    }

    public function test_perawat_gagal_tambah_detail_rekam_medis_route_tidak_ada()
    {
        $perawat = self::perawat();
        $this->actingAs($perawat);
        session(['idrole' => 3]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', 1),
            ['idkode_tindakan_terapi' => 1, 'detail' => 'test']
        );

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function test_admin_gagal_tambah_detail_route_tidak_ada()
    {
        $admin = self::admin();
        $this->actingAs($admin);
        session(['idrole' => 1]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', 1),
            ['idkode_tindakan_terapi' => 1, 'detail' => 'test']
        );

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function test_dokter_berhasil_update_detail_ke_rekam_medis_miliknya()
    {
        $setup = $this->createRekamMedisSetup();
        
        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
            'detail' => 'Original detail text'
        ]);

        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $new_therapy = KodeTindakanTerapi::factory()->create();

        $response = $this->put(
            route('Dokter.RekamMedis.update-detail', $detail->iddetail_rekam_medis),
            [
                'idkode_tindakan_terapi' => $new_therapy->idkode_tindakan_terapi,
                'detail' => 'Updated detail text with new information'
            ]
        );

        $response->assertRedirect(
            route('Dokter.RekamMedis.detail-rekam-medis', $setup['rekam_medis']->idrekam_medis)
        );
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'idkode_tindakan_terapi' => $new_therapy->idkode_tindakan_terapi,
            'detail' => 'Updated detail text with new information'
        ]);
    }

    public function test_dokter_gagal_update_detail_kode_invalid()
    {
        $setup = $this->createRekamMedisSetup();
        
        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
            'detail' => 'Original detail'
        ]);

        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->put(
            route('Dokter.RekamMedis.update-detail', $detail->iddetail_rekam_medis),
            [
                'idkode_tindakan_terapi' => 99999,
                'detail' => 'Updated detail'
            ]
        );

        $response->assertSessionHasErrors('idkode_tindakan_terapi');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'detail' => 'Original detail'
        ]);
    }

    public function test_dokter_gagal_update_detail_ke_rekam_medis_dokter_lain()
    {
        $dokter_a = self::dokter();
        $dokter_b = self::dokter();

        $dokter_b_role = RoleUser::where('iduser', $dokter_b->iduser)->where('idrole', 2)->first();

        $data_b = $this->createRekamMedisWithDokter($dokter_b_role);

        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $data_b['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => KodeTindakanTerapi::factory()->create()->idkode_tindakan_terapi,
            'detail' => 'Dokter B detail'
        ]);

        $this->actingAs($dokter_a);
        session(['idrole' => 2]);

        $response = $this->put(
            route('Dokter.RekamMedis.update-detail', $detail->iddetail_rekam_medis),
            [
                'idkode_tindakan_terapi' => KodeTindakanTerapi::factory()->create()->idkode_tindakan_terapi,
                'detail' => 'Hacked detail'
            ]
        );

        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengubah tindakan.');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'detail' => 'Dokter B detail'
        ]);
    }

    public function test_dokter_berhasil_hapus_detail_ke_rekam_medis_miliknya()
    {
        $setup = $this->createRekamMedisSetup();
        
        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['kode_tindakan_terapi']->idkode_tindakan_terapi,
            'detail' => 'Detail to delete'
        ]);

        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->delete(
            route('Dokter.RekamMedis.delete-detail', $detail->iddetail_rekam_medis)
        );

        $response->assertRedirect(
            route('Dokter.RekamMedis.detail-rekam-medis', $setup['rekam_medis']->idrekam_medis)
        );
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'deleted_by' => $setup['user_dokter']->iduser
        ]);

        $deleted_detail = DetailRekamMedis::where('iddetail_rekam_medis', $detail->iddetail_rekam_medis)->first();
        $this->assertNotNull($deleted_detail->deleted_at);
    }

    public function test_dokter_gagal_hapus_detail_dokter_lain()
    {
        $dokter_a = self::dokter();
        $dokter_b = self::dokter();

        $dokter_b_role = RoleUser::where('iduser', $dokter_b->iduser)->where('idrole', 2)->first();

        $data_b = $this->createRekamMedisWithDokter($dokter_b_role);

        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $data_b['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => KodeTindakanTerapi::factory()->create()->idkode_tindakan_terapi,
            'detail' => 'Dokter B detail'
        ]);

        $this->actingAs($dokter_a);
        session(['idrole' => 2]);

        $response = $this->delete(
            route('Dokter.RekamMedis.delete-detail', $detail->iddetail_rekam_medis)
        );

        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk menghapus tindakan.');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'deleted_at' => null
        ]);
    }
}