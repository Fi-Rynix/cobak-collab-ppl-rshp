<?php

namespace Tests\Feature;

use Tests\TestCase;

class EditProfilePemilikTest extends TestCase
{
    public function test_admin_update_pemilik_sukses()
    {
        ['pemilik' => $pemilik] = $this->buatPemilikDanLoginAdmin();

        $response = $this->put(
            route('Admin.Pemilik.update-pemilik', $pemilik->idpemilik),
            [
                'nama' => 'Pemilik Baru',
                'no_wa' => '081234567890'
            ]
        );

        $response->assertSessionHas('success');
    }

    public function test_update_pemilik_gagal_no_wa_kosong()
    {
        ['pemilik' => $pemilik] = $this->buatPemilikDanLoginAdmin();

        $this->put(
            route('Admin.Pemilik.update-pemilik', $pemilik->idpemilik),
            [
                'no_wa' => ''
            ]
        )->assertSessionHasErrors('no_wa');
    }

    public function test_update_pemilik_gagal_nama_terlalu_panjang()
    {
        ['pemilik' => $pemilik] = $this->buatPemilikDanLoginAdmin();

        $this->put(
            route('Admin.Pemilik.update-pemilik', $pemilik->idpemilik),
            [
                'nama' => str_repeat('A', 260)
            ]
        )->assertSessionHasErrors('nama');
    }
}