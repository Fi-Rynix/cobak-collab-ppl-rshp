<?php

namespace Tests\Feature;

use App\Models\KodeTindakanTerapi;
use App\Models\DetailRekamMedis;
use App\Models\RekamMedis;
use App\Models\TemuDokter;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerawatRekamMedisTest extends TestCase
{
    use RefreshDatabase;

    // login sebagai perawat
    private function actAsPerawat()
    {
        $perawat = WithRole::perawat();
        $this->actingAs($perawat);
        $this->withSession(['idrole' => 3, 'iduser' => $perawat->iduser]);
        return $perawat;
    }

    // login sebagai dokter
    private function actAsDokter()
    {
        $dokter = WithRole::dokter();
        $this->actingAs($dokter);
        $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
        return $dokter;
    }

    // login sebagai resepsionis
    private function actAsResepsionis()
    {
        $resepsionis = WithRole::resepsionis();
        $this->actingAs($resepsionis);
        $this->withSession(['idrole' => 4, 'iduser' => $resepsionis->iduser]);
        return $resepsionis;
    }

    // positif case perawat create rekam medis
    public function test_perawat_berhasil_create_rekam_medis()
    {
        $this->actAsPerawat();

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

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertRedirect(route('Perawat.RekamMedis.daftar-rekam-medis'));
        $response->assertSessionHas('success', 'Rekam medis berhasil ditambahkan.');

        $this->assertDatabaseHas('rekam_medis', [
            'anamnesa' => 'Pemilik melaporkan hewan mengalami diare selama 3 hari',
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
            'diagnosa' => 'Gastroenteritis bacterial suspect',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);

        $rekamMedis = RekamMedis::where('idreservasi_dokter', $temuDokter->idreservasi_dokter)->first();
        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'detail' => 'Pemberian antibiotik spektrum luas dan terapi cairan',
        ]);
    }

    // negatif case dokter gagal create rekam medis
    public function test_dokter_gagal_create_rekam_medis()
    {
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

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertStatus(403);
    }

    // negatif case resepsionis gagal create rekam medis
    public function test_resepsionis_gagal_create_rekam_medis()
    {
        $this->actAsResepsionis();

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

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertStatus(403);
    }

    // negatif case create rekam medis dengan anamnesa kosong
    public function test_create_rekam_medis_anamnesa_kosong()
    {
        $this->actAsPerawat();

        $temuDokter = TemuDokter::factory()->create();
        $kodeTindakan = KodeTindakanTerapi::factory()->create();

        $rekamMedisData = [
            'anamnesa' => '',
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
            'diagnosa' => 'Gastroenteritis bacterial suspect',
            'detail' => 'Pemberian antibiotik spektrum luas dan terapi cairan',
            'idkode_tindakan_terapi' => $kodeTindakan->idkode_tindakan_terapi,
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('anamnesa');
        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);
    }
}
