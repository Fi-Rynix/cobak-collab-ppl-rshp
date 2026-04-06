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

class IntegrationRekamMedisDokter extends TestCase
{
    use RefreshDatabase;
    use WithRole;

    /**
     * Helper: Create complete setup with Pet, Dokter, and RekamMedis
     */
    private function createRekamMedisSetup()
    {
        // Level 1: Pet & Owner
        $ras_hewan = RasHewan::factory()->create();
        $pemilik = Pemilik::factory()->create();
        $pet = Pet::factory()
            ->forPemilik($pemilik)
            ->forRasHewan($ras_hewan)
            ->create();

        // Level 2: Dokter
        $user_dokter = self::dokter();
        $dokter_role = RoleUser::where('iduser', $user_dokter->iduser)
            ->where('idrole', 2)
            ->first();

        // Level 3: Appointment & RekamMedis
        $temu_dokter = TemuDokter::factory()
            ->forPet($pet)
            ->forRoleUser($dokter_role)
            ->create();

        $rekam_medis = RekamMedis::factory()
            ->forDokter($dokter_role)
            ->forTemuDokter($temu_dokter)
            ->create();

        // Level 4: Therapy Code
        $therapy_code = KodeTindakanTerapi::factory()->create();

        return compact('user_dokter', 'dokter_role', 'rekam_medis', 'therapy_code', 'ras_hewan', 'pemilik', 'pet', 'temu_dokter');
    }

    /**
     * SCOPE 5 - SCENARIO 1: Dokter add detail to own RekamMedis
     * 
     * Flow TOP-DOWN:
     * 1. Setup: Create dokter user with RekamMedis dan KodeTindakanTerapi
     * 2. Action: Login dokter, POST ke store_detail route
     * 3. Assert: Verify redirect, database state, dan relationships
     * 
     * Expected: Detail created with therapy code & description
     */
    public function test_dokter_add_detail_to_own_rekam_medis_successfully()
    {
        // ==================== SETUP LEVEL 1: Create Pet & Owner ====================
        
        // Create RasHewan - factory auto-creates JenisHewan relationship
        $ras_hewan = RasHewan::factory()->create();
        
        // Create Pemilik (owner) - factory auto-creates User
        $pemilik = Pemilik::factory()->create();

        // Create Pet with factory methods handling relationships
        $pet = Pet::factory()
            ->forPemilik($pemilik)
            ->forRasHewan($ras_hewan)
            ->create();

        // ==================== SETUP LEVEL 2: Create Dokter User with Role ====================
        
        // WithRole::dokter() creates User + RoleUser (idrole=2) + Dokter record automatically
        $user_dokter = self::dokter();
        
        // Get the dokter's role_user record (automatically created by WithRole::dokter)
        $dokter_role = RoleUser::where('iduser', $user_dokter->iduser)
            ->where('idrole', 2)
            ->first();

        // ==================== SETUP LEVEL 3: Create Appointment & RekamMedis ====================
        
        // Create TemuDokter with factory methods handling relationships
        $temu_dokter = TemuDokter::factory()
            ->forPet($pet)
            ->forRoleUser($dokter_role)
            ->create();

        // Create RekamMedis with factory methods handling relationships
        $rekam_medis = RekamMedis::factory()
            ->forDokter($dokter_role)
            ->forTemuDokter($temu_dokter)
            ->create();

        // ==================== SETUP LEVEL 4: Create Therapy Code ====================
        
        // Create KodeTindakanTerapi - factory auto-creates Kategori & KategoriKlinis
        $therapy_code = KodeTindakanTerapi::factory()->create();

        // ==================== ACTION LEVEL 1: Authenticate ====================
        
        // Login sebagai dokter & set session for IsDokter middleware
        $this->actingAs($user_dokter);
        session(['idrole' => 2]);  // IsDokter middleware checks session('idrole')

        // ==================== ACTION LEVEL 2: Make HTTP Request ====================
        
        // Prepare POST data
        $detail_data = [
            'idkode_tindakan_terapi' => $therapy_code->idkode_tindakan_terapi,
            'detail' => 'Berikan 500mg Amoxicillin setiap 12 jam selama 10 hari'
        ];

        // POST to store_detail route (top-down: start from user action)
        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $rekam_medis->idrekam_medis),
            $detail_data
        );

        // ==================== ASSERTION LEVEL 1: HTTP Response ====================
        
        // Verify redirect to detail view with success message
        $response->assertRedirect(
            route('Dokter.RekamMedis.detail-rekam-medis', $rekam_medis->idrekam_medis)
        );
        $response->assertSessionHas('success', 'Tindakan berhasil ditambahkan');

        // ==================== ASSERTION LEVEL 2: Database State ====================
        
        // Verify DetailRekamMedis record exists in database
        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekam_medis->idrekam_medis,
            'idkode_tindakan_terapi' => $therapy_code->idkode_tindakan_terapi,
            'detail' => 'Berikan 500mg Amoxicillin setiap 12 jam selama 10 hari',
            'deleted_at' => null,
            'deleted_by' => null
        ]);

        // ==================== ASSERTION LEVEL 3: Relationships & Data Integrity ====================
        
        // Retrieve created detail from database
        $created_detail = DetailRekamMedis::where('idrekam_medis', $rekam_medis->idrekam_medis)
            ->where('idkode_tindakan_terapi', $therapy_code->idkode_tindakan_terapi)
            ->first();

        // Verify relationship to RekamMedis
        $this->assertNotNull($created_detail);
        $this->assertEquals($rekam_medis->idrekam_medis, $created_detail->idrekam_medis);
        
        // Verify relationship to KodeTindakanTerapi
        $this->assertEquals($therapy_code->idkode_tindakan_terapi, $created_detail->idkode_tindakan_terapi);
        
        // Verify detail content
        $this->assertEquals(
            'Berikan 500mg Amoxicillin setiap 12 jam selama 10 hari',
            $created_detail->detail
        );

        // Verify therapy code association
        $loaded_therapy = $created_detail->kodeTindakanTerapi;
        $this->assertEquals($therapy_code->kode, $loaded_therapy->kode);
        $this->assertEquals($therapy_code->deskripsi_tindakan_terapi, $loaded_therapy->deskripsi_tindakan_terapi);

        // ==================== ASSERTION LEVEL 4: Data Preservation ====================
        
        // Verify original RekamMedis is unchanged (fetch actual data from DB)
        $this->assertDatabaseHas('rekam_medis', [
            'idrekam_medis' => $rekam_medis->idrekam_medis,
            'anamnesa' => $rekam_medis->anamnesa,  // Use value from factory
            'temuan_klinis' => $rekam_medis->temuan_klinis,
            'diagnosa' => $rekam_medis->diagnosa,
            'dokter_pemeriksa' => $dokter_role->idrole_user
        ]);

        // Verify TemuDokter is unchanged
        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $temu_dokter->idreservasi_dokter,
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter_role->idrole_user
        ]);
    }

    /**
     * SCOPE 5 - SCENARIO 2: Dokter add detail fail - empty description
     * ❌ NEGATIVE TEST: Validation error
     */
    public function test_dokter_gagal_tambah_detail_deskripsi_kosong()
    {
        $setup = $this->createRekamMedisSetup();
        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $setup['rekam_medis']->idrekam_medis),
            [
                'idkode_tindakan_terapi' => $setup['therapy_code']->idkode_tindakan_terapi,
                'detail' => ''  // ← OVERRIDE: Empty detail
            ]
        );

        // Expect: Validation error
        $response->assertSessionHasErrors('detail');
        
        // Verify detail NOT created
        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
        ]);
    }

    /**
     * SCOPE 5 - SCENARIO 3: Dokter add detail fail - invalid RekamMedis ID
     * ❌ NEGATIVE TEST: RekamMedis not found
     */
    public function test_dokter_gagal_tambah_detail_rekam_medis_tidak_ada()
    {
        $setup = $this->createRekamMedisSetup();
        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        // Try with invalid RekamMedis ID
        $invalid_id = 99999;

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $invalid_id),
            [
                'idkode_tindakan_terapi' => $setup['therapy_code']->idkode_tindakan_terapi,
                'detail' => 'Some detail'
            ]
        );

        // Expect: Redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Rekam medis tidak ditemukan.');
    }

    /**
     * SCOPE 5 - SCENARIO 4: Dokter attempt add to OTHER dokter's RekamMedis
     * ❌ NEGATIVE TEST: Access denied
     */
    public function test_dokter_gagal_tambah_detail_bukan_punya_dia()
    {
        $dokter_a = self::dokter();
        $dokter_a_role = RoleUser::where('iduser', $dokter_a->iduser)->where('idrole', 2)->first();
        
        $dokter_b = self::dokter();  // Different dokter
        $dokter_b_role = RoleUser::where('iduser', $dokter_b->iduser)->where('idrole', 2)->first();

        // Create RekamMedis for dokter B
        $ras_hewan = RasHewan::factory()->create();
        $pemilik = Pemilik::factory()->create();
        $pet = Pet::factory()->forPemilik($pemilik)->forRasHewan($ras_hewan)->create();
        
        $temu_dokter = TemuDokter::factory()
            ->forPet($pet)
            ->forRoleUser($dokter_b_role)
            ->create();

        $rekam_medis_dokter_b = RekamMedis::factory()
            ->forDokter($dokter_b_role)  // ← Punya dokter B
            ->forTemuDokter($temu_dokter)
            ->create();

        $therapy_code = KodeTindakanTerapi::factory()->create();

        // Login as dokter A, try add detail to dokter B's RekamMedis
        $this->actingAs($dokter_a);
        session(['idrole' => 2]);

        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', $rekam_medis_dokter_b->idrekam_medis),
            [
                'idkode_tindakan_terapi' => $therapy_code->idkode_tindakan_terapi,
                'detail' => 'Unauthorized detail'
            ]
        );

        // Expect: Access denied
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk menambah tindakan.');
        
        // Verify detail NOT created
        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $rekam_medis_dokter_b->idrekam_medis,
        ]);
    }

    /**
     * SCOPE 5 - SCENARIO 5: Perawat attempt add detail (no route)
     * ❌ NEGATIVE TEST: Route not found
     */
    public function test_perawat_gagal_tambah_detail_route_tidak_ada()
    {
        $perawat = self::perawat();
        $this->actingAs($perawat);
        session(['idrole' => 3]);

        // Try to POST to Dokter route (should fail - no route for Perawat)
        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', 1),
            ['idkode_tindakan_terapi' => 1, 'detail' => 'test']
        );

        // Expect: IsDokter middleware blocks (session idrole != 2)
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * SCOPE 5 - SCENARIO 6: Admin attempt add detail (no route)
     * ❌ NEGATIVE TEST: Route not found
     */
    public function test_admin_gagal_tambah_detail_route_tidak_ada()
    {
        $admin = self::admin();
        $this->actingAs($admin);
        session(['idrole' => 1]);

        // Try to POST to Dokter route
        $response = $this->post(
            route('Dokter.RekamMedis.store-detail', 1),
            ['idkode_tindakan_terapi' => 1, 'detail' => 'test']
        );

        // Expect: IsDokter middleware blocks
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * SCOPE 5 - SCENARIO 7: Dokter update own detail
     * ✅ POSITIVE TEST: Update therapy and description
     */
    public function test_dokter_update_detail_sendiri_successfully()
    {
        $setup = $this->createRekamMedisSetup();
        
        // First: Create a detail
        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['therapy_code']->idkode_tindakan_terapi,
            'detail' => 'Original detail text'
        ]);

        // Login & update
        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        // Create new therapy code for update
        $new_therapy = KodeTindakanTerapi::factory()->create();

        $response = $this->put(
            route('Dokter.RekamMedis.update-detail', $detail->iddetail_rekam_medis),
            [
                'idkode_tindakan_terapi' => $new_therapy->idkode_tindakan_terapi,
                'detail' => 'Updated detail text with new information'
            ]
        );

        // Expect: Success redirect
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify update in database
        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'idkode_tindakan_terapi' => $new_therapy->idkode_tindakan_terapi,
            'detail' => 'Updated detail text with new information'
        ]);
    }

    /**
     * SCOPE 5 - SCENARIO 8: Dokter update fail - invalid therapy code
     * ❌ NEGATIVE TEST: Validation error
     */
    public function test_dokter_gagal_update_detail_kode_invalid()
    {
        $setup = $this->createRekamMedisSetup();
        
        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['therapy_code']->idkode_tindakan_terapi,
            'detail' => 'Original detail'
        ]);

        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->put(
            route('Dokter.RekamMedis.update-detail', $detail->iddetail_rekam_medis),
            [
                'idkode_tindakan_terapi' => 99999,  // ← OVERRIDE: Invalid ID
                'detail' => 'Updated detail'
            ]
        );

        // Expect: Validation error
        $response->assertSessionHasErrors('idkode_tindakan_terapi');

        // Verify detail NOT updated
        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'detail' => 'Original detail'  // Should still be original
        ]);
    }

    /**
     * SCOPE 5 - SCENARIO 9: Dokter attempt update OTHER dokter's detail
     * ❌ NEGATIVE TEST: Access denied
     */
    public function test_dokter_gagal_update_detail_dokter_lain()
    {
        $dokter_a = self::dokter();
        $dokter_b = self::dokter();
        $dokter_b_role = RoleUser::where('iduser', $dokter_b->iduser)->where('idrole', 2)->first();

        // Create RekamMedis for dokter B with detail
        $ras_hewan = RasHewan::factory()->create();
        $pemilik = Pemilik::factory()->create();
        $pet = Pet::factory()->forPemilik($pemilik)->forRasHewan($ras_hewan)->create();
        
        $temu_dokter = TemuDokter::factory()
            ->forPet($pet)
            ->forRoleUser($dokter_b_role)
            ->create();

        $rekam_medis = RekamMedis::factory()
            ->forDokter($dokter_b_role)
            ->forTemuDokter($temu_dokter)
            ->create();

        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $rekam_medis->idrekam_medis,
            'idkode_tindakan_terapi' => KodeTindakanTerapi::factory()->create()->idkode_tindakan_terapi,
            'detail' => 'Dokter B detail'
        ]);

        // Dokter A tries to update dokter B's detail
        $this->actingAs($dokter_a);
        session(['idrole' => 2]);

        $response = $this->put(
            route('Dokter.RekamMedis.update-detail', $detail->iddetail_rekam_medis),
            [
                'idkode_tindakan_terapi' => KodeTindakanTerapi::factory()->create()->idkode_tindakan_terapi,
                'detail' => 'Hacked detail'
            ]
        );

        // Expect: Access denied
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengubah tindakan.');

        // Verify detail NOT updated
        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'detail' => 'Dokter B detail'  // Should still be original
        ]);
    }

    /**
     * SCOPE 5 - SCENARIO 10: Dokter delete own detail
     * ✅ POSITIVE TEST: Soft delete
     */
    public function test_dokter_hapus_detail_sendiri_successfully()
    {
        $setup = $this->createRekamMedisSetup();
        
        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $setup['rekam_medis']->idrekam_medis,
            'idkode_tindakan_terapi' => $setup['therapy_code']->idkode_tindakan_terapi,
            'detail' => 'Detail to delete'
        ]);

        $this->actingAs($setup['user_dokter']);
        session(['idrole' => 2]);

        $response = $this->delete(
            route('Dokter.RekamMedis.delete-detail', $detail->iddetail_rekam_medis)
        );

        // Expect: Success redirect
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify soft delete (deleted_at set, deleted_by set)
        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'deleted_by' => $setup['user_dokter']->iduser
        ]);

        // Verify deleted_at is not null (soft delete)
        $deleted_detail = DetailRekamMedis::where('iddetail_rekam_medis', $detail->iddetail_rekam_medis)->first();
        $this->assertNotNull($deleted_detail->deleted_at, 'Detail should be soft deleted (deleted_at should not be null)');
    }

    /**
     * SCOPE 5 - SCENARIO 11: Dokter attempt delete OTHER dokter's detail
     * ❌ NEGATIVE TEST: Access denied
     */
    public function test_dokter_gagal_hapus_detail_dokter_lain()
    {
        $dokter_a = self::dokter();
        $dokter_b = self::dokter();
        $dokter_b_role = RoleUser::where('iduser', $dokter_b->iduser)->where('idrole', 2)->first();

        // Create RekamMedis for dokter B with detail
        $ras_hewan = RasHewan::factory()->create();
        $pemilik = Pemilik::factory()->create();
        $pet = Pet::factory()->forPemilik($pemilik)->forRasHewan($ras_hewan)->create();
        
        $temu_dokter = TemuDokter::factory()
            ->forPet($pet)
            ->forRoleUser($dokter_b_role)
            ->create();

        $rekam_medis = RekamMedis::factory()
            ->forDokter($dokter_b_role)
            ->forTemuDokter($temu_dokter)
            ->create();

        $detail = DetailRekamMedis::create([
            'idrekam_medis' => $rekam_medis->idrekam_medis,
            'idkode_tindakan_terapi' => KodeTindakanTerapi::factory()->create()->idkode_tindakan_terapi,
            'detail' => 'Dokter B detail'
        ]);

        // Dokter A tries to delete dokter B's detail
        $this->actingAs($dokter_a);
        session(['idrole' => 2]);

        $response = $this->delete(
            route('Dokter.RekamMedis.delete-detail', $detail->iddetail_rekam_medis)
        );

        // Expect: Access denied
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk menghapus tindakan.');

        // Verify detail NOT deleted
        $this->assertDatabaseHas('detail_rekam_medis', [
            'iddetail_rekam_medis' => $detail->iddetail_rekam_medis,
            'deleted_at' => null  // Should NOT be deleted
        ]);
    }
}
