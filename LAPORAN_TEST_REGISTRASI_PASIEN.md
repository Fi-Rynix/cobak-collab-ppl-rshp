# LAPORAN PENGUJIAN (TEST REPORT)
Registrasi Pasien - Admin & Resepsionis

**Tanggal**: 2026-03-14
**Framework**: Laravel PHPUnit
**Status**: Ready for Test Execution

---

## I. PENDAHULUAN

Laporan ini mendokumentasikan hasil pengujian fitur registrasi pasien pada aplikasi manajemen klinik hewan. Pengujian mencakup dua modul utama dengan total 20 test cases yang mengcover skenario positif dan negatif untuk create pemilik dan create pet.

---

## II. ADMIN REGISTRASI PASIEN TEST

**File Test**: tests/Feature/AdminRegistrasiPasienTest.php
**Total Cases**: 9 (2 Positif + 7 Negatif)
**Coverage**: Create Pemilik (6 test) & Create Pet (3 test)

---

### Test Case 1

**ID**: TRPA-P01

**Test Case**: Admin Berhasil Create Pemilik

**Test Scenario**:
Login sebagai Admin (idrole = 1)
Input data pemilik dengan informasi yang valid
Submit form ke route Admin.Pemilik.store-pemilik
Verifikasi redirect ke halaman daftar
Verifikasi pesan success muncul di session
Verifikasi data tersimpan di tabel user, pemilik, dan role_user

**Test Data**:
Nama: John Doe
Email: john.doe@example.com
No wa: 081234567890
Alamat: Jalan Merdeka No 123, Bandung

**Expected**:
Data pemilik berhasil ditambahkan dan sistem melakukan redirect ke halaman daftar pemilik dengan menampilkan pesan sukses, serta data tersimpan di tabel user, pemilik, dan role_user dengan idrole = 5 (Pemilik).

**Actual**:

---

### Test Case 2

**ID**: TRPA-N01

**Test Case**: Dokter Gagal Create Pemilik

**Test Scenario**:
Login sebagai Dokter (idrole = 2)
Coba mengakses route Admin.Pemilik.store-pemilik dengan data valid
Verifikasi middleware IsAdmin menolak request
Verifikasi error message muncul di session
Verifikasi data tidak tersimpan di database

**Test Data**:
Role: Dokter
Nama: Dokter Test
Email: dokter@example.com
No wa: 081234567890
Alamat: Alamat dokter test

**Expected**:
User dokter tidak berhasil menambahkan data pemilik dan sistem menolak akses dengan menampilkan pesan error otorisasi, serta data tidak tersimpan di database.

**Actual**:

---

### Test Case 3

**ID**: TRPA-N02

**Test Case**: Nama Melebihi Max Length

**Test Scenario**:
Login sebagai Admin
Input data pemilik dengan nama 256 karakter (melebihi max 255)
Submit form
Verifikasi validation error pada field nama
Verifikasi data tidak tersimpan

**Test Data**:
Nama: AAAAAA...AAAAAA (256 karakter)
Email: test@example.com
No wa: 081234567890
Alamat: Alamat test

**Expected**:
Data pemilik gagal ditambahkan dan session menampilkan error validation pada field nama karena melebihi batas maksimal karakter.

**Actual**:

---

### Test Case 4

**ID**: TRPA-N03

**Test Case**: Email Invalid

**Test Scenario**:
Login sebagai Admin
Input data pemilik dengan email tidak valid (tanpa @)
Submit form
Verifikasi validation error pada field email

**Test Data**:
Nama: John Test
Email: invalid-email (tanpa @)
No wa: 081234567890
Alamat: Alamat test

**Expected**:
Data pemilik gagal ditambahkan dan session menampilkan error validation pada field email karena format email tidak valid.

**Actual**:

---

### Test Case 5

**ID**: TRPA-N04

**Test Case**: No WA Mengandung Huruf

**Test Scenario**:
Login sebagai Admin
Input data pemilik dengan no_wa mengandung huruf (bukan numeric)
Submit form
Verifikasi validation error pada field no_wa

**Test Data**:
Nama: John Test
Email: john@example.com
No wa: 0812abc567890 (mengandung huruf)
Alamat: Alamat test

**Expected**:
Data pemilik gagal ditambahkan dan session menampilkan error validation pada field no_wa karena harus berupa angka saja.

**Actual**:

---

### Test Case 6

**ID**: TRPA-N05

**Test Case**: Alamat Kosong

**Test Scenario**:
Login sebagai Admin
Input data pemilik dengan alamat tidak diisi (kosong)
Submit form
Verifikasi validation error pada field alamat

**Test Data**:
Nama: John Test
Email: john@example.com
No wa: 081234567890
Alamat: "" (kosong)

**Expected**:
Data pemilik gagal ditambahkan dan session menampilkan error validation pada field alamat karena wajib diisi.

**Actual**:

---

### Test Case 7

**ID**: TRPA-P02

**Test Case**: Admin Berhasil Create Pet

**Test Scenario**:
Login sebagai Admin
Create test pemilik dan ras hewan via factory
Input data pet dengan informasi yang valid
Submit form ke route Admin.Pet.store-pet
Verifikasi redirect ke halaman daftar pet
Verifikasi pesan success muncul
Verifikasi data tersimpan di tabel pet

**Test Data**:
Nama: Fluffy
Tanggal Lahir: 2020-01-15
Warna/Tanda: Putih dengan bintik hitam
Jenis Kelamin: B
Pemilik: Factory generated
Ras Hewan: Factory generated

**Expected**:
Data pet berhasil ditambahkan dan sistem melakukan redirect ke halaman daftar pet dengan menampilkan pesan sukses, serta data tersimpan di tabel pet dengan semua field yang sesuai.

**Actual**:

---

### Test Case 8

**ID**: TRPA-N06

**Test Case**: Pet Tanpa Nama

**Test Scenario**:
Login sebagai Admin
Create test pemilik dan ras hewan via factory
Input data pet dengan nama kosong
Submit form
Verifikasi validation error pada field nama

**Test Data**:
Nama: "" (kosong)
Tanggal Lahir: 2020-01-15
Warna/Tanda: Putih
Jenis Kelamin: B

**Expected**:
Data pet tidak berhasil ditambahkan dan session menampilkan error field nama karena wajib diisi.

**Actual**:

---

### Test Case 9

**ID**: TRPA-N07

**Test Case**: Jenis Kelamin Invalid

**Test Scenario**:
Login sebagai Admin
Create test pemilik dan ras hewan via factory
Input data pet dengan jenis_kelamin tidak valid (bukan J atau B)
Submit form
Verifikasi validation error pada field jenis_kelamin

**Test Data**:
Nama: Fluffy
Tanggal Lahir: 2020-01-15
Warna/Tanda: Putih
Jenis Kelamin: X (invalid)

**Expected**:
Data pet tidak berhasil ditambahkan dan session menampilkan error field jenis_kelamin karena hanya menerima J (Jantan) atau B (Betina).

**Actual**:

---

## III. RESEPSIONIS REGISTRASI PASIEN TEST

**File Test**: tests/Feature/ResepsionisRegistrasiPasienTest.php
**Total Cases**: 7 (2 Positif + 5 Negatif)
**Coverage**: Create Pemilik (6 test) & Create Pet (1 test)
**Note**: Test otorisasi lebih comprehensive dengan 3 role lain yang ditest

---

### Test Case 1

**ID**: TRPR-P01

**Test Case**: Resepsionis Berhasil Create Pemilik

**Test Scenario**:
Login sebagai Resepsionis (idrole = 4)
Input data pemilik dengan informasi yang valid
Submit form ke route Resepsionis.Pemilik.store-pemilik
Verifikasi redirect ke halaman daftar pemilik resepsionis
Verifikasi pesan success muncul di session
Verifikasi data tersimpan di tabel user, pemilik, dan role_user dengan idrole = 5

**Test Data**:
Nama: Siti Nurhaliza
Email: siti.nurhaliza@example.com
No wa: 082567891234
Alamat: Jalan Ahmad Yani No 789, Surabaya

**Expected**:
Data pemilik berhasil ditambahkan dan sistem melakukan redirect ke halaman daftar pemilik resepsionis dengan menampilkan pesan sukses, serta data tersimpan di tabel user, pemilik, dan role_user dengan idrole = 5.

**Actual**:

---

### Test Case 2

**ID**: TRPR-N01

**Test Case**: Perawat Gagal Create Pemilik

**Test Scenario**:
Login sebagai Perawat (idrole = 3)
Coba mengakses route Resepsionis.Pemilik.store-pemilik dengan data valid
Verifikasi middleware IsResepsionis menolak request
Verifikasi error message muncul

**Test Data**:
Role: Perawat
Nama: Perawat Test
Email: perawat@example.com
No wa: 082567891234
Alamat: Alamat perawat test

**Expected**:
User perawat tidak berhasil memasukkan data dan sistem menolak akses dengan menampilkan pesan error otorisasi.

**Actual**:

---

### Test Case 3

**ID**: TRPR-N02

**Test Case**: Admin Gagal Create Pemilik

**Test Scenario**:
Login sebagai Admin (idrole = 1)
Coba mengakses route Resepsionis.Pemilik.store-pemilik dengan data valid
Verifikasi middleware IsResepsionis menolak request meskipun role Admin lebih tinggi
Verifikasi error message muncul

**Test Data**:
Role: Admin
Nama: Admin Test
Email: admin@example.com
No wa: 082567891234
Alamat: Alamat admin test

**Expected**:
User admin tidak berhasil memasukkan data meskipun levelnya lebih tinggi, dan sistem menolak akses dengan menampilkan pesan error otorisasi.

**Actual**:

---

### Test Case 4

**ID**: TRPR-N03

**Test Case**: Dokter Gagal Create Pemilik

**Test Scenario**:
Login sebagai Dokter (idrole = 2)
Coba mengakses route Resepsionis.Pemilik.store-pemilik dengan data valid
Verifikasi middleware IsResepsionis menolak request
Verifikasi error message muncul

**Test Data**:
Role: Dokter
Nama: Dokter Test
Email: dokter@example.com
No wa: 082567891234
Alamat: Alamat dokter test

**Expected**:
User dokter tidak berhasil memasukkan data dan sistem menolak akses dengan menampilkan pesan error otorisasi.

**Actual**:

---

### Test Case 5

**ID**: TRPR-N04

**Test Case**: Nama Kurang dari 3 Karakter

**Test Scenario**:
Login sebagai Resepsionis
Input data pemilik dengan nama kurang dari 3 karakter (test case berbeda dari admin)
Submit form
Verifikasi validation error pada field nama

**Test Data**:
Nama: AB (2 karakter)
Email: test@example.com
No wa: 082567891234
Alamat: Alamat test

**Expected**:
Data pemilik gagal ditambahkan dan session menampilkan error validation pada field nama karena kurang dari minimum 3 karakter.

**Actual**:

---

### Test Case 6

**ID**: TRPR-N05

**Test Case**: Email Duplikat

**Test Scenario**:
Login sebagai Resepsionis
Pre-create user dengan email existing@example.com di database
Input data pemilik dengan email yang sama
Submit form
Verifikasi validation error pada field email (unique constraint)

**Test Data**:
Pre-existing email: existing@example.com
Nama: Siti Test
Email: existing@example.com (duplikat)
No wa: 082567891234
Alamat: Alamat test

**Expected**:
Data pemilik gagal ditambahkan dan session menampilkan error validation pada field email karena sudah terdaftar di database.

**Actual**:

---

### Test Case 7

**ID**: TRPR-P02

**Test Case**: Resepsionis Berhasil Create Pet

**Test Scenario**:
Login sebagai Resepsionis
Create test pemilik dan ras hewan via factory
Input data pet dengan informasi yang valid
Submit form ke route Resepsionis.Pet.store-pet
Verifikasi redirect ke halaman daftar pet resepsionis
Verifikasi pesan success muncul
Verifikasi data tersimpan di tabel pet

**Test Data**:
Nama: Charlie
Tanggal Lahir: 2022-03-10
Warna/Tanda: Hitam dengan putih
Jenis Kelamin: B
Pemilik: Factory generated
Ras Hewan: Factory generated

**Expected**:
Data pet berhasil ditambahkan dan sistem melakukan redirect ke halaman daftar pet resepsionis dengan menampilkan pesan sukses, serta data tersimpan di tabel pet.

**Actual**:

---

## IV. RINGKASAN & STATISTIK

**Total Test Cases**: 16
**Positif Cases**: 4 (25%)
**Negatif Cases**: 12 (75%)

Breakdown by Module

Admin: Total 9, Positif 2, Negatif 7
Resepsionis: Total 7, Positif 2, Negatif 5

Breakdown by Type

Create Pemilik (Positif): 2
Create Pemilik (Otorisasi): 4
Create Pemilik (Validasi): 6
Create Pet (Positif): 2
Create Pet (Validasi): 2

---

## V. CATATAN EKSEKUSI

Cara Menjalankan Test:

```bash
php artisan test tests/Feature/AdminRegistrasiPasienTest.php
php artisan test tests/Feature/ResepsionisRegistrasiPasienTest.php
php artisan test tests/Feature/AdminRegistrasiPasienTest.php tests/Feature/ResepsionisRegistrasiPasienTest.php --verbose
```

Kolom Actual pada setiap test case di atas KOSONG dan harus diisi dengan hasil eksekusi test:
PASS: Jika semua assertion berhasil
FAIL: Jika ada assertion yang gagal, tuliskan assertion mana dan error message-nya
ERROR: Jika ada error pada test execution

---

*Laporan ini dibuat dengan struktur: ID, Test Case, Test Scenario, Test Data, Expected, dan Actual untuk memudahkan tracking dan dokumentasi hasil pengujian.*

*Generated: 2026-03-14*
*Framework: Laravel PHPUnit Testing*