<?php

namespace Tests\Feature;

use App\Models\DetailRekamMedis;
use App\Models\KodeTindakanTerapi;
use App\Models\RekamMedis;
use App\Models\TemuDokter;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRekamMedisDetailTest extends TestCase
{
    use RefreshDatabase;

    // login sebagai admin
    private function actAsAdmin()
    {
        $admin = WithRole::admin();
        $this->actingAs($admin);
        $this->withSession(['idrole' => 1, 'iduser' => $admin->iduser]);
        return $admin;
    }

    // positif case create rekam medis dan detail
    public function test_berhasil_create_rekam_medis_dan_detail()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create(['status' => 'W']);
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lesu',
            'temuan_klinis' => 'Suhu tubuh 39.5°C, mata merah, nafsu makan berkurang',
            'diagnosa' => 'Suspek demam berdarah akibat virus',
            'detail' => 'Injeksi cairan infus, pemberian vitamin, dan monitor ketat',
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Admin.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertRedirect(route('Admin.RekamMedis.daftar-rekam-medis'));
        $response->assertSessionHas('success', 'Rekam medis berhasil dibuat.');

        $this->assertDatabaseHas('rekam_medis', [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lesu',
            'temuan_klinis' => 'Suhu tubuh 39.5°C, mata merah, nafsu makan berkurang',
            'diagnosa' => 'Suspek demam berdarah akibat virus',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);

        $rekamMedis = RekamMedis::where('idreservasi_dokter', $temuDokter->idreservasi_dokter)->first();
        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi cairan infus, pemberian vitamin, dan monitor ketat',
        ]);

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
            'status' => 'D',
        ]);
    }

    // negatif case create rekam medis dengan anamnesa kosong
    public function test_gagal_create_rekam_medis_dan_detail_anamnesa_kosong()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create(['status' => 'W']);
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $rekamMedisData = [
            'anamnesa' => '',
            'temuan_klinis' => 'Suhu tubuh 39.5°C, mata merah, nafsu makan berkurang',
            'diagnosa' => 'Suspek demam berdarah akibat virus',
            'detail' => 'Injeksi cairan infus, pemberian vitamin, dan monitor ketat',
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Admin.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('anamnesa');
        $this->assertDatabaseMissing('rekam_medis', ['idreservasi_dokter' => $temuDokter->idreservasi_dokter]);
    }

    // positif case edit rekam medis dan detail
    public function test_berhasil_edit_rekam_medis_dan_detail()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create(['status' => 'D']);
        $kodeTindakan1 = KodeTindakanTerapi::factory()->create();
        $kodeTindakan2 = KodeTindakanTerapi::factory()->create();

        $rekamMedis = RekamMedis::factory()->create([
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
            'anamnesa' => 'Anamnesa lama',
            'temuan_klinis' => 'Temuan lama',
            'diagnosa' => 'Diagnosa lama',
        ]);

        DetailRekamMedis::factory()->create([
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $kodeTindakan1->idkode_tindakan_terapi,
            'detail' => 'Detail lama',
        ]);

        $updateData = [
            'anamnesa' => 'Anamnesa baru - hewan menunjukkan perbaikan',
            'temuan_klinis' => 'Temuan baru - suhu normal, mata cerah',
            'diagnosa' => 'Diagnosa baru - pemulihan baik',
            'detail' => 'Detail baru - lanjutkan antibiotik',
            'idkode_tindakan_terapi' => $kodeTindakan2->idkode_tindakan_terapi,
        ];

        $response = $this->put(route('Admin.RekamMedis.update-rekam-medis', $rekamMedis->idrekam_medis), $updateData);

        $response->assertRedirect(route('Admin.RekamMedis.daftar-rekam-medis'));
        $response->assertSessionHas('success', 'Rekam medis berhasil diupdate.');

        $this->assertDatabaseHas('rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'anamnesa' => 'Anamnesa baru - hewan menunjukkan perbaikan',
            'temuan_klinis' => 'Temuan baru - suhu normal, mata cerah',
            'diagnosa' => 'Diagnosa baru - pemulihan baik',
        ]);

        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $kodeTindakan2->idkode_tindakan_terapi,
            'detail' => 'Detail baru - lanjutkan antibiotik',
        ]);
    }

    // negatif case edit rekam medis dengan diagnosa kosong
    public function test_gagal_edit_rekam_medis_dan_detail_diagnosa_kosong()
    {
        $this->actAsAdmin();

        $rekamMedis = RekamMedis::factory()->create();
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $updateData = [
            'anamnesa' => 'Anamnesa update',
            'temuan_klinis' => 'Temuan update',
            'diagnosa' => '',
            'detail' => 'Detail update',
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
        ];

        $response = $this->put(route('Admin.RekamMedis.update-rekam-medis', $rekamMedis->idrekam_medis), $updateData);

        $response->assertSessionHasErrors('diagnosa');
    }

    // positif case delete rekam medis dan detail
    public function test_berhasil_delete_rekam_medis_dan_detail()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create(['status' => 'D']);
        $rekamMedis = RekamMedis::factory()->create(['idreservasi_dokter' => $temuDokter->idreservasi_dokter]);
        DetailRekamMedis::factory()->create(['idrekam_medis' => $rekamMedis->idrekam_medis]);

        $response = $this->delete(route('Admin.RekamMedis.delete-rekam-medis', $rekamMedis->idrekam_medis));

        $response->assertRedirect(route('Admin.RekamMedis.daftar-rekam-medis'));
        $response->assertSessionHas('success', 'Rekam medis berhasil dihapus.');

        $this->assertSoftDeleted('rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
        ]);

        $this->assertSoftDeleted('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
        ]);

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
            'status' => 'W',
        ]);
    }

    // negatif case delete rekam medis tidak ditemukan
    public function test_gagal_delete_rekam_medis_dan_detail()
    {
        $this->actAsAdmin();

        $response = $this->delete(route('Admin.RekamMedis.delete-rekam-medis', 999));

        $response->assertStatus(404);
    }
}

