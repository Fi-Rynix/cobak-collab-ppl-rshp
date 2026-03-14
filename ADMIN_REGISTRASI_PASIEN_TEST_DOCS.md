# Dokumentasi AdminRegistrasiPasienTest

## 📋 Daftar Isi
1. [Overview](#overview)
2. [Struktur Test](#struktur-test)
3. [Helper Methods](#helper-methods)
4. [Test Cases Detail](#test-cases-detail)
5. [Menjalankan Tests](#menjalankan-tests)
6. [Memodifikasi Test](#memodifikasi-test)
7. [Best Practices](#best-practices)

---

## Overview

**File:** `tests/Feature/AdminRegistrasiPasienTest.php`

**Tujuan:** Menguji fitur registrasi pasien (membuat pemilik dan pet) dengan validasi role-based access control dan business logic validation.

**Coverage:**
- ✅ Membuat pemilik (admin)
- ✅ Membuat pet (admin)
- ✅ Validasi input untuk pemilik (5 test cases)
- ✅ Validasi input untuk pet (3 test cases)
- ✅ Authorization checks (1 test case)

**Total Tests:** 9 | **Assertions:** 25 | **Status:** ✅ ALL PASSED

---

## Struktur Test

### Organisasi File

```
AdminRegistrasiPasienTest
├── Private helper methods untuk setup user
│   ├── actAsAdmin()
│   ├── actAsResepsionis()
│   └── actAsDokter()
├── Section 1: CREATE PEMILIK TESTS (5 tests)
├── Section 2: CREATE PET TESTS (3 tests)
└── Section 3: AUTHORIZATION TESTS (1 test)
```

### Setup & Teardown

Test menggunakan trait **RefreshDatabase** yang otomatis:
- ✅ Menjalankan semua migrations sebelum setiap test
- ✅ Menghapus database setelah setiap test
- ✅ Memastikan test data isolated dan tidak saling mempengaruhi

---

## Helper Methods

### 1. `actAsAdmin()`

**Tujuan:** Setup user dengan role Admin untuk test

**Implementasi:**
```php
private function actAsAdmin()
{
    $admin = WithRole::admin();           // Buat user + assign role Admin (id=1)
    $this->actingAs($admin);              // Login as admin (untuk authentication)
    $this->withSession(['idrole' => 1, 'iduser' => $admin->iduser]); // Set session untuk middleware
    return $admin;
}
```

**Mengapa perlu session?**
- Middleware `IsAdmin` mengecek `session('idrole')` bukan hanya auth check
- Tanpa session, middleware akan redirect ke home

**Kapan pakai:**
```php
public function test_admin_can_create_pemilik_successfully()
{
    $this->actAsAdmin();  // Harus dipanggil lebih dulu

    $response = $this->post(route('Admin.Pemilik.store-pemilik'), $data);
    // ... assertions
}
```

---

### 2. `actAsResepsionis()`

**Tujuan:** Setup user dengan role Resepsionis

**Catatan:**
- Resepsionis punya route terpisah (prefix `Resepsionis`, bukan `Admin`)
- Dalam test ini digunakan untuk negative test (memastikan tidak bisa akses route Admin)

```php
private function actAsResepsionis()
{
    $resepsionis = WithRole::resepsionis();  // Create user + assign role Resepsionis (id=4)
    $this->actingAs($resepsionis);
    $this->withSession(['idrole' => 4, 'iduser' => $resepsionis->iduser]);
    return $resepsionis;
}
```

---

### 3. `actAsDokter()`

**Tujuan:** Setup user dengan role Dokter untuk negative test

**Role ID:** 2

```php
private function actAsDokter()
{
    $dokter = WithRole::dokter();
    $this->actingAs($dokter);
    $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
    return $dokter;
}
```

---

## Test Cases Detail

### SECTION 1: CREATE PEMILIK TESTS

#### Test 1: `test_admin_can_create_pemilik_successfully()`

**Type:** Positive Test ✅ (Happy Path)

**Tujuan:** Memastikan admin dapat membuat pemilik baru dengan valid data

**Flow:**
1. Setup admin user
2. Post request dengan valid pemilik data ke `Admin.Pemilik.store-pemilik`
3. Assert redirect ke `Admin.Pemilik.daftar-pemilik`
4. Assert success message di session
5. Verify data di 3 tabel:
   - `user` table (nama, email)
   - `pemilik` table (no_wa, alamat, iduser)
   - `role_user` table (iduser=new user, idrole=5 for Pemilik)

**Valid Input:**
```php
$pemilikData = [
    'nama' => 'John Doe',                           // min 3 chars
    'email' => 'john.doe@example.com',             // valid email, unique
    'no_wa' => '081234567890',                     // 10-15 digits
    'alamat' => 'Jalan Merdeka No 123, Bandung',   // min 5 chars
];
```

**Assertions:**
- Status code: 302 (redirect)
- Redirect destination: `/Admin/Pemilik/daftar-pemilik`
- Session: `success` message
- Database: user exists
- Database: pemilik exists with correct data
- Database: role_user exists with idrole=5 and status=1

---

#### Test 2: `test_pemilik_create_fails_without_nama()`

**Type:** Negative Test ❌ (Validation Error)

**Tujuan:** Memastikan validation menolak nama kosong

**Input:**
```php
'nama' => '',  // Empty
```

**Expected Behavior:**
- Form tidak di-submit ke database
- Session error key `nama` diset
- User tidak di-create di database

**Assertions:**
```php
$response->assertSessionHasErrors('nama');
$this->assertDatabaseMissing('user', ['email' => 'test@example.com']);
```

**Validation Rule di Controller:**
```php
'nama' => ['required', 'string', 'max:255', 'min:3']
```

---

#### Test 3: `test_pemilik_create_fails_without_email()`

**Type:** Negative Test ❌ (Validation Error)

**Tujuan:** Memastikan validation menolak email kosong

**Input:**
```php
'email' => '',  // Empty
```

**Validation Rule di Controller:**
```php
'email' => ['required', 'email', 'max:255', 'unique:user,email']
```

---

#### Test 4: `test_pemilik_create_fails_without_no_wa()`

**Type:** Negative Test ❌ (Validation Error)

**Tujuan:** Memastikan validation menolak no_wa kosong

**Input:**
```php
'no_wa' => '',  // Empty
```

**Validation Rule di Controller:**
```php
'no_wa' => ['required', 'numeric', 'digits_between:10,15']
```

---

#### Test 5: `test_pemilik_create_fails_without_alamat()`

**Type:** Negative Test ❌ (Validation Error)

**Tujuan:** Memastikan validation menolak alamat kosong

**Input:**
```php
'alamat' => '',  // Empty
```

**Validation Rule di Controller:**
```php
'alamat' => ['required', 'string', 'min:5']
```

---

### SECTION 2: CREATE PET TESTS

#### Test 6: `test_admin_can_create_pet_successfully()`

**Type:** Positive Test ✅ (Happy Path)

**Tujuan:** Memastikan admin dapat membuat pet baru dengan valid data

**Prerequisite Data:**
```php
$pemilik = Pemilik::factory()->create();    // Create owner
$rasHewan = RasHewan::factory()->create();  // Create breed (auto-create JenisHewan)
```

**Valid Input:**
```php
$petData = [
    'nama' => 'Fluffy',                                  // 3-100 chars
    'tanggal_lahir' => '2020-01-15',                    // date format
    'warna_tanda' => 'Putih dengan bintik hitam',       // max 45 chars
    'jenis_kelamin' => 'B',                             // J atau B
    'idpemilik' => $pemilik->idpemilik,                // existing pemilik
    'idras_hewan' => $rasHewan->idras_hewan,           // existing ras
];
```

**Flow:**
1. Setup 2 prerequisite data (pemilik, ras hewan)
2. Post pet data
3. Assert redirect & success message
4. Verify pet data di database

---

#### Test 7: `test_pet_create_fails_without_nama()`

**Type:** Negative Test ❌ (Validation Error)

**Tujuan:** Memastikan validation menolak nama kosong

---

#### Test 8: `test_pet_create_fails_without_jenis_kelamin()`

**Type:** Negative Test ❌ (Validation Error)

**Tujuan:** Memastikan validation menolak jenis_kelamin kosong

---

### SECTION 3: AUTHORIZATION TESTS

#### Test 9: `test_dokter_cannot_create_pemilik()`

**Type:** Authorization Test (Negative)

**Tujuan:** Memastikan Dokter tidak bisa akses Admin route

**Setup:**
```php
$this->actAsDokter();  // User with role=2 (Dokter), trying to access Admin route
```

**Expected Behavior:**
- Middleware `IsAdmin` mendeteksi `idrole != 1`
- Redirect ke halaman sebelumnya dengan error message
- Status code: 302 (redirect)
- Session error: "Anda tidak memiliki akses ke halaman ini."

**Middleware Logic (app/Http/Middleware/isAdmin.php):**
```php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $id_role = session('idrole');  // Check session, not database

    if ($id_role == 1) {
        return $next($request);
    }

    return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
}
```

---

## Menjalankan Tests

### 1. Jalankan Semua Admin Tests
```bash
php artisan test tests/Feature/AdminRegistrasiPasienTest.php
```

**Output:**
```
Tests:    9 passed (25 assertions)
Duration: 1.14s
```

---

### 2. Jalankan Test Spesifik
```bash
php artisan test tests/Feature/AdminRegistrasiPasienTest.php::test_admin_can_create_pemilik_successfully
```

---

### 3. Jalankan Dengan Verbose Output
```bash
php artisan test tests/Feature/AdminRegistrasiPasienTest.php --verbose
```

---

### 4. Run Tests Dengan Coverage Report
```bash
php artisan test tests/Feature/AdminRegistrasiPasienTest.php --coverage
```

---

## Memodifikasi Test

### Skenario 1: Tambah Test Case Baru untuk Validasi Email Unique

**Tujuan:** Test email sudah terdaftar

**Langkah:**

1. Add test method:
```php
public function test_pemilik_create_fails_with_duplicate_email()
{
    $this->actAsAdmin();

    // Create first user
    User::factory()->create(['email' => 'existing@example.com']);

    $pemilikData = [
        'nama' => 'John Test',
        'email' => 'existing@example.com',  // Duplicate!
        'no_wa' => '081234567890',
        'alamat' => 'Alamat test',
    ];

    $response = $this->post(route('Admin.Pemilik.store-pemilik'), $pemilikData);

    $response->assertSessionHasErrors('email');
}
```

---

### Skenario 2: Ubah Test untuk Resepsionis Route

**Tujuan:** Test Resepsionis membuat pemilik via `Resepsionis.Pemilik.store-pemilik`

**Current:** Test menggunakan Admin route (yang seharusnya fail)

**Modified:**
```php
public function test_resepsionis_can_create_pemilik()
{
    $this->actAsResepsionis();

    $pemilikData = [
        'nama' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'no_wa' => '082345678901',
        'alamat' => 'Jalan Sudirman No 456, Jakarta',
    ];

    // Change route to Resepsionis route
    $response = $this->post(route('Resepsionis.Pemilik.store-pemilik'), $pemilikData);

    $response->assertRedirect(route('Resepsionis.Pemilik.daftar-pemilik'));
    $response->assertSessionHas('success');
}
```

---

### Skenario 3: Add Test untuk UPDATE Pemilik

**Tujuan:** Test mengupdate data pemilik yang sudah ada

**New Test Method:**
```php
public function test_admin_can_update_pemilik()
{
    $this->actAsAdmin();

    // Create initial pemilik
    $user = User::factory()->create();
    $pemilik = Pemilik::factory()->forUser($user)->create();

    // Update data
    $updateData = [
        'nama' => 'John Updated',
        'email' => 'john.updated@example.com',
        'no_wa' => '089876543210',
        'alamat' => 'Jalan Baru No 999',
    ];

    $response = $this->put(route('Admin.Pemilik.update-pemilik', $pemilik->idpemilik), $updateData);

    $response->assertRedirect(route('Admin.Pemilik.daftar-pemilik'));

    // Verify updated data
    $this->assertDatabaseHas('user', [
        'iduser' => $user->iduser,
        'nama' => 'John Updated',
    ]);
}
```

---

### Skenario 4: Add Test untuk DELETE Pemilik

**Tujuan:** Test menghapus pemilik (soft delete)

**New Test Method:**
```php
public function test_admin_can_delete_pemilik()
{
    $this->actAsAdmin();

    $user = User::factory()->create();
    $pemilik = Pemilik::factory()->forUser($user)->create();

    $response = $this->delete(route('Admin.Pemilik.delete-pemilik', $pemilik->idpemilik));

    $response->assertRedirect(route('Admin.Pemilik.daftar-pemilik'));

    // Verify soft delete
    $this->assertSoftDeleted('pemilik', ['idpemilik' => $pemilik->idpemilik]);
}
```

---

## Best Practices

### 1. **Selalu Set Session untuk Middleware**
❌ **WRONG:**
```php
$admin = WithRole::admin();
$this->actingAs($admin);
$response = $this->post(route('Admin.Pemilik.store-pemilik'), $data);
// Middleware IsAdmin akan reject karena session('idrole') null
```

✅ **CORRECT:**
```php
$admin = WithRole::admin();
$this->actingAs($admin);
$this->withSession(['idrole' => 1, 'iduser' => $admin->iduser]);
$response = $this->post(route('Admin.Pemilik.store-pemilik'), $data);
```

---

### 2. **Gunakan Helper Methods**
✅ **RECOMMENDED:**
```php
public function test_something()
{
    $this->actAsAdmin();  // One line, clean
    // test code
}
```

---

### 3. **Test 1 Behavior Per Test Method**
❌ **WRONG** (terlalu banyak assertions):
```php
public function test_everything()
{
    $this->actAsAdmin();

    // Test create
    $response = $this->post(...);
    $response->assertStatus(302);

    // Test update
    $response = $this->put(...);
    $response->assertStatus(302);

    // Test delete
    $response = $this->delete(...);
    $response->assertStatus(302);
}
```

✅ **CORRECT** (separate test methods):
```php
public function test_admin_can_create_pemilik() { ... }
public function test_admin_can_update_pemilik() { ... }
public function test_admin_can_delete_pemilik() { ... }
```

---

### 4. **Verify Data di Database**

Jangan hanya check HTTP response, verify juga database state:

✅ **COMPLETE:**
```php
public function test_admin_can_create_pemilik_successfully()
{
    $this->actAsAdmin();

    $response = $this->post(route('Admin.Pemilik.store-pemilik'), $data);

    // Check HTTP
    $response->assertRedirect(...);
    $response->assertSessionHas('success');

    // Check Database
    $this->assertDatabaseHas('user', ['email' => $data['email']]);
    $this->assertDatabaseHas('pemilik', ['no_wa' => $data['no_wa']]);
    $this->assertDatabaseHas('role_user', ['idrole' => 5]);
}
```

---

### 5. **Setup Data Sebelum Negative Test**

Untuk duplicate email test, buat user dulu:

```php
public function test_pemilik_create_fails_with_duplicate_email()
{
    $this->actAsAdmin();

    // SETUP: Create existing user
    User::factory()->create(['email' => 'existing@example.com']);

    // TEST: Try create with same email
    $response = $this->post(route('Admin.Pemilik.store-pemilik'), [
        'email' => 'existing@example.com',  // Duplicate
        // ...other data
    ]);

    // ASSERT: Should fail
    $response->assertSessionHasErrors('email');
}
```

---

### 6. **Gunakan Factory Relationship Methods**

Jika ingin create pemilik dengan user tertentu:

```php
$user = User::factory()->create();
$pemilik = Pemilik::factory()->forUser($user)->create();
```

---

## Route Reference

### Admin Routes (Protected by `IsAdmin` middleware)
```
POST   /Admin/Pemilik/store-pemilik          → Pemilik_Controller@store_pemilik
PUT    /Admin/Pemilik/update-pemilik/{id}    → Pemilik_Controller@update_pemilik
DELETE /Admin/Pemilik/delete-pemilik/{id}    → Pemilik_Controller@delete_pemilik

POST   /Admin/Pet/store-pet                  → Pet_Controller@store_pet
PUT    /Admin/Pet/update-pet/{id}            → Pet_Controller@update_pet
DELETE /Admin/Pet/delete-pet/{id}            → Pet_Controller@delete_pet
```

### Resepsionis Routes (Protected by `IsResepsionis` middleware)
```
POST   /Resepsionis/Pemilik/store-pemilik    → PemilikResepsionis_Controller@store_pemilik
PUT    /Resepsionis/Pemilik/save-pemilik/{iduser} → PemilikResepsionis_Controller@save_pemilik

POST   /Resepsionis/Pet/store-pet            → PetResepsionis_Controller@store_pet
PUT    /Resepsionis/Pet/update-pet/{id}      → PetResepsionis_Controller@update_pet
```

---

## FAQ

**Q: Mengapa perlu `withSession(['idrole' => 1, 'iduser' => ...])`?**
A: Middleware `IsAdmin` mengecek `session('idrole')`, bukan `Auth::user()->role`. Session tidak otomatis diset oleh `actingAs()`.

**Q: Bagaimana cara test authorization untuk multiple roles?**
A: Buat test method terpisah (test_admin_..., test_resepsionis_..., test_dokter_...) dengan helper method berbeda.

**Q: Bisa test multiple assertions di satu test method?**
A: Ya, boleh, tapi lebih baik split ke multiple methods untuk clarity.

**Q: Bagaimana cara add test untuk endpoint yang belum ada?**
A: Buat test method, jalankan, akan fail. Kemudian implement controller dan route yang diperlukan.

---

## Kontribusi

Untuk add test baru:
1. Follow naming convention: `test_{action}_{scenario}()`
2. Gunakan helper methods untuk setup
3. Minimal 1 positive + 2 negative tests per feature
4. Always verify database state, not just HTTP response
5. Keep tests focused and independent

