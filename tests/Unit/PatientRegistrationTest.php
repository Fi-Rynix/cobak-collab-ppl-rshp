<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

    protected function createDummyPemilikOnly()
    {
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        $iduser = DB::table('user')->insertGetId([
            'nama' => 'Dummy Pemilik',
            'email' => $email,
            'password' => Hash::make('123456')
        ]);
        DB::table('role_user')->insert(['iduser' => $iduser, 'idrole' => 5, 'status' => 1]);
        $idpemilik = DB::table('pemilik')->insertGetId([
            'iduser' => $iduser,
            'no_wa' => '080000000000',
            'alamat' => 'Dummy Alamat'
        ]);
        return (object)['iduser' => $iduser, 'idpemilik' => $idpemilik, 'email' => $email];
    }

    protected function createDummyPetOnly($idpemilik, $idras_hewan)
    {
        $idpet = DB::table('pet')->insertGetId([
            'nama' => 'Dummy Pet',
            'tanggal_lahir' => '2023-01-01',
            'warna_tanda' => 'Dummy Warna',
            'jenis_kelamin' => 'J',
            'idpemilik' => $idpemilik,
            'idras_hewan' => $idras_hewan
        ]);
        return (object)['idpet' => $idpet];
    }

    // --- POSITIVE CASES ---

    public function test_admin_register_patient()
    {
        $admin = $this->createDummyUser(1); // Admin
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);

        // Register Pemilik
        $responsePemilik = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Jod',
            'email' => $email,
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Test No 123'
        ]);

        $this->assertDatabaseHas('user', ['email' => $email]);
        
        $userPemilik = DB::table('user')->where('email', $email)->first();
        $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

        // Register Pet
        $rasId = $this->setupDummyRasHewan();
        $responsePet = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pet.store-pet'), [
            'nama' => 'Flu',
            'tanggal_lahir' => '2023-01-01',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $this->assertDatabaseHas('pet', [
            'nama' => 'Flu',
            'idpemilik' => $pemilik->idpemilik
        ]);

        // Cleanup
        DB::table('pet')->where('idpemilik', $pemilik->idpemilik)->delete();
        $this->cleanupUser($userPemilik);
        $this->cleanupUser($admin);
    }

    public function test_receptionist_register_patient()
    {
        $receptionist = $this->createDummyUser(3); // Resepsionis
        Auth::login($receptionist);
        $email = sprintf('pemilik.baru%04d@gmail.com', rand(1000, 9999));

        // Register Pemilik dengan data normal
        $responsePemilik = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Budi Santoso',
            'email' => $email,
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Merpati No. 12, Surabaya'
        ]);

        $this->assertDatabaseHas('user', ['email' => $email]);
        
        $userPemilik = DB::table('user')->where('email', $email)->first();
        $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

        // Register Pet dengan data normal
        $rasId = $this->setupDummyRasHewan();
        $responsePet = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pet.store-pet'), [
            'nama' => 'Mochi',
            'tanggal_lahir' => '2022-05-10',
            'warna_tanda' => 'Putih Coklat',
            'jenis_kelamin' => 'B',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $this->assertDatabaseHas('pet', [
            'nama' => 'Mochi',
            'idpemilik' => $pemilik->idpemilik
        ]);

        // Cleanup
        DB::table('pet')->where('idpemilik', $pemilik->idpemilik)->delete();
        $this->cleanupUser($userPemilik);
        $this->cleanupUser($receptionist);
    }

    // --- NEGATIVE CASES (RESEPSIONIS) ---

    /** semua field kosong harus ditolak validasi */
    public function test_resepsionis_missing_required_fields_pemilik()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => '',
            'email' => '',
            'no_wa' => '',
            'alamat' => ''
        ]);

        $response->assertSessionHasErrors(['nama', 'email', 'no_wa', 'alamat']);
        $this->cleanupUser($receptionist);
    }

    /** nama terlalu pendek (min:3) */
    public function test_resepsionis_nama_terlalu_pendek()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Ab',  // kurang dari 3 karakter
            'email' => sprintf('pendek%04d@gmail.com', rand(1000, 9999)),
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Kenanga No. 5'
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->cleanupUser($receptionist);
    }

    /** nomor WA bukan angka */
    public function test_resepsionis_invalid_phone_number()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Citra Dewi',
            'email' => sprintf('citra%04d@gmail.com', rand(1000, 9999)),
            'no_wa' => 'bukan-angka',  // string bukan angka
            'alamat' => 'Jl. Melati No. 8'
        ]);

        $response->assertSessionHasErrors(['no_wa']);
        $this->cleanupUser($receptionist);
    }

    /** nomor WA pendek dengan 3 digit saja) */
    public function test_resepsionis_phone_too_short()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Dewi Sari',
            'email' => sprintf('dewi%04d@gmail.com', rand(1000, 9999)),
            'no_wa' => '08123',  // kurang dari 10 digit
            'alamat' => 'Jl. Anggrek No. 3'
        ]);

        $response->assertSessionHasErrors(['no_wa']);
        $this->cleanupUser($receptionist);
    }

    /** alamat terlalu pendek (min:5) */
    public function test_resepsionis_alamat_terlalu_pendek()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Eko Prasetyo',
            'email' => sprintf('eko%04d@gmail.com', rand(1000, 9999)),
            'no_wa' => '081234567890',
            'alamat' => 'Jl'  // kurang dari 5 karakter
        ]);

        $response->assertSessionHasErrors(['alamat']);
        $this->cleanupUser($receptionist);
    }

    /** email duplikat harusnya sih kena tolak yahh kayak kisah cinta gwe */
    public function test_resepsionis_duplicate_email()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);
        $existingUser = $this->createDummyUser(5); // Pemilik yang sudah ada

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Faisal Akbar',
            'email' => $existingUser->email,  // email duplikat
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Dahlia No. 15'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->cleanupUser($existingUser);
        $this->cleanupUser($receptionist);
    }

    /** format email tidak valid */
    public function test_resepsionis_invalid_email_format()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => 'Gita Nirmala',
            'email' => 'ini-bukan-email',  // format salah
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Flamboyan No. 7'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->cleanupUser($receptionist);
    }

    /** tanggal lahir pet tidak valid */
    public function test_resepsionis_invalid_pet_date()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pet.store-pet'), [
            'nama' => 'Kitty',
            'tanggal_lahir' => 'bukan-tanggal',  // format tanggal salah
            'warna_tanda' => 'Hitam',
            'jenis_kelamin' => 'B',
            'idpemilik' => 9999,
            'idras_hewan' => 9999
        ]);

        $response->assertSessionHasErrors(['tanggal_lahir', 'idpemilik', 'idras_hewan']);
        $this->cleanupUser($receptionist);
    }

    /** jenis kelamin pet tidak valid (harus J atau B) */
    public function test_resepsionis_invalid_pet_gender()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);
        $dummyPemilik = $this->createDummyPemilikOnly();
        $rasId = $this->setupDummyRasHewan();

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pet.store-pet'), [
            'nama' => 'Luna',
            'tanggal_lahir' => '2023-01-01',
            'warna_tanda' => 'Abu-abu',
            'jenis_kelamin' => 'X',  // tidak valid, harus J atau B
            'idpemilik' => $dummyPemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $response->assertSessionHasErrors(['jenis_kelamin']);
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($receptionist);
    }

    /** nama pet terlalu pendek (min:3) */
    public function test_resepsionis_nama_pet_terlalu_pendek()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);
        $dummyPemilik = $this->createDummyPemilikOnly();
        $rasId = $this->setupDummyRasHewan();

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pet.store-pet'), [
            'nama' => 'AB',  // kurang dari 3 karakter
            'tanggal_lahir' => '2023-06-15',
            'warna_tanda' => 'Hitam Putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $dummyPemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $response->assertSessionHasErrors(['nama']);
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($receptionist);
    }

    /** SQL injection pada nama pemilik */
    public function test_resepsionis_sql_injection_nama_pemilik()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);
        $sqlPayload = "Robert'; DROP TABLE user; --";
        $email = sprintf('sqlinject%04d@gmail.com', rand(1000, 9999));

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => $sqlPayload,
            'email' => $email,
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Test SQL Injection No. 1'
        ]);

        // Data SQL injection seharusnya TIDAK tersimpan di database
        $this->assertDatabaseMissing('user', ['email' => $email]);

        $this->cleanupUser($receptionist);
    }

    /** XSS pada nama pemilik */
    public function test_resepsionis_xss_nama_pemilik()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);
        $xssPayload = '<script>alert("XSS")</script>';
        $email = sprintf('xsstest%04d@gmail.com', rand(1000, 9999));

        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->post(route('Resepsionis.Pemilik.store-pemilik'), [
            'nama' => $xssPayload,
            'email' => $email,
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Test XSS Injection No. 2'
        ]);

        // Data XSS seharusnya TIDAK tersimpan di database
        $this->assertDatabaseMissing('user', ['email' => $email]);

        $this->cleanupUser($receptionist);
    }

    // --- NEGATIVE CASES (ADMIN) ---

    public function test_missing_required_fields_pemilik()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pemilik.store-pemilik'), [
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
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Valid Name',
            'email' => sprintf('TestEmail%02d@test.com', self::$emailCounter++),
            'no_wa' => 'not-a-number',
            'alamat' => 'Valid Address Here'
        ]);

        $response->assertSessionHasErrors(['no_wa']);
        $this->cleanupUser($admin);
    }

    public function test_invalid_date_format_pet()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pet.store-pet'), [
            'nama' => 'Doggy',
            'tanggal_lahir' => 'invalid-date',
            'warna_tanda' => 'Coklat',
            'jenis_kelamin' => 'J',
            'idpemilik' => 9999,
            'idras_hewan' => 9999
        ]);

        $response->assertSessionHasErrors(['tanggal_lahir', 'idpemilik', 'idras_hewan']);
        $this->cleanupUser($admin);
    }

    public function test_duplicate_patient_record_email()
    {
        $admin = $this->createDummyUser(1);
        $existingPemilik = $this->createDummyUser(5);

        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Nama Baru',
            'email' => $existingPemilik->email,
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Duplikat No. 1'
        ]);

        $response->assertSessionHasErrors(['email']);
        
        $this->cleanupUser($existingPemilik);
        $this->cleanupUser($admin);
    }

    public function test_sql_injection_attempt_pet_name()
    {
        $admin = $this->createDummyUser(1);
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Test Owner',
            'email' => $email,
            'no_wa' => '081111111111',
            'alamat' => 'Jl. Test Alamat'
        ]);
        $userPemilik = DB::table('user')->where('email', $email)->first();
        $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

        $rasId = $this->setupDummyRasHewan();
        $sqlInjection = "Bobby'; DROP TABLE pet; --";

        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pet.store-pet'), [
            'nama' => $sqlInjection,
            'tanggal_lahir' => '2023-01-01',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'J',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);

        $this->assertDatabaseHas('pet', [
            'nama' => $sqlInjection,
            'idpemilik' => $pemilik->idpemilik
        ]);

        DB::table('pet')->where('idpemilik', $pemilik->idpemilik)->delete();
        $this->cleanupUser($userPemilik);
        $this->cleanupUser($admin);
    }

    public function test_xss_attempt_owner_name()
    {
        $admin = $this->createDummyUser(1);
        $xssInput = '<img src=x onerror=alert(1)>';
        $email = sprintf('xssadmin%04d@test.com', rand(1000, 9999));

        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => $xssInput,
            'email' => $email,
            'no_wa' => '081111111111',
            'alamat' => '<script>alert("hacked")</script>'
        ]);

        $this->assertDatabaseHas('user', ['email' => $email]);

        $userPemilik = DB::table('user')->where('email', $email)->first();
        if ($userPemilik) {
            $this->cleanupUser($userPemilik);
        }
        $this->cleanupUser($admin);
    }

    public function test_unauthorized_role_attempting_registration()
    {
        $doctor = $this->createDummyUser(2); // Dokter
        
        $response = $this->actingAs($doctor)->post(route('Admin.Pemilik.store-pemilik'), [
            'nama' => 'Unauthorized User',
            'email' => sprintf('TestEmail%02d@test.com', self::$emailCounter++),
            'no_wa' => '081111111111',
            'alamat' => 'Jl. Unauthorized No. 1'
        ]);

        $this->assertTrue(in_array($response->status(), [401, 403, 302]));
        $this->cleanupUser($doctor);
    }

    // --- CRUD TESTS FOR ADMIN ---
    
    public function test_admin_read_pemilik_and_pet()
    {
        $admin = $this->createDummyUser(1);
        $response1 = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->get(route('Admin.Pemilik.daftar-pemilik'));
        $response1->assertStatus(200);
        
        $response2 = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->get(route('Admin.Pet.daftar-pet'));
        $response2->assertStatus(200);
        
        $this->cleanupUser($admin);
    }
    
    public function test_admin_update_pemilik()
    {
        $admin = $this->createDummyUser(1);
        $dummy = $this->createDummyPemilikOnly();
        $newEmail = sprintf('adminupd%04d@test.com', rand(1000, 9999));
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->put(route('Admin.Pemilik.update-pemilik', $dummy->idpemilik), [
            'nama' => 'Ahmad Wijaya',
            'email' => $newEmail,
            'no_wa' => '089999999999',
            'alamat' => 'Jl. Baru No. 99'
        ]);
        
        // format_nama → 'Ahmad Wijaya'
        $this->assertDatabaseHas('user', ['iduser' => $dummy->iduser, 'nama' => 'Ahmad Wijaya']);
        $this->assertDatabaseHas('pemilik', ['idpemilik' => $dummy->idpemilik, 'no_wa' => '089999999999']);
        
        $this->cleanupUser((object)['iduser' => $dummy->iduser]);
        $this->cleanupUser($admin);
    }
    
    public function test_admin_update_pet()
    {
        $admin = $this->createDummyUser(1);
        $dummyPemilik = $this->createDummyPemilikOnly();
        $rasId = $this->setupDummyRasHewan();
        $dummyPet = $this->createDummyPetOnly($dummyPemilik->idpemilik, $rasId);
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->put(route('Admin.Pet.update-pet', $dummyPet->idpet), [
            'nama' => 'Bella',
            'tanggal_lahir' => '2023-02-02',
            'warna_tanda' => 'Putih Bersih',
            'jenis_kelamin' => 'B',
            'idpemilik' => $dummyPemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);
        
        $this->assertDatabaseHas('pet', ['idpet' => $dummyPet->idpet, 'nama' => 'Bella', 'jenis_kelamin' => 'B']);
        
        DB::table('pet')->where('idpet', $dummyPet->idpet)->delete();
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($admin);
    }
    
    public function test_admin_delete_pet()
    {
        $admin = $this->createDummyUser(1);
        $dummyPemilik = $this->createDummyPemilikOnly();
        $rasId = $this->setupDummyRasHewan();
        $dummyPet = $this->createDummyPetOnly($dummyPemilik->idpemilik, $rasId);
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->delete(route('Admin.Pet.delete-pet', $dummyPet->idpet));
        $this->assertDatabaseMissing('pet', ['idpet' => $dummyPet->idpet, 'deleted_at' => null]);
        
        DB::table('pet')->where('idpet', $dummyPet->idpet)->delete();
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($admin);
    }
    
    public function test_admin_delete_pemilik()
    {
        $admin = $this->createDummyUser(1);
        $dummyPemilik = $this->createDummyPemilikOnly();
        
        $response = $this->actingAs($admin)
            ->withSession(['idrole' => 1])
            ->delete(route('Admin.Pemilik.delete-pemilik', $dummyPemilik->idpemilik));
        $this->assertDatabaseMissing('pemilik', ['idpemilik' => $dummyPemilik->idpemilik, 'deleted_at' => null]);
        $this->assertDatabaseMissing('user', ['iduser' => $dummyPemilik->iduser, 'deleted_at' => null]);
        
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($admin);
    }
    
    // --- CRUD TESTS FOR RESEPSIONIS ---
    
    public function test_resepsionis_read_pemilik_and_pet()
    {
        $receptionist = $this->createDummyUser(3);
        Auth::login($receptionist);
        $response1 = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->get(route('Resepsionis.Pemilik.daftar-pemilik'));
        $response1->assertStatus(200);
        
        $response2 = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->get(route('Resepsionis.Pet.daftar-pet'));
        $response2->assertStatus(200);
        
        $this->cleanupUser($receptionist);
    }
    
    public function test_resepsionis_update_pemilik()
    {
        $receptionist = $this->createDummyUser(3);
        $dummy = $this->createDummyPemilikOnly();
        Auth::login($receptionist);
        $newEmail = sprintf('resupd%04d@test.com', rand(1000, 9999));
        
        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->put(route('Resepsionis.Pemilik.save-pemilik', $dummy->iduser), [
            'nama' => 'Siti Aminah',
            'email' => $newEmail,
            'no_wa' => '087777777777',
            'alamat' => 'Jl. Kenanga No. 45'
        ]);
        
        // format_nama → 'Siti Aminah'
        $this->assertDatabaseHas('user', ['iduser' => $dummy->iduser, 'nama' => 'Siti Aminah']);
        $this->assertDatabaseHas('pemilik', ['idpemilik' => $dummy->idpemilik, 'no_wa' => '087777777777']);
        
        $this->cleanupUser((object)['iduser' => $dummy->iduser]);
        $this->cleanupUser($receptionist);
    }
    
    public function test_resepsionis_update_pet()
    {
        $receptionist = $this->createDummyUser(3);
        $dummyPemilik = $this->createDummyPemilikOnly();
        $rasId = $this->setupDummyRasHewan();
        $dummyPet = $this->createDummyPetOnly($dummyPemilik->idpemilik, $rasId);
        Auth::login($receptionist);
        
        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->put(route('Resepsionis.Pet.update-pet', $dummyPet->idpet), [
            'nama' => 'Simba',
            'tanggal_lahir' => '2022-08-15',
            'warna_tanda' => 'Orange Belang',
            'jenis_kelamin' => 'J',
            'idpemilik' => $dummyPemilik->idpemilik,
            'idras_hewan' => $rasId
        ]);
        
        $this->assertDatabaseHas('pet', ['idpet' => $dummyPet->idpet, 'nama' => 'Simba']);
        
        DB::table('pet')->where('idpet', $dummyPet->idpet)->delete();
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($receptionist);
    }
    
    public function test_resepsionis_delete_pet()
    {
        $receptionist = $this->createDummyUser(3);
        $dummyPemilik = $this->createDummyPemilikOnly();
        $rasId = $this->setupDummyRasHewan();
        $dummyPet = $this->createDummyPetOnly($dummyPemilik->idpemilik, $rasId);
        Auth::login($receptionist);
        
        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->delete(route('Resepsionis.Pet.delete-pet', $dummyPet->idpet));
        $this->assertDatabaseMissing('pet', ['idpet' => $dummyPet->idpet, 'deleted_at' => null]);
        
        DB::table('pet')->where('idpet', $dummyPet->idpet)->delete();
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($receptionist);
    }
    
    public function test_resepsionis_delete_pemilik()
    {
        $receptionist = $this->createDummyUser(3);
        $dummyPemilik = $this->createDummyPemilikOnly();
        Auth::login($receptionist);
        
        $response = $this->actingAs($receptionist)
            ->withSession(['idrole' => 3])
            ->delete(route('Resepsionis.Pemilik.delete-pemilik', $dummyPemilik->idpemilik));
        $this->assertDatabaseMissing('pemilik', ['idpemilik' => $dummyPemilik->idpemilik, 'deleted_at' => null]);
        
        $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
        $this->cleanupUser($receptionist);
    }
}
