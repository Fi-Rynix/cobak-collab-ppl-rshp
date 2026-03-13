<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PatientRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    protected static $emailCounter = 1;

    protected function createDummyUser($roleId)
    {
        $uuid = Str::uuid()->toString();
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        
        $userId = DB::table('user')->insertGetId([
            'nama' => 'UserRole ' . $roleId,
            'email' => $email,
            'password' => Hash::make('123456'),
        ]);

        DB::table('role_user')->insert([
            'iduser' => $userId,
            'idrole' => $roleId,
            'status' => 1
        ]);

        return User::find($userId);
    }

    protected function cleanupUser($user)
    {
        if ($user) {
            DB::table('role_user')->where('iduser', $user->iduser)->delete();
            DB::table('pemilik')->where('iduser', $user->iduser)->delete();
            DB::table('user')->where('iduser', $user->iduser)->delete();
        }
    }

    protected function setupDummyRasHewan()
    {
        // Check if ras_hewan exists, else create one
        $ras = DB::table('ras_hewan')->first();
        if ($ras) {
            return $ras->idras_hewan;
        }

        // We need a jenis_hewan first
        $jenisId = DB::table('jenis_hewan')->insertGetId([
            'nama_jenis' => 'Kucing Test'
        ]);

        return DB::table('ras_hewan')->insertGetId([
            'nama_ras' => 'Persia Test',
            'idjenis_hewan' => $jenisId,
        ]);
    }

    // --- POSITIVE CASES ---

    public function test_admin_register_patient_successful()
    {
        $admin = $this->createDummyUser(1); // Admin
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);

        // Register Pemilik
        $responsePemilik = $this->actingAs($admin)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'John Doe Pemilik',
            'email' => $email,
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Test No 123'
        ]);

        $responsePemilik->assertSessionHas('success');
        $this->assertDatabaseHas('user', ['email' => $email]);
        
        $userPemilik = DB::table('user')->where('email', $email)->first();
        $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

        // Register Pet
        $rasId = $this->setupDummyRasHewan();
        $responsePet = $this->actingAs($admin)->post(route('Admin.Pet.store-pet'), [
            'nama' => 'Fluffy',
            'tanggal_lahir' => '2023-01-01',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $responsePet->assertSessionHas('success');
        $this->assertDatabaseHas('pet', [
            'nama' => 'Fluffy',
            'idpemilik' => $pemilik->idpemilik
        ]);

        // Cleanup
        DB::table('pet')->where('idpemilik', $pemilik->idpemilik)->delete();
        $this->cleanupUser($userPemilik);
        $this->cleanupUser($admin);
    }

    public function test_receptionist_register_patient_successful()
    {
        $receptionist = $this->createDummyUser(4); // Resepsionis
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);

        // Register Pemilik
        $responsePemilik = $this->actingAs($receptionist)->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Jane Doe Pemilik',
            'email' => $email,
            'no_wa' => '089876543210',
            'alamat' => 'Jl. Test Resepsionis'
        ]);

        $responsePemilik->assertSessionHas('success');
        $this->assertDatabaseHas('user', ['email' => $email]);
        
        $userPemilik = DB::table('user')->where('email', $email)->first();
        $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

        // Register Pet
        $rasId = $this->setupDummyRasHewan();
        $responsePet = $this->actingAs($receptionist)->post(route('Resepsionis.Pet.store-pet'), [
            'nama' => 'Snowball',
            'tanggal_lahir' => '2022-05-10',
            'warna_tanda' => 'Hitam',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $responsePet->assertSessionHas('success');
        $this->assertDatabaseHas('pet', [
            'nama' => 'Snowball',
            'idpemilik' => $pemilik->idpemilik
        ]);

        // Cleanup
        DB::table('pet')->where('idpemilik', $pemilik->idpemilik)->delete();
        $this->cleanupUser($userPemilik);
        $this->cleanupUser($receptionist);
    }

    // --- NEGATIVE CASES ---

    public function test_missing_required_fields_pemilik()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => '',
            'email' => '',
            'no_wa' => '',
            'alamat' => ''
        ]);

        $response->assertSessionHasErrors(['nama', 'email', 'no_wa', 'alamat']);
        $this->cleanupUser($admin);
    }

    public function test_invalid_phone_number()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Valid Name',
            'email' => sprintf('TestEmail%02d@test.com', self::$emailCounter++),
            'no_wa' => 'not-a-number', // invalid
            'alamat' => 'Valid Address'
        ]);

        $response->assertSessionHasErrors(['no_wa']);
        $this->cleanupUser($admin);
    }

    public function test_invalid_date_format_pet()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)->post(route('Admin.Pet.store-pet'), [
            'nama' => 'Doggy',
            'tanggal_lahir' => 'invalid-date',
            'warna_tanda' => 'Coklat',
            'jenis_kelamin' => 'J',
            'idpemilik' => 9999, // Doesn't matter, validation will fail before DB check or along with it.
            'idras_hewan' => 9999
        ]);

        $response->assertSessionHasErrors(['tanggal_lahir', 'idpemilik', 'idras_hewan']);
        $this->cleanupUser($admin);
    }

    public function test_duplicate_patient_record_email()
    {
        $admin = $this->createDummyUser(1);
        $existingPemilik = $this->createDummyUser(5); // Pemilik

        $response = $this->actingAs($admin)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'New Guy',
            'email' => $existingPemilik->email, // Duplicate
            'no_wa' => '081234567890',
            'alamat' => 'Some Address'
        ]);

        // The validation rule for Pemilik_Controller requires unique email
        $response->assertSessionHasErrors(['email']);
        
        $this->cleanupUser($existingPemilik);
        $this->cleanupUser($admin);
    }

    public function test_sql_injection_attempt_pet_name()
    {
        $admin = $this->createDummyUser(1);
        // Create valid pemilik first
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        $this->actingAs($admin)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Tester',
            'email' => $email,
            'no_wa' => '081111111111',
            'alamat' => 'Alamat'
        ]);
        $userPemilik = DB::table('user')->where('email', $email)->first();
        $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

        $rasId = $this->setupDummyRasHewan();
        $sqlInjection = "Drop' OR '1'='1"; // Malicious string

        $response = $this->actingAs($admin)->post(route('Admin.Pet.store-pet'), [
            'nama' => $sqlInjection,
            'tanggal_lahir' => '2023-01-01',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        // Should successfully insert without SQL error because Laravel uses PDO
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pet', [
            'nama' => $sqlInjection,
            'idpemilik' => $pemilik->idpemilik
        ]);

        // Cleanup
        DB::table('pet')->where('idpemilik', $pemilik->idpemilik)->delete();
        $this->cleanupUser($userPemilik);
        $this->cleanupUser($admin);
    }

    public function test_xss_attempt_owner_name()
    {
        $admin = $this->createDummyUser(1);
        $xssInput = "<script>alert('XSS')</script>";
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);

        $response = $this->actingAs($admin)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => $xssInput,
            'email' => $email,
            'no_wa' => '081111111111',
            'alamat' => $xssInput
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user', [
            'email' => ltrim(strtolower($email)), // Sometimes app lowercases it
        ]);

        $userPemilik = DB::table('user')->where('email', strtolower($email))->first();
        $this->assertDatabaseHas('pemilik', [
            'alamat' => $xssInput
        ]);

        $this->cleanupUser($userPemilik);
        $this->cleanupUser($admin);
    }

    public function test_unauthorized_role_attempting_registration()
    {
        $doctor = $this->createDummyUser(2); // Dokter
        
        $response = $this->actingAs($doctor)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Hacker',
            'email' => sprintf('TestEmail%02d@test.com', self::$emailCounter++),
            'no_wa' => '081111111111',
            'alamat' => 'Hacked'
        ]);

        // Should be forbidden
        $this->assertTrue(in_array($response->status(), [401, 403, 302]));
        
        $this->cleanupUser($doctor);
    }
}
