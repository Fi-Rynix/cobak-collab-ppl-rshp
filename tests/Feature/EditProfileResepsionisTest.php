<?php

namespace Tests\Feature;

use Tests\TestCase;

class EditProfileResepsionisTest extends TestCase
{

    public function test_resepsionis_bisa_melihat_dashboard()
    {
        $this->withoutMiddleware();

        $response = $this->get('/Resepsionis/dashboard-resepsionis');

        $response->assertStatus(200);
    }

    public function test_resepsionis_menyimpan_data_pemilik()
    {
        $this->withoutMiddleware();

        $response = $this->post('/Resepsionis/Pemilik/store-pemilik', [
            'alamat' => 'Alamat Baru',
            'no_wa' => '08123456789'
        ]);

        $response->assertStatus(302);
    }

    public function test_resepsionis_gagal_simpan_data_pemilik()
    {
        $this->withoutMiddleware();

        $response = $this->post('/Resepsionis/Pemilik/store-pemilik', [
            'no_wa' => ''
        ]);

        $response->assertStatus(302);
    }

}