<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Pemilik;
use App\Models\RasHewan;
use App\Models\User;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResepsionisRegistrasiPasienTest extends TestCase
{
    use RefreshDatabase;

    // login sebagai resepsionis
    private function actAsResepsionis()
    {
        $resepsionis = WithRole::resepsionis();
        $this->actingAs($resepsionis);
        $this->withSession(['idrole' => 4, 'iduser' => $resepsionis->iduser]);
        return $resepsionis;
    }

    // login sebagai admin
    private function actAsAdmin()
    {
        $admin = WithRole::admin();
        $this->actingAs($admin);
        $this->withSession(['idrole' => 1, 'iduser' => $admin->iduser]);
        return $admin;
    }

    // positif case resepsionis create pemilik
    public function test_resepsionis_berhasil_create_pemilik()
    {
        $this->actAsResepsionis();

        $pemilikData = [
            'nama' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'no_wa' => '089876543210',
            'alamat' => 'Jalan Sudirman No 456, Jakarta',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertRedirect(route('Resepsionis.Pemilik.daftar-pemilik'));
        $response->assertSessionHas('success', 'Data pemilik berhasil ditambahkan.');

        $this->assertDatabaseHas('user', [
            'nama' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
        ]);

        $user = User::where('email', 'budi.santoso@example.com')->first();
        $this->assertDatabaseHas('pemilik', [
            'iduser' => $user->iduser,
            'no_wa' => '089876543210',
            'alamat' => 'Jalan Sudirman No 456, Jakarta',
        ]);

        $this->assertDatabaseHas('role_user', [
            'iduser' => $user->iduser,
            'idrole' => 5,
            'status' => 1,
        ]);
    }

    // negatif case resepsionis create pemilik karena role bukan resepsionis
    public function test_admin_gagal_create_pemilik_dengan_route_resepsionis()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'no_wa' => '089876543210',
            'alamat' => 'Alamat admin test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    // negatif case create pemilik dengan nama melebihi max length
    public function test_gagal_resepsionis_create_pemilik_nama_melebihi_max_length()
    {
        $this->actAsResepsionis();

        $pemilikData = [
            'nama' => str_repeat('A', 256),
            'email' => 'test@example.com',
            'no_wa' => '089876543210',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseMissing('user', ['email' => 'test@example.com']);
    }

    // negatif case create pemilik dengan email tidak valid
    public function test_gagal_resepsionis_create_pemilik_email_invalid()
    {
        $this->actAsResepsionis();

        $pemilikData = [
            'nama' => 'Resepsionis Test',
            'email' => 'invalid-email-format',
            'no_wa' => '089876543210',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('email');
    }

    // positif case resepsionis create pet
    public function test_resepsionis_berhasil_create_pet()
    {
        $this->actAsResepsionis();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => 'Buddy',
            'tanggal_lahir' => '2021-05-20',
            'warna_tanda' => 'Coklat dan putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Resepsionis.Pet.store-pet'), $petData);

        $response->assertRedirect(route('Resepsionis.Pet.daftar-pet'));
        $response->assertSessionHas('success', 'Data pet berhasil ditambahkan.');

        $this->assertDatabaseHas('pet', [
            'nama' => 'Buddy',
            'tanggal_lahir' => '2021-05-20',
            'warna_tanda' => 'Coklat dan putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ]);
    }

    // negatif case resepsionis create pet tanpa nama
    public function test_gagal_resepsionis_create_pet_tanpa_nama()
    {
        $this->actAsResepsionis();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => '',
            'tanggal_lahir' => '2021-05-20',
            'warna_tanda' => 'Coklat',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Resepsionis.Pet.store-pet'), $petData);

        $response->assertSessionHasErrors('nama');
    }

    // negatif case resepsionis create pet dengan jenis_kelamin invalid
    public function test_gagal_resepsionis_create_pet_jenis_kelamin_invalid()
    {
        $this->actAsResepsionis();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => 'Buddy',
            'tanggal_lahir' => '2021-05-20',
            'warna_tanda' => 'Coklat',
            'jenis_kelamin' => 'M',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Resepsionis.Pet.store-pet'), $petData);

        $response->assertSessionHasErrors('jenis_kelamin');
    }
}
