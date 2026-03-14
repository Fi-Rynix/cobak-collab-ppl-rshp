<?php

namespace Tests\Feature;

use App\Models\RekamMedis;
use App\Models\TemuDokter;
use App\Models\User;
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

    // positif case admin create rekam medis
    public function test_admin_berhasil_create_rekam_medis()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lethargi',
            'temuan_klinis' => 'Suhu tubuh 39.5C, mata berair, debit urin menurun',
            'diagnosa' => 'Suspected viral infection dengan dehidrasi ringan',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Admin.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertRedirect(route('Admin.RekamMedis.daftar-rekam-medis'));
        $response->assertSessionHas('success', 'Rekam medis berhasil ditambahkan.');

        $this->assertDatabaseHas('rekam_medis', [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lethargi',
            'temuan_klinis' => 'Suhu tubuh 39.5C, mata berair, debit urin menurun',
            'diagnosa' => 'Suspected viral infection dengan dehidrasi ringan',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);
    }

    // negatif case admin create rekam medis dengan anamnesa kosong
    public function test_gagal_admin_create_rekam_medis_anamnesa_kosong()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => '',
            'temuan_klinis' => 'Suhu tubuh 39.5C, mata berair, debit urin menurun',
            'diagnosa' => 'Suspected viral infection dengan dehidrasi ringan',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Admin.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('anamnesa');
        $this->assertDatabaseMissing('rekam_medis', [
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);
    }

    // negatif case admin create rekam medis dengan temuan_klinis kosong
    public function test_gagal_admin_create_rekam_medis_temuan_klinis_kosong()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lethargi',
            'temuan_klinis' => '',
            'diagnosa' => 'Suspected viral infection dengan dehidrasi ringan',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Admin.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('temuan_klinis');
        $this->assertDatabaseMissing('rekam_medis', [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lethargi',
        ]);
    }

    // negatif case admin create rekam medis dengan diagnosa kosong
    public function test_gagal_admin_create_rekam_medis_diagnosa_kosong()
    {
        $this->actAsAdmin();

        $temuDokter = TemuDokter::factory()->create();

        $rekamMedisData = [
            'anamnesa' => 'Hewan menunjukkan gejala demam tinggi dan lethargi',
            'temuan_klinis' => 'Suhu tubuh 39.5C, mata berair, debit urin menurun',
            'diagnosa' => '',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post(route('Admin.RekamMedis.store-rekam-medis'), $rekamMedisData);

        $response->assertSessionHasErrors('diagnosa');
        $this->assertDatabaseMissing('rekam_medis', [
            'temuan_klinis' => 'Suhu tubuh 39.5C, mata berair, debit urin menurun',
        ]);
    }
}
