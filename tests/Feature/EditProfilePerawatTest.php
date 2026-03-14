<?php

namespace Tests\Feature;

use Tests\TestCase;

class EditProfilePerawatTest extends TestCase
{
    public function test_admin_update_perawat_berhasil()
    {
        ['perawat' => $perawat] = $this->buatPerawatDanLoginAdmin();

        $response = $this->put(
            route('Admin.Perawat.update-perawat', $perawat->idperawat),
            [
                'nama' => 'Perawat Baru',
                'pendidikan' => 'S1 Keperawatan'
            ]
        );

        $response->assertSessionHas('success');
    }

    public function test_update_perawat_gagal_jika_pendidikan_kosong()
    {
        ['perawat' => $perawat] = $this->buatPerawatDanLoginAdmin();

        $this->put(
            route('Admin.Perawat.update-perawat', $perawat->idperawat),
            [
                'pendidikan' => ''
            ]
        )->assertSessionHasErrors('pendidikan');
    }

    public function test_update_perawat_gagal_nomor_hp_terlalu_panjang()
    {
        ['perawat' => $perawat] = $this->buatPerawatDanLoginAdmin();

        $this->put(
            route('Admin.Perawat.update-perawat', $perawat->idperawat),
            [
                'no_hp' => '08123456789012345'
            ]
        )->assertSessionHasErrors('no_hp');
    }
}