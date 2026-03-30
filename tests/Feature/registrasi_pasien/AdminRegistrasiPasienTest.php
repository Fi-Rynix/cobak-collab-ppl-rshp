<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Pemilik;
use App\Models\RasHewan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRegistrasiPasienTest extends TestCase
{
    use RefreshDatabase;
    
    // login sebagai admin (buat user & assign role langsung)
    private function actAsAdmin()
    {
        $admin = User::create([
            'nama' => 'Admin '.uniqid(),
            'email' => 'admin'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_user')->insert([
            'iduser' => $admin->iduser,
            'idrole' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin);
        $this->withSession(['idrole' => 1, 'iduser' => $admin->iduser]);
        return $admin;
    }

    // login as doctor (create user & assign role)
    private function actAsDoctor()
    {
        $dokter = User::create([
            'nama' => 'Dokter '.uniqid(),
            'email' => 'dokter'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_user')->insert([
            'iduser' => $dokter->iduser,
            'idrole' => 2,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($dokter);
        $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
        return $dokter;
    }

    // positive case: create owner
    public function test_admin_successfully_creates_owner()
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

    // negative case: doctor cannot create owner
    public function test_doctor_cannot_create_owner()
    {
        $this->actAsDoctor();

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

    // negative case: owner name exceeds max length
    public function test_owner_creation_fails_when_name_exceeds_max_length()
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

    // negative case: invalid email
    public function test_owner_creation_fails_with_invalid_email()
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

    // negative case: no_wa contains non-numeric chars
    public function test_owner_creation_fails_with_non_numeric_no_wa()
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

    // negative case: missing address
    public function test_owner_creation_fails_without_address()
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

    // positive case: create pet
    public function test_admin_successfully_creates_pet()
    {
        $this->actAsAdmin();

        $userPemilik = User::create([
            'nama' => 'Owner '.uniqid(),
            'email' => 'owner'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $userPemilik->iduser,
            'no_wa' => '081234567890',
            'alamat' => 'Addr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pemilik = DB::table('pemilik')->where('idpemilik', $idpemilik)->first();

        $idras = DB::table('ras_hewan')->insertGetId([
            'nama_ras' => 'Generic',
            'idjenis_hewan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rasHewan = DB::table('ras_hewan')->where('idras_hewan', $idras)->first();

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

    // negative case: pet creation without name
    public function test_pet_creation_fails_without_name()
    {
        $this->actAsAdmin();

        $userPemilik = User::create([
            'nama' => 'Owner '.uniqid(),
            'email' => 'owner'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $userPemilik->iduser,
            'no_wa' => '081234567890',
            'alamat' => 'Addr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pemilik = DB::table('pemilik')->where('idpemilik', $idpemilik)->first();

        $idras = DB::table('ras_hewan')->insertGetId([
            'nama_ras' => 'Generic',
            'idjenis_hewan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rasHewan = DB::table('ras_hewan')->where('idras_hewan', $idras)->first();

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

    // negative case: invalid pet gender
    public function test_pet_creation_fails_with_invalid_gender()
    {
        $this->actAsAdmin();

        $userPemilik = User::create([
            'nama' => 'Owner '.uniqid(),
            'email' => 'owner'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $userPemilik->iduser,
            'no_wa' => '081234567890',
            'alamat' => 'Addr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pemilik = DB::table('pemilik')->where('idpemilik', $idpemilik)->first();

        $idras = DB::table('ras_hewan')->insertGetId([
            'nama_ras' => 'Generic',
            'idjenis_hewan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rasHewan = DB::table('ras_hewan')->where('idras_hewan', $idras)->first();

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
