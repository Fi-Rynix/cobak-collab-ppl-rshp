<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Pemilik;
use App\Models\RasHewan;
use App\Models\User;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRegistrasiPasienTest extends TestCase
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

    // login sebagai dokter
    private function actAsDokter()
    {
        $dokter = WithRole::dokter();
        $this->actingAs($dokter);
        $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
        return $dokter;
    }

    // positif case create pemilik
    public function test_admin_berhasil_create_pemilik()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => 'John Doe',
            'email' => 'john.doe@example.com',
            'no_wa' => '081234567890',
            'alamat' => 'Jalan Merdeka No 123, Bandung',
        ];

        $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

        $response->assertRedirect(route('Admin.Pemilik.daftar-pemilik'));
        $response->assertSessionHas('success', 'Data pemilik berhasil ditambahkan.');

        $this->assertDatabaseHas('user', [
            'nama' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertDatabaseHas('pemilik', [
            'iduser' => $user->iduser,
            'no_wa' => '081234567890',
            'alamat' => 'Jalan Merdeka No 123, Bandung',
        ]);

        $this->assertDatabaseHas('role_user', [
            'iduser' => $user->iduser,
            'idrole' => 5,
            'status' => 1,
        ]);
    }

    // negatif case create pemilik karena role selain admin
    public function test_dokter_gagal_create_pemilik()
    {
        $this->actAsDokter();

        $pemilikData = [
            'nama' => 'Dokter Test',
            'email' => 'dokter@example.com',
            'no_wa' => '081234567890',
            'alamat' => 'Alamat dokter test',
        ];

        $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    // negatif case create pemilik dengan panjang nama lebih dari 255 karakter
    public function test_gagal_create_pemilik_nama_melebihi_max_length()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => str_repeat('A', 256),
            'email' => 'test@example.com',
            'no_wa' => '081234567890',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseMissing('user', ['email' => 'test@example.com']);
    }

    // negatif case create pemilik dengan email tidak valid tanpa @
    public function test_gagal_create_pemilik_email_invalid()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => 'John Test',
            'email' => 'invalid-email',
            'no_wa' => '081234567890',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('email');
    }

    // negatif case create pemilik dengan no_wa selain angka
    public function test_gagal_create_pemilik_no_wa_huruf()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => 'John Test',
            'email' => 'john@example.com',
            'no_wa' => '0812abc567890',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('no_wa');
    }

    // negatif case create pemilik tanpa menginputkan alamat
    public function test_gagal_create_pemilik_tanpa_alamat()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => 'John Test',
            'email' => 'john@example.com',
            'no_wa' => '081234567890',
            'alamat' => '',
        ];

        $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('alamat');
    }

    // positif case create pet
    public function test_admin_berhasil_create_pet()
    {
        $this->actAsAdmin();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => 'Fluffy',
            'tanggal_lahir' => '2020-01-15',
            'warna_tanda' => 'Putih dengan bintik hitam',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Admin.Pet.store-pet'), $petData);

        $response->assertRedirect(route('Admin.Pet.daftar-pet'));
        $response->assertSessionHas('success', 'Data pet berhasil ditambahkan.');

        $this->assertDatabaseHas('pet', [
            'nama' => 'Fluffy',
            'tanggal_lahir' => '2020-01-15',
            'warna_tanda' => 'Putih dengan bintik hitam',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ]);
    }

    // negatif case create pet tanpa nama
    public function test_gagal_create_pet_tanpa_nama()
    {
        $this->actAsAdmin();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => '',
            'tanggal_lahir' => '2020-01-15',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Admin.Pet.store-pet'), $petData);

        $response->assertSessionHasErrors('nama');
    }

    // negatif case create pet dengan jenis_kelamin selain J atau B
    public function test_gagal_create_pet_jenis_kelamin_selain_j_atau_b()
    {
        $this->actAsAdmin();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => 'Fluffy',
            'tanggal_lahir' => '2020-01-15',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'X',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Admin.Pet.store-pet'), $petData);

        $response->assertSessionHasErrors('jenis_kelamin');
    }
}
