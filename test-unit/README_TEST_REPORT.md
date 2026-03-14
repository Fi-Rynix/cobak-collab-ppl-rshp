# 📋 Laporan Hasil Pengujian Unit Test

**Tanggal Pengujian:** 14 Maret 2026  
**Branch:** `test-unit-udin`  
**Framework:** PHPUnit 11.5.53 | PHP 8.4.7 | Laravel  
**Total Durasi:** 13.44s  
**Hasil:** **43 Passed, 5 Failed** (84 assertions)

---

## 1. EditProfileTest (14/14 ✅ PASSED)

### Positive Case

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| EP-01 | Admin update profil sendiri | Admin mengubah nama diri sendiri via route `Admin.User.update-user` | `nama: 'Ad', email: (existing)` | Nama terupdate di database menjadi `Ad` | ✅ Pass — nama berhasil diupdate |
| EP-02 | Admin update profil Dokter | Admin mengubah nama user Dokter | `nama: 'Dr', email: (existing)` | Nama Dokter terupdate menjadi `Dr` | ✅ Pass — nama berhasil diupdate |
| EP-03 | Admin update profil Resepsionis | Admin mengubah nama user Resepsionis | `nama: 'Re', email: (existing)` | Nama Resepsionis terupdate menjadi `Re` | ✅ Pass — nama berhasil diupdate |
| EP-04 | Admin update profil Perawat | Admin mengubah nama user Perawat | `nama: 'Pr', email: (existing)` | Nama Perawat terupdate menjadi `Pr` | ✅ Pass — nama berhasil diupdate |
| EP-05 | Admin update profil Pemilik | Admin mengubah nama user Pemilik | `nama: 'Pm', email: (existing)` | Nama Pemilik terupdate menjadi `Pm` | ✅ Pass — nama berhasil diupdate |
| EP-06 | Reset password valid | Admin reset password user Dokter | Route `Admin.User.reset-password` | Password ter-reset menjadi `123456` | ✅ Pass — password valid setelah reset |
| EP-07 | Ubah email user | Admin mengubah email diri sendiri | `nama: 'An', email: (new email)` | Email terupdate di database | ✅ Pass — email berhasil diupdate |

### Negative Case

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| EP-08 | Field kosong ditolak | Admin submit form dengan nama dan email kosong | `nama: '', email: ''` | Validasi error pada field `nama` dan `email` | ✅ Pass — session error muncul |
| EP-09 | Format email salah | Admin submit email dengan format tidak valid | `nama: 'Ad', email: 'invalid-email-format'` | Validasi error pada field `email` | ✅ Pass — session error muncul |
| EP-10 | Email duplikat | Admin ubah email ke email user lain yang sudah ada | `nama: 'Ad', email: (email user lain)` | Validasi error atau status 500 | ✅ Pass — error terdeteksi |
| EP-11 | Input sangat panjang | Admin submit nama 505 karakter | `nama: 'aaa...' (505 char), email: (existing)` | Validasi error pada field `nama` | ✅ Pass — session error muncul |
| EP-12 | SQL Injection | Admin submit nama berisi SQL injection | `nama: '=1', email: (existing)` | Data tersimpan aman tanpa eksekusi SQL | ✅ Pass — tersimpan sebagai string biasa |
| EP-13 | XSS Input | Admin submit nama berisi tag HTML | `nama: '<s', email: (existing)` | Data tersimpan aman tanpa eksekusi script | ✅ Pass — tersimpan sebagai string biasa |
| EP-14 | Akses tidak sah | Dokter mencoba edit profil Admin | `nama: 'Hacked Name', email: (admin email)` | Redirect/forbidden (302/401/403) | ✅ Pass — redirect 302 |

---

## 2. PatientRegistrationTest — Registrasi Pasien

### Positive Case

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| PR-01 | Admin registrasi pasien | Admin mendaftarkan pemilik baru + pet baru | Pemilik: `nama: 'Jod', email: (unique), no_wa: '081234567890', alamat: 'Jl. Test No 123'` · Pet: `nama: 'Flu', tanggal_lahir: '2023-01-01', warna_tanda: 'Putih', jenis_kelamin: 'J'` | Data pemilik & pet tersimpan di database | ✅ Pass — data masuk ke DB |
| PR-02 | Resepsionis registrasi pasien | Resepsionis mendaftarkan pemilik baru + pet baru | Pemilik: `nama: 'Budi Santoso', email: (unique), no_wa: '081234567890', alamat: 'Jl. Merpati No. 12, Surabaya'` · Pet: `nama: 'Mochi', tanggal_lahir: '2022-05-10', warna_tanda: 'Putih Coklat', jenis_kelamin: 'B'` | Data pemilik & pet tersimpan di database | ❌ Fail — data tidak masuk ke DB (BUG) |

### Negative Case (Resepsionis)

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| PR-03 | Semua field kosong | Resepsionis submit form pemilik dengan semua field kosong | `nama: '', email: '', no_wa: '', alamat: ''` | Validasi error pada `nama, email, no_wa, alamat` | ✅ Pass — session error muncul |
| PR-04 | Nama terlalu pendek | Resepsionis submit nama kurang dari 3 karakter | `nama: 'Ab', email: (unique), no_wa: '081234567890', alamat: 'Jl. Kenanga No. 5'` | Validasi error pada `nama` (min:3) | ✅ Pass — session error muncul |
| PR-05 | No WA bukan angka | Resepsionis submit no WA berisi huruf | `nama: 'Citra Dewi', email: (unique), no_wa: 'bukan-angka', alamat: 'Jl. Melati No. 8'` | Validasi error pada `no_wa` (numeric) | ✅ Pass — session error muncul |
| PR-06 | No WA terlalu pendek | Resepsionis submit no WA kurang dari 10 digit | `nama: 'Dewi Sari', email: (unique), no_wa: '08123', alamat: 'Jl. Anggrek No. 3'` | Validasi error pada `no_wa` (digits_between:10,15) | ✅ Pass — session error muncul |
| PR-07 | Alamat terlalu pendek | Resepsionis submit alamat kurang dari 5 karakter | `nama: 'Eko Prasetyo', email: (unique), no_wa: '081234567890', alamat: 'Jl'` | Validasi error pada `alamat` (min:5) | ✅ Pass — session error muncul |
| PR-08 | Email duplikat | Resepsionis submit email yang sudah terdaftar | `nama: 'Faisal Akbar', email: (existing user email), no_wa: '081234567890', alamat: 'Jl. Dahlia No. 15'` | Validasi error pada `email` (unique) | ✅ Pass — session error muncul |
| PR-09 | Format email salah | Resepsionis submit email format tidak valid | `nama: 'Gita Nirmala', email: 'ini-bukan-email', no_wa: '081234567890', alamat: 'Jl. Flamboyan No. 7'` | Validasi error pada `email` (email) | ✅ Pass — session error muncul |
| PR-10 | Tanggal lahir pet tidak valid | Resepsionis submit tanggal lahir format salah | `nama: 'Kitty', tanggal_lahir: 'bukan-tanggal', jenis_kelamin: 'B', idpemilik: 9999` | Validasi error pada `tanggal_lahir, idpemilik, idras_hewan` | ✅ Pass — session error muncul |
| PR-11 | Jenis kelamin pet tidak valid | Resepsionis submit jenis kelamin selain J/B | `nama: 'Luna', tanggal_lahir: '2023-01-01', jenis_kelamin: 'X'` | Validasi error pada `jenis_kelamin` (in:J,B) | ✅ Pass — session error muncul |
| PR-12 | Nama pet terlalu pendek | Resepsionis submit nama pet kurang dari 3 karakter | `nama: 'AB', tanggal_lahir: '2023-06-15', jenis_kelamin: 'J'` | Validasi error pada `nama` (min:3) | ✅ Pass — session error muncul |
| PR-13 | SQL Injection nama pemilik | Resepsionis submit nama berisi SQL injection payload | `nama: "Robert'; DROP TABLE user; --", email: (unique)` | Data tersimpan aman tanpa eksekusi SQL | ❌ Fail — data tidak masuk ke DB (BUG) |
| PR-14 | XSS nama pemilik | Resepsionis submit nama berisi XSS script tag | `nama: '<script>alert("XSS")</script>', email: (unique)` | Data tersimpan aman tanpa eksekusi script | ❌ Fail — data tidak masuk ke DB (BUG) |

### Negative Case (Admin)

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| PR-15 | Field pemilik kosong | Admin submit form pemilik dengan semua field kosong | `nama: '', email: '', no_wa: '', alamat: ''` | Validasi error pada `nama, email, no_wa, alamat` | ✅ Pass — session error muncul |
| PR-16 | No WA bukan angka (Admin) | Admin submit no WA berisi huruf | `nama: 'Valid Name', no_wa: 'not-a-number', alamat: 'Valid Address Here'` | Validasi error pada `no_wa` | ✅ Pass — session error muncul |
| PR-17 | Tanggal lahir pet salah (Admin) | Admin submit pet dengan tanggal tidak valid | `nama: 'Doggy', tanggal_lahir: 'invalid-date', idpemilik: 9999` | Validasi error pada `tanggal_lahir, idpemilik, idras_hewan` | ✅ Pass — session error muncul |
| PR-18 | Email duplikat (Admin) | Admin submit pemilik dengan email sudah terdaftar | `nama: 'Nama Baru', email: (existing), alamat: 'Jl. Duplikat No. 1'` | Validasi error pada `email` | ✅ Pass — session error muncul |
| PR-19 | SQL Injection nama pet (Admin) | Admin submit nama pet berisi SQL injection payload | `nama: "Bobby'; DROP TABLE pet; --"` | Data tersimpan aman (PDO parameterized) | ✅ Pass — tersimpan sebagai string biasa |
| PR-20 | XSS nama pemilik (Admin) | Admin submit nama berisi XSS img tag | `nama: '<img src=x onerror=alert(1)>', alamat: '<script>alert("hacked")</script>'` | Data tersimpan aman tanpa sanitasi | ✅ Pass — tersimpan sebagai string biasa |
| PR-21 | Role tidak sah | Dokter mencoba akses route registrasi Admin | `nama: 'Unauthorized User', no_wa: '081111111111'` | Redirect/forbidden (302/401/403) | ✅ Pass — redirect 302 |

---

## 3. PatientRegistrationTest — CRUD Admin

### Positive Case

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| CA-01 | Admin read daftar pemilik & pet | Admin akses halaman daftar pemilik dan daftar pet | GET route `Admin.Pemilik.daftar-pemilik` & `Admin.Pet.daftar-pet` | Response status 200 OK | ✅ Pass — halaman tampil |
| CA-02 | Admin update pemilik | Admin mengubah nama dan no_wa pemilik | `nama: 'Ahmad Wijaya', email: (new unique), no_wa: '089999999999', alamat: 'Jl. Baru No. 99'` | Nama menjadi `Ahmad Wijaya`, no_wa terupdate | ✅ Pass — data terupdate di DB |
| CA-03 | Admin update pet | Admin mengubah nama dan jenis kelamin pet | `nama: 'Bella', tanggal_lahir: '2023-02-02', warna_tanda: 'Putih Bersih', jenis_kelamin: 'B'` | Nama pet menjadi `Bella`, jenis kelamin `B` | ✅ Pass — data terupdate di DB |
| CA-04 | Admin delete pet | Admin soft delete pet | DELETE route `Admin.Pet.delete-pet` | Pet di-soft delete (deleted_at terisi) | ✅ Pass — soft delete berhasil |
| CA-05 | Admin delete pemilik | Admin soft delete pemilik beserta user | DELETE route `Admin.Pemilik.delete-pemilik` | Pemilik & user di-soft delete | ✅ Pass — soft delete berhasil |

---

## 4. PatientRegistrationTest — CRUD Resepsionis

### Positive Case

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| CR-01 | Resepsionis read daftar pemilik & pet | Resepsionis akses halaman daftar pemilik dan daftar pet | GET route `Resepsionis.Pemilik.daftar-pemilik` & `Resepsionis.Pet.daftar-pet` | Response status 200 OK | ✅ Pass — halaman tampil |
| CR-02 | Resepsionis update pemilik | Resepsionis mengubah nama dan no_wa pemilik | `nama: 'Siti Aminah', email: (new unique), no_wa: '087777777777', alamat: 'Jl. Kenanga No. 45'` | Nama menjadi `Siti Aminah`, no_wa terupdate | ❌ Fail — nama tetap `Dummy Pemilik` (BUG) |
| CR-03 | Resepsionis update pet | Resepsionis mengubah nama pet | `nama: 'Simba', tanggal_lahir: '2022-08-15', warna_tanda: 'Orange Belang', jenis_kelamin: 'J'` | Nama pet menjadi `Simba` | ❌ Fail — nama tetap `Dummy Pet` (BUG) |
| CR-04 | Resepsionis delete pet | Resepsionis soft delete pet | DELETE route `Resepsionis.Pet.delete-pet` | Pet di-soft delete (deleted_at terisi) | ✅ Pass — soft delete berhasil |
| CR-05 | Resepsionis delete pemilik | Resepsionis soft delete pemilik | DELETE route `Resepsionis.Pemilik.delete-pemilik` | Pemilik di-soft delete | ✅ Pass — soft delete berhasil |

---

## 5. UserTest (3/3 ✅ PASSED)

### Positive Case

| ID | Deskripsi | Test Scenario | Data Uji | Hasil yang Diharapkan | Actual Result |
|----|-----------|---------------|----------|----------------------|---------------|
| UT-01 | Pembuatan user & role | Buat user baru dan assign role | `nama, email, password, idrole` | User & role_user tersimpan di DB | ✅ Pass — data tersimpan |
| UT-02 | Login berhasil | User login dengan kredensial valid | `email, password: '123456'` | User berhasil terautentikasi | ✅ Pass — Auth check true |
| UT-03 | Logout berhasil | User yang sudah login melakukan logout | Session user aktif | User ter-logout dari session | ✅ Pass — Auth check false |

---

## 📊 Ringkasan

| Test Suite | Total | Pass | Fail |
|------------|-------|------|------|
| EditProfileTest | 14 | 14 | 0 |
| PatientRegistrationTest | 31 | 26 | 5 |
| UserTest | 3 | 3 | 0 |
| **TOTAL** | **48** | **43** | **5** |

---

## 🐛 Daftar Bug Ditemukan

| No | Bug ID | Lokasi | Severity | Deskripsi |
|----|--------|--------|----------|-----------|
| 1 | PR-02 | `PemilikResepsionis_Controller::store_pemilik` | **High** | Resepsionis tidak bisa mendaftarkan pemilik baru — data tidak masuk ke database |
| 2 | PR-13 | `PemilikResepsionis_Controller::store_pemilik` | **Medium** | SQL Injection payload pada nama pemilik via Resepsionis menyebabkan data gagal insert |
| 3 | PR-14 | `PemilikResepsionis_Controller::store_pemilik` | **Medium** | XSS payload pada nama pemilik via Resepsionis menyebabkan data gagal insert |
| 4 | CR-02 | `PemilikResepsionis_Controller::save_pemilik` | **High** | Resepsionis tidak bisa update data pemilik — nama dan no_wa tidak berubah |
| 5 | CR-03 | `PetResepsionis_Controller::update_pet` | **High** | Resepsionis tidak bisa update data pet — nama pet tidak berubah |
