<?php

namespace Tests\Feature;

use Tests\TestCase;

class EditProfileAdminTest extends TestCase
{

    public function test_admin_dapat_mengakses_dashboard()
    {
        $this->withoutMiddleware();

        $response = $this->get('/Admin/dashboard-admin');

        $response->assertStatus(200);
    }

    public function test_admin_update_profile_send_data()
    {
        $this->withoutMiddleware();

        $response = $this->put('/Admin/User/update-user/6', [
            'nama' => 'Admin Baru',
            'email' => 'adminbaru@test.com'
        ]);

        $response->assertStatus(302);
    }

    public function test_admin_update_profile_email_kosong()
    {
        $this->withoutMiddleware();

        $response = $this->put('/Admin/User/update-user/6', [
            'nama' => 'Admin Baru',
            'email' => ''
        ]);

        $response->assertStatus(302);
    }

}