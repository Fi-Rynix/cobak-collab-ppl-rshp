<?php

namespace Tests\Feature;

use App\Models\KodeTindakanTerapi;
use App\Models\DetailRekamMedis;
use App\Models\RekamMedis;
use App\Models\TemuDokter;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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

    // login sebagai perawat
    private function actAsPerawat()
    {
        $perawat = WithRole::perawat();
        $this->actingAs($perawat);
        $this->withSession(['idrole' => 3, 'iduser' => $perawat->iduser]);
        return $perawat;
    }

    // helper untuk mendapatkan idrole_user dokter
    private function getDokterIdroleUser($dokter)
    {
        return DB::table('role_user')
            ->where('iduser', '=', $dokter->iduser)
            ->where('idrole', '=', 2)
            ->value('idrole_user');
    }

    // positif case dokter berhasil tambah detail rekam medis
    public function test_dokter_berhasil_tambah_detail_rekam_medis()
    {
        $dokter = $this->actAsDokter();
        $dokterIdroleUser = $this->getDokterIdroleUser($dokter);

        $temuDokter = TemuDokter::factory()->create();
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        // Create rekam medis with current dokter as dokter_pemeriksa
        $rekamMedis = RekamMedis::factory()->create([
            'dokter_pemeriksa' => $dokterIdroleUser,
        ]);

        $detailData = [
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ];

        $response = $this->post(route('Dokter.RekamMedis.store-detail', ['idrekam_medis' => $rekamMedis->idrekam_medis]), $detailData);

        $response->assertRedirect(route('Dokter.RekamMedis.detail-rekam-medis', $rekamMedis->idrekam_medis));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ]);
    }

    // negatif case dokter gagal menambah rekam medis
    public function test_dokter_gagal_menambah_rekam_medis()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->actAsDokter();

        $temuDokter = TemuDokter::factory()->create();
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Pemilik melaporkan hewan mengalami diare selama 3 hari',
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
            'diagnosa' => 'Gastroenteritis bacterial suspect',
            'detail' => 'Pemberian antibiotik spektrum luas dan terapi cairan',
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $this->post(route('Dokter.RekamMedis.store-rekam-medis'), $rekamMedisData);
    }

    // negatif case dokter gagal menambah detail rekam medis yang bukan atas nama dirinya
    public function test_dokter_gagal_menambah_detail_rekam_medis_bukan_atas_nama_dirinya()
    {
        $currentDokter = $this->actAsDokter();
        $otherDokter = WithRole::dokter();

        $otherDokterIdroleUser = $this->getDokterIdroleUser($otherDokter);

        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        // Create rekam medis with other dokter as dokter_pemeriksa
        $rekamMedis = RekamMedis::factory()->create([
            'dokter_pemeriksa' => $otherDokterIdroleUser,
        ]);

        $detailData = [
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Injeksi antibiotik diberikan intramuscular',
        ];

        $response = $this->post(route('Dokter.RekamMedis.store-detail', ['idrekam_medis' => $rekamMedis->idrekam_medis]), $detailData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk menambah tindakan.');

        $this->assertDatabaseMissing('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
        ]);
    }

    // negatif case create detail rekam medis dengan detail kosong
    public function test_create_detail_rekam_medis_detail_kosong()
    {
        $dokter = $this->actAsDokter();
        $dokterIdroleUser = $this->getDokterIdroleUser($dokter);

        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $rekamMedis = RekamMedis::factory()->create([
            'dokter_pemeriksa' => $dokterIdroleUser,
        ]);

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
}
