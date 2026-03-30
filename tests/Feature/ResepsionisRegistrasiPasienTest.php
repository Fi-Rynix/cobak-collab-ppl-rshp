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

    // login sebagai perawat
    private function actAsPerawat()
    {
        $perawat = WithRole::perawat();
        $this->actingAs($perawat);
        $this->withSession(['idrole' => 3, 'iduser' => $perawat->iduser]);
        return $perawat;
    }

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
    public function test_resepsionis_berhasil_create_pemilik()
    {
        $this->actAsResepsionis();

        $pemilikData = [
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@example.com',
            'no_wa' => '082567891234',
            'alamat' => 'Jalan Ahmad Yani No 789, Surabaya',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertRedirect(route('Resepsionis.Pemilik.daftar-pemilik'));
        $response->assertSessionHas('success', 'Data pemilik berhasil ditambahkan.');

        $this->assertDatabaseHas('user', [
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@example.com',
        ]);

        $user = User::where('email', 'siti.nurhaliza@example.com')->first();
        $this->assertDatabaseHas('pemilik', [
            'iduser' => $user->iduser,
            'no_wa' => '082567891234',
            'alamat' => 'Jalan Ahmad Yani No 789, Surabaya',
        ]);

        $this->assertDatabaseHas('role_user', [
            'iduser' => $user->iduser,
            'idrole' => 5,
            'status' => 1,
        ]);
    }

    // negatif case create pemilik karena role selain resepsionis
    public function test_perawat_gagal_create_pemilik()
    {
        $this->actAsPerawat();

        $pemilikData = [
            'nama' => 'Perawat Test',
            'email' => 'perawat@example.com',
            'no_wa' => '082567891234',
            'alamat' => 'Alamat perawat test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    // negatif case create pemilik karena admin tidak bisa akses resepsionis route
    public function test_admin_gagal_create_pemilik()
    {
        $this->actAsAdmin();

        $pemilikData = [
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'no_wa' => '082567891234',
            'alamat' => 'Alamat admin test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    // negatif case create pemilik karena dokter tidak bisa akses resepsionis route
    public function test_dokter_gagal_create_pemilik()
    {
        $this->actAsDokter();

        $pemilikData = [
            'nama' => 'Dokter Test',
            'email' => 'dokter@example.com',
            'no_wa' => '082567891234',
            'alamat' => 'Alamat dokter test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    // negatif case create pemilik dengan nama kurang dari 3 karakter
    public function test_gagal_create_pemilik_nama_kurang_dari_3_karakter()
    {
        $this->actAsResepsionis();

        $pemilikData = [
            'nama' => 'AB',
            'email' => 'test@example.com',
            'no_wa' => '082567891234',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseMissing('user', ['email' => 'test@example.com']);
    }

    // negatif case create pemilik dengan email duplikat
    public function test_gagal_create_pemilik_email_duplikat()
    {
        $this->actAsResepsionis();

        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $pemilikData = [
            'nama' => 'Siti Test',
            'email' => 'existing@example.com',
            'no_wa' => '082567891234',
            'alamat' => 'Alamat test',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

        $response->assertSessionHasErrors('email');
    }

    // positif case create pet
    public function test_resepsionis_berhasil_create_pet()
    {
        $this->actAsResepsionis();

        $pemilik = Pemilik::factory()->create();
        $rasHewan = RasHewan::factory()->create();

        $petData = [
            'nama' => 'Charlie',
            'tanggal_lahir' => '2022-03-10',
            'warna_tanda' => 'Hitam dengan putih',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ];

        $response = $this->post(route('Resepsionis.Pet.store-pet'), $petData);

        $response->assertRedirect(route('Resepsionis.Pet.daftar-pet'));
        $response->assertSessionHas('success', 'Data pet berhasil ditambahkan.');

        $this->assertDatabaseHas('pet', [
            'nama' => 'Charlie',
            'tanggal_lahir' => '2022-03-10',
            'warna_tanda' => 'Hitam dengan putih',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasHewan->idras_hewan,
        ]);
    }
}
