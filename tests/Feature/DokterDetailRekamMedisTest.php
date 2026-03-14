<?php

namespace Tests\Feature;

use App\Models\KodeTindakanTerapi;
use App\Models\RekamMedis;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterDetailRekamMedisTest extends TestCase
{
    use RefreshDatabase;

    // login sebagai dokter
    private function actAsDokter()
    {
        $dokter = WithRole::dokter();
        $this->actingAs($dokter);
        $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
        return $dokter;
    }

    // positif case dokter add detail rekam medis
    public function test_dokter_berhasil_add_detail_rekam_medis()
    {
        $this->actAsDokter();

        $rekamMedis = RekamMedis::factory()->create();
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $detailData = [
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ];

        $response = $this->post(route('Dokter.RekamMedis.store-detail', ['idrekam_medis' => $rekamMedis->idrekam_medis]), $detailData);

        $response->assertRedirect(route('Dokter.RekamMedis.daftar-rekam-medis'));
        $response->assertSessionHas('success', 'Detail rekam medis berhasil ditambahkan.');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ]);
    }

    // negatif case dokter add detail dengan text kosong
    public function test_gagal_dokter_add_detail_rekam_medis_text_kosong()
    {
        $this->actAsDokter();

        $rekamMedis = RekamMedis::factory()->create();
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $detailData = [
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => '',
        ];

        $response = $this->post(route('Dokter.RekamMedis.store-detail', ['idrekam_medis' => $rekamMedis->idrekam_medis]), $detailData);

        $response->assertSessionHasErrors('detail');
        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
        ]);
    }

    // negatif case dokter add detail dengan kode_tindakan_terapi invalid
    public function test_gagal_dokter_add_detail_rekam_medis_invalid_kode_tindakan()
    {
        $this->actAsDokter();

        $rekamMedis = RekamMedis::factory()->create();

        $detailData = [
            'idkode_tindakan_terapi' => 99999,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ];

        $response = $this->post(route('Dokter.RekamMedis.store-detail', ['idrekam_medis' => $rekamMedis->idrekam_medis]), $detailData);

        $response->assertSessionHasErrors('idkode_tindakan_terapi');
    }

    // negatif case dokter add detail ke rekam medis yang tidak ada
    public function test_gagal_dokter_add_detail_rekam_medis_tidak_ada()
    {
        $this->actAsDokter();

        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $detailData = [
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ];

        $response = $this->post(route('Dokter.RekamMedis.store-detail', ['idrekam_medis' => 99999]), $detailData);

        $response->assertSessionHasErrors('idrekam_medis');
    }
}
