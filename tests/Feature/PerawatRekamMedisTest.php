<?php

namespace Tests\Feature;

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

    // positif case perawat create rekam medis
    public function test_perawat_berhasil_create_rekam_medis()
    {
        $this->actAsPerawat();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Pemilik melaporkan hewan mengalami diare selama 3 hari',
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
            'diagnosa' => 'Gastroenteritis bacterial suspect',
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
    }

    // negatif case perawat create rekam medis dengan anamnesa kosong
    public function test_gagal_perawat_create_rekam_medis_anamnesa_kosong()
    {
        $this->actAsPerawat();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => '',
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
            'diagnosa' => 'Gastroenteritis bacterial suspect',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('anamnesa');
        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);
    }

    // negatif case perawat create rekam medis dengan temuan_klinis kosong
    public function test_gagal_perawat_create_rekam_medis_temuan_klinis_kosong()
    {
        $this->actAsPerawat();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Pemilik melaporkan hewan mengalami diare selama 3 hari',
            'temuan_klinis' => '',
            'diagnosa' => 'Gastroenteritis bacterial suspect',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('temuan_klinis');
        $this->assertDatabaseMissing('rekam_medis', [
            'anamnesa' => 'Pemilik melaporkan hewan mengalami diare selama 3 hari',
        ]);
    }

    // negatif case perawat create rekam medis dengan diagnosa kosong
    public function test_gagal_perawat_create_rekam_medis_diagnosa_kosong()
    {
        $this->actAsPerawat();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Pemilik melaporkan hewan mengalami diare selama 3 hari',
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
            'diagnosa' => '',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Perawat.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('diagnosa');
        $this->assertDatabaseMissing('rekam_medis', [
            'temuan_klinis' => 'Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan',
        ]);
    }
}
