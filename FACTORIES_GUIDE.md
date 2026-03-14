# Factory Testing Guide

Dokumentasi ini menjelaskan cara menggunakan factories untuk testing fitur rekam medis dan registrasi pet.

## 📋 Daftar Factories yang Tersedia

### Core Factories (untuk testing):
1. **UserFactory** - Membuat user
2. **RoleUserFactory** - Menghubungkan user dengan role (dengan preset: admin, dokter, perawat, resepsionis, pemilik)
3. **PemilikFactory** - Membuat pemilik hewan
4. **PetFactory** - Membuat hewan peliharaan
5. **TemuDokterFactory** - Membuat jadwal temu dokter
6. **RekamMedisFactory** - Membuat rekam medis
7. **DetailRekamMedisFactory** - Membuat detail rekam medis

### Support Factories:
8. **RasHewanFactory** - Membuat ras hewan (default idjenis_hewan = 1)
9. **KodeTindakanTerapiFactory** - Membuat kode tindakan terapi (default idkategori = 1, idkategori_klinis = 1)

### Helper:
- **WithRole trait** - Helper static methods untuk membuat user dengan role tertentu

---

## 📖 Penggunaan Detail

### 1. UserFactory
Membuat data user untuk testing.

```php
// User biasa
$user = User::factory()->create();

// User dengan nama dan email spesifik
$user = User::factory()->create([
    'nama' => 'John Doe',
    'email' => 'john@example.com',
]);
```

### 2. RoleUserFactory
Menghubungkan user dengan role. Terdapat 5 role yang sudah fixed di database.

```php
// RoleUser dengan user dan role default (Pemilik)
$roleUser = RoleUser::factory()->create();

// RoleUser dengan user spesifik
$roleUser = RoleUser::factory()
    ->forUser($user)
    ->create();

// RoleUser dengan role tertentu
$roleUser = RoleUser::factory()
    ->dokter()
    ->create();

// Contoh lengkap: User Dokter
$user = User::factory()->create();
$roleUser = RoleUser::factory()
    ->forUser($user)
    ->dokter()
    ->create();
```

**Role yang tersedia:**
- `admin()` - idrole 1
- `dokter()` - idrole 2
- `perawat()` - idrole 3
- `resepsionis()` - idrole 4
- `pemilik()` - idrole 5 (default)

### 3. PemilikFactory
Membuat data pemilik hewan.

```php
// Pemilik generik
$pemilik = Pemilik::factory()->create();

// Pemilik untuk user spesifik
$pemilik = Pemilik::factory()
    ->forUser($user)
    ->create();
```

### 4. RasHewanFactory
Membuat ras hewan. Default menggunakan idjenis_hewan = 1.

```php
// Ras hewan (default idjenis_hewan = 1)
$rasHewan = RasHewan::factory()->create();

// Ras untuk jenis hewan spesifik (jika ada)
$rasHewan = RasHewan::factory()
    ->forJenisHewan($jenisHewan)
    ->create();

// Atau manual override jenis hewan
$rasHewan = RasHewan::factory()->create([
    'idjenis_hewan' => 2,
]);
```

### 5. PetFactory
Membuat data hewan peliharaan.

```php
// Pet generik
$pet = Pet::factory()->create();

// Pet untuk pemilik spesifik
$pet = Pet::factory()
    ->forPemilik($pemilik)
    ->create();

// Pet dengan ras hewan spesifik
$pet = Pet::factory()
    ->forRasHewan($rasHewan)
    ->create();

// Pet dengan pemilik dan ras spesifik
$pet = Pet::factory()
    ->forPemilik($pemilik)
    ->forRasHewan($rasHewan)
    ->create();
```

### 6. KodeTindakanTerapiFactory
Membuat kode tindakan terapi. Default menggunakan idkategori = 1 dan idkategori_klinis = 1.

```php
// Kode tindakan (default idkategori = 1, idkategori_klinis = 1)
$kodeTindakan = KodeTindakanTerapi::factory()->create();

// Untuk kategori spesifik
$kodeTindakan = KodeTindakanTerapi::factory()
    ->forKategori($kategori)
    ->create();

// Atau manual override
$kodeTindakan = KodeTindakanTerapi::factory()->create([
    'idkategori' => 2,
    'idkategori_klinis' => 2,
]);
```

### 7. TemuDokterFactory
Membuat jadwal temu dokter.

```php
// TemuDokter generik
$temuDokter = TemuDokter::factory()->create();

// Untuk pet spesifik
$temuDokter = TemuDokter::factory()
    ->forPet($pet)
    ->create();

// Untuk dokter spesifik
$temuDokter = TemuDokter::factory()
    ->forRoleUser($roleUserDokter)
    ->create();
```

### 8. RekamMedisFactory
Membuat rekam medis.

```php
// Rekam medis generik
$rekamMedis = RekamMedis::factory()->create();

// Untuk dokter spesifik
$rekamMedis = RekamMedis::factory()
    ->forDokter($roleUserDokter)
    ->create();

// Untuk temu dokter spesifik
$rekamMedis = RekamMedis::factory()
    ->forTemuDokter($temuDokter)
    ->create();
```

### 9. DetailRekamMedisFactory
Membuat detail rekam medis.

```php
// Detail generik
$detail = DetailRekamMedis::factory()->create();

// Untuk rekam medis spesifik
$detail = DetailRekamMedis::factory()
    ->forRekamMedis($rekamMedis)
    ->create();

// Untuk kode tindakan spesifik
$detail = DetailRekamMedis::factory()
    ->forKodeTindakanTerapi($kodeTindakan)
    ->create();
```

---

## 🎯 Trait WithRole - Helper untuk Testing

Trait `WithRole` menyediakan method static untuk membuat user dengan role tertentu secara langsung:

```php
use Database\Factories\WithRole;

// Membuat user dengan role Admin
$admin = WithRole::admin();

// Membuat user dengan role Dokter
$dokter = WithRole::dokter();

// Membuat user dengan role Perawat
$perawat = WithRole::perawat();

// Membuat user dengan role Resepsionis
$resepsionis = WithRole::resepsionis();

// Membuat user dengan role Pemilik
$pemilik = WithRole::pemilik();
```

---

## 📝 Contoh Testing Lengkap

### Scenario: Testing registrasi pet

```php
<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Pemilik;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_resepsionis_can_register_pet()
    {
        // Membuat user dengan role Resepsionis
        $resepsionis = WithRole::resepsionis();

        // Login sebagai resepsionis
        $this->actingAs($resepsionis);

        // Membuat owner/pemilik
        $pemilik = Pemilik::factory()->create();

        // Test registrasi pet
        $petData = [
            'nama' => 'Fluffy',
            'tanggal_lahir' => '2020-01-15',
            'warna_tanda' => 'White',
            'jenis_kelamin' => 'Betina',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => 1,
        ];

        $response = $this->post('/api/pets', $petData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pet', ['nama' => 'Fluffy']);
    }

    public function test_dokter_cannot_register_pet()
    {
        // Membuat user dengan role Dokter
        $dokter = WithRole::dokter();

        // Login sebagai dokter
        $this->actingAs($dokter);

        // Membuat pemilik
        $pemilik = Pemilik::factory()->create();

        // Test - seharusnya Dokter tidak bisa daftar pet
        $petData = [
            'nama' => 'Fluffy',
            'tanggal_lahir' => '2020-01-15',
            'warna_tanda' => 'White',
            'jenis_kelamin' => 'Betina',
            'idpemilik' => $pemilik->idpemilik,
            'idras_hewan' => 1,
        ];

        $response = $this->post('/api/pets', $petData);

        // Seharusnya unauthorized
        $response->assertStatus(403);
    }
}
```

### Scenario: Testing rekam medis

```php
<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\RekamMedis;
use App\Models\TemuDokter;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_dokter_can_create_rekam_medis()
    {
        // Membuat dokter dengan role
        $dokter = WithRole::dokter();
        $this->actingAs($dokter);

        // Membuat pet dan temu dokter
        $pet = Pet::factory()->create();
        $temuDokter = TemuDokter::factory()
            ->forPet($pet)
            ->create();

        // Data rekam medis
        $data = [
            'anamnesa' => 'Hewan terlihat lesu',
            'temuan_klinis' => 'Suhu normal',
            'diagnosa' => 'Flu',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post('/api/rekam-medis', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rekam_medis', ['diagnosa' => 'Flu']);
    }

    public function test_resepsionis_cannot_create_rekam_medis()
    {
        // Membuat resepsionis
        $resepsionis = WithRole::resepsionis();
        $this->actingAs($resepsionis);

        $pet = Pet::factory()->create();
        $temuDokter = TemuDokter::factory()->forPet($pet)->create();

        $data = [
            'anamnesa' => 'Hewan terlihat lesu',
            'temuan_klinis' => 'Suhu normal',
            'diagnosa' => 'Flu',
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ];

        $response = $this->post('/api/rekam-medis', $data);

        // Seharusnya unauthorized
        $response->assertStatus(403);
    }
}
```

---

## ✅ Tips

1. **Selalu gunakan `RefreshDatabase`** untuk setiap test untuk memastikan database bersih
2. **Gunakan `WithRole` helper** untuk membuat user dengan role spesifik dengan cepat
3. **Gunakan method builder** seperti `forUser()`, `forPet()` untuk relasi yang jelas
4. **Chain multiple factories** untuk membuat data yang kompleks dengan mudah
5. **Test role authorization** dengan membuat test untuk setiap role yang berbeda
6. **Gunakan default value** untuk foreign keys yang tidak critical
