<?php

namespace Tests\Feature;

use Tests\TestCase;

class TemuDokterTest extends TestCase
{
    public function test_admin_dapat_membuat_reservasi_dokter()
    {
        $response = $this->post(
            route('Admin.TemuDokter.store-temu-dokter'),
            [
                'dokter_id' => 1,
                'pet_id' => 1,
                'tanggal_reservasi' => '2026-06-01'
            ]
        );

        $response->assertStatus(302);
    }

    public function test_reservasi_gagal_jika_tanggal_kosong()
    {
        $response = $this->post(
            route('Admin.TemuDokter.store-temu-dokter'),
            [
                'dokter_id' => 1,
                'pet_id' => 1,
                'tanggal_reservasi' => ''
            ]
        );

        $response->assertSessionHasErrors('tanggal_reservasi');
    }

    public function test_admin_dapat_membatalkan_reservasi()
    {
        $response = $this->put(
            route('Admin.TemuDokter.cancel-temu-dokter', 1)
        );

        $response->assertStatus(302);
    }
}