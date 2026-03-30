<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResepsionisRegistrasiPasienTest extends TestCase
{
    use RefreshDatabase;

    private function actAsResepsionis()
    {
        $resepsionis = User::create([
            'nama' => 'Resepsionis '.uniqid(),
            'email' => 'resepsionis'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_user')->insert([
            'iduser' => $resepsionis->iduser,
            'idrole' => 4,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($resepsionis);
        $this->withSession(['idrole' => 4, 'iduser' => $resepsionis->iduser]);
        return $resepsionis;
    }

    public function test_resepsionis_can_create_owner()
    {
        $this->actAsResepsionis();

        $data = [
            'nama' => 'Owner Resepsionis',
            'email' => 'owner.resepsionis@example.test',
            'no_wa' => '081234567893',
            'alamat' => 'Alamat Resepsionis',
        ];

        $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('user', ['email' => 'owner.resepsionis@example.test']);
    }

    public function test_resepsionis_can_create_pet()
    {
        $this->actAsResepsionis();

        $userPemilik = User::create([
            'nama' => 'Owner '.uniqid(),
            'email' => 'owner'.uniqid().'@example.test',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $userPemilik->iduser,
            'no_wa' => '081234567894',
            'alamat' => 'Addr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idras = DB::table('ras_hewan')->insertGetId([
            'nama_ras' => 'Generic',
            'idjenis_hewan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $petData = [
            'nama' => 'Buddy',
            'tanggal_lahir' => '2021-01-01',
            'warna_tanda' => 'Black',
            'jenis_kelamin' => 'B',
            'idpemilik' => $idpemilik,
            'idras_hewan' => $idras,
        ];

        $response = $this->post(route('Resepsionis.Pet.store-pet'), $petData);

        $response->assertRedirect();
        $this->assertDatabaseHas('pet', ['nama' => 'Buddy']);
    }

    public function test_resepsionis_validation_rejects_invalid_owner()
    {
        $this->actAsResepsionis();

        $resp = $this->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => '',
            'email' => 'not-an-email',
            'no_wa' => 'abc',
            'alamat' => '',
        ]);

        $resp->assertSessionHasErrors(['nama','email','no_wa','alamat']);
    }
}
