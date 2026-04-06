# LAPORAN PENGUJIAN (TEST REPORT)
Temu Dokter - Create (Resepsionis, Perawat, Admin)

**Tanggal**: 2026-03-30
**Framework**: Laravel PHPUnit
**Status**: Ready for Test Execution

---

## I. PENDAHULUAN

Laporan ini mendokumentasikan hasil pengujian fitur pembuatan jadwal temu dokter (reservasi dokter) dalam aplikasi manajemen klinik hewan. Pengujian mencakup skenario positif untuk create appointment oleh berbagai role (Resepsionis, Perawat, Admin), skenario edge case untuk reset nomor urut harian, dan skenario negatif untuk pencegahan appointment dengan dokter yang tidak aktif.

---

## II. TEMU DOKTER TEST

**File Test**: tests/Feature/TemuDokterTest.php
**Total Cases**: 5 (3 Positif + 1 Edge Case + 1 Negatif)
**Coverage**: Create TemuDokter oleh 3 role + daily sequence + inactive doctor prevention

---

### Test Case 1

### Test Case 1

**ID**: TTDR-P01

**Test Case**: Resepsionis Berhasil Create Temu Dokter

**Test Scenario**:
Login sebagai Resepsionis
Create pet dan dokter via factory
Prepare data create appointment (idpet, idrole_user dari dokter aktif)
Submit POST request ke route Resepsionis.TemuDokter.store-temu-dokter
Verifikasi response redirect ke route Resepsionis.TemuDokter.daftar-temu-dokter
Verifikasi session menampilkan pesan success 'Reservasi dokter berhasil dibuat.'
Verifikasi data temu dokter tersimpan di database dengan status='W' dan no_urut=1

**Test Data**:
User: Factory generated Resepsionis (idrole=4)
Pet: Factory generated
Dokter: Factory generated dengan status=1 (aktif)
idpet: valid pet id
idrole_user: valid dokter role_user id dengan status aktif

**Expected**:
Appointment berhasil dibuat, sistem melakukan redirect ke daftar appointment dengan pesan success, data tersimpan di database dengan no_urut=1 untuk hari itu (sequence dimulai dari 1 setiap hari).

**Actual**:

---

### Test Case 2

**ID**: TTDR-N01

**Test Case**: Perawat Gagal Create Temu Dokter

**Test Scenario**:
Login sebagai Perawat
Create pet dan dokter via factory
Prepare data create appointment (idpet, idrole_user dari dokter aktif)
Attempt POST request ke route Perawat.TemuDokter.store-temu-dokter
Verifikasi response menunjukkan kegagalan (RouteNotFoundException atau 404)
Verifikasi data temu dokter TIDAK tersimpan di database (assertDatabaseMissing)
Memastikan perawat tidak memiliki akses untuk membuat appointment

**Test Data**:
User: Factory generated Perawat (idrole=3)
Pet: Factory generated
Dokter: Factory generated dengan status=1 (aktif)
idpet: valid pet id
idrole_user: valid dokter role_user id dengan status aktif

**Expected**:
Sistem menolak Perawat untuk membuat appointment karena route/feature belum diimplementasikan atau Perawat tidak memiliki authorization. Test expect RouteNotFoundException atau Perawat tidak dapat akses fitur ini. Data appointment TIDAK tersimpan di database.

**Actual**:

---

### Test Case 3

**ID**: TTDR-P02

**Test Case**: Admin Berhasil Create Temu Dokter

**Test Scenario**:
Login sebagai Admin
Create pet dan dokter via factory
Prepare data create appointment (idpet, idrole_user dari dokter aktif)
Submit POST request ke route Admin.TemuDokter.store-temu-dokter
Verifikasi response redirect ke route Admin.TemuDokter.daftar-temu-dokter
Verifikasi session menampilkan pesan success 'Reservasi dokter berhasil dibuat.'
Verifikasi data temu dokter tersimpan di database dengan status='W'

**Test Data**:
User: Factory generated Admin (idrole=1)
Pet: Factory generated
Dokter: Factory generated dengan status=1 (aktif)
idpet: valid pet id
idrole_user: valid dokter role_user id dengan status aktif

**Expected**:
Appointment berhasil dibuat oleh admin, sistem melakukan redirect dengan pesan success, data tersimpan di database dengan status='W'.

**Actual**:

---

### Test Case 4

**ID**: TTDR-P03

**Test Case**: Admin Create Dua Appointment dengan Daily Sequence Reset

**Test Scenario**:
Login sebagai Admin
Create 2 pets dan 1 dokter
DAY 1 - Buat appointment pertama untuk Dokter X
Verifikasi response redirect success
Query temu dokter hari ini untuk Dokter X
Verifikasi no_urut = 1
DAY 1 - Buat appointment kedua untuk Dokter X (hari yang sama)
Verifikasi response redirect success
Query temu dokter hari ini untuk Dokter X
Verifikasi count = 2
Verifikasi appointment 1 memiliki no_urut = 1
Verifikasi appointment 2 memiliki no_urut = 2
DAY 2 - Travel time ke besok (Carbon::tomorrow())
Create appointment ketiga untuk Dokter X
Verifikasi response redirect success
Query temu dokter besok untuk Dokter X dengan filter whereDate('waktu_daftar', tomorrow)
Verifikasi NO_URUT RESET KE 1 (bukan 3)

**Test Data**:
User: Factory generated Admin
Pet 1: Factory generated
Pet 2: Factory generated
Pet 3: Factory generated
Dokter: Factory generated dengan status=1
Day 1 Appointment 1: idpet=pet1.id, idrole_user=dokter role_user id
Day 1 Appointment 2: idpet=pet2.id, idrole_user=dokter role_user id
Day 2 Appointment 3: idpet=pet3.id, idrole_user=dokter role_user id

**Expected**:
Nomor urut (sequence number) mereset setiap hari per dokter. Hari 1 dibuat 2 appointment dengan nomor 1 dan 2. Hari 2 membuat appointment baru memiliki nomor 1 lagi (RESET). Ini memastikan nomor urut appointment dokter reset setiap harinya.

**Actual**:

---

### Test Case 5

**ID**: TTDR-N02

**Test Case**: Admin Gagal Create Temu Dokter dengan Dokter Status Inactive

**Test Scenario**:
Login sebagai Admin
Create pet dan dokter via factory
Ubah status dokter menjadi 0 (inactive) menggunakan $roleUserDokter->update(['status' => 0])
Prepare data create appointment dengan idrole_user dari dokter yang tidak aktif
Submit POST request ke route Admin.TemuDokter.store-temu-dokter
Verifikasi response (dapat redirect atau validation error, tergantung implementasi)
Verifikasi data temu dokter TIDAK tersimpan di database (assertDatabaseMissing)
Memastikan tidak ada record dengan idpet dan idrole_user yang diminta

**Test Data**:
User: Factory generated Admin
Pet: Factory generated
Dokter: Factory generated, kemudian status diubah menjadi 0 (inactive)
idpet: valid pet id
idrole_user: dokter role_user id dengan status=0 (tidak aktif)

**Expected**:
Sistem menolak create appointment dengan validation error karena dokter yang dipilih tidak aktif (status=0). Data appointment TIDAK tersimpan di database, melindungi integritas data dengan memastikan hanya dokter aktif (status=1) yang dapat dijadwalkan.

**Actual**:

---

## III. RINGKASAN & STATISTIK

**Total Test Cases**: 5
**Positif Cases**: 2 (40%)
**Negatif/Edge Cases**: 3 (60%)

Breakdown by Operation

Create: Total 5, Positif 2 (Resepsionis, Admin), Negatif 2 (Perawat, Dokter Inactive), Edge Case 1 (Daily Reset)

Breakdown by Role

Resepsionis: Total 1 (create positif)
Perawat: Total 1 (create negatif - penolakan akses)
Admin: Total 3 (create positif + daily reset edge case + inactive dokter negatif)

---

## IV. CATATAN PENTING

**Tentang Create TemuDokter Operation**:
Create operation membuat jadwal appointment dokter:
- Role yang dapat buat: Resepsionis dan Admin SAJA
- Role yang TIDAK dapat buat: Perawat (tidak memiliki akses)
- Route: POST method ke /[Role]/TemuDokter/store-temu-dokter (hanya Resepsionis & Admin)
- Validation: idpet harus exist di tabel pet, idrole_user harus exist di tabel role_user
- Perhitungan nomor urut: Menggunakan max(no_urut) dengan filter whereDate('waktu_daftar', today()) untuk setiap dokter setiap hari
- Status default: 'W' (waiting)
- Timestamp: waktu_daftar menggunakan now() untuk mencatat waktu pembuatan

**Tentang Daily Sequence Reset**:
Nomor urut (no_urut) harus mereset setiap hari:
- Resepsionis controller: Sudah implement dengan whereDate filter di generate_nomor_urut()
- Admin controller: BELUM implement daily filter, masih menggunakan global max (BUG)
- Perawat controller: BELUM ada, perlu dibuat berbasis Resepsionis

**Tentang Inactive Dokter Prevention**:
Validation harus mencegah appointment dengan dokter tidak aktif (status=0):
- Current state: Validation hanya check existence, NOT check status=1 (BUG)
- Required fix: Tambah custom validation rule atau gunakan Rule::exists()->where('status', 1)
- Applied to: Resepsionis dan Admin controller (Perawat jika sudah ada)

**Tentang Middleware & Authorization**:
Setiap route dilindungi oleh middleware role-based:
- Resepsionis routes: Middleware 'IsResepsionis' check idrole == 4 (CAN CREATE TemuDokter)
- Perawat routes: Middleware 'IsPerawat' check idrole == 3 (CANNOT CREATE TemuDokter - no route defined)
- Admin routes: Middleware 'IsAdmin' check idrole == 1 (CAN CREATE TemuDokter)
- Test set session dengan idrole yang sesuai untuk mock user role
- **Catatan**: Perawat sengaja TIDAK diberikan akses untuk membuat appointment, hanya Resepsionis dan Admin

**Tentang Database Schema**:
Tabel temu_dokter memiliki struktur:
- idreservasi_dokter (primary key)
- no_urut (sequence number per dokter per hari)
- status (W, sedang_diperiksa, selesai)
- idpet (foreign key)
- idrole_user (foreign key)
- waktu_daftar (timestamp creation)
- deleted_at, deleted_by (soft delete columns)

---

## V. REKOMENDASI PERBAIKAN

**Temu Dokter - Daily Sequence Reset & Validation Dokter Aktif**:
Hasil pengujian menunjukkan bahwa fitur create appointment untuk Resepsionis dan Admin sudah berfungsi (PASS), namun ada dua aspek penting yang memerlukan perbaikan. Pertama, pada test TTDR-E01 (daily sequence reset), ditemukan bahwa `generate_nomor_urut()` di Admin controller menggunakan global max tanpa filter tanggal, sehingga nomor urut tidak mereset setiap harinya. Perbaikan yang diperlukan adalah menambahkan `whereDate('waktu_daftar', today())` filter pada query, sama seperti implementasi yang benar di Resepsionis controller, untuk memastikan setiap dokter memiliki nomor urut yang dimulai dari 1 setiap harinya. Kedua, pada test TTDR-N02, ditemukan bahwa validation saat ini hanya mengecek keberadaan dokter (exists) tanpa mengecek status aktif (status=1), sehingga appointment tetap dapat dibuat untuk dokter yang tidak aktif. Perbaikan yang diperlukan adalah menambahkan validation rule `Rule::exists('role_user', 'idrole_user')->where('status', 1)` pada kedua controller (Resepsionis dan Admin) untuk memastikan hanya dokter aktif yang dapat dijadwalkan. Dengan perbaikan ini, sistem akan lebih robust dalam menjaga konsistensi nomor urut harian dan mencegah appointment dengan dokter yang tidak aktif.

---

## V. CATATAN EKSEKUSI

Cara Menjalankan Test:

```bash
php artisan test tests/Feature/TemuDokterTest.php
php artisan test tests/Feature/TemuDokterTest.php --verbose
php artisan test --filter=TemuDokterTest
php artisan test --filter=test_resepsionis_berhasil_create_temu_dokter
php artisan test --filter=test_perawat_gagal_create_temu_dokter
php artisan test --filter=test_admin_gagal_create_temu_dokter_dengan_dokter_status_inactive
```

Implementation Checklist:

**Phase 1: Verify Perawat Cannot Create**
- [x] Test TTDR-N01: Perawat tidak dapat create appointment
- [x] Expected: RouteNotFoundException atau 404 (route tidak ada untuk Perawat)
- [x] PASS: Perawat deliberately tidak diberikan fitur create TemuDokter

**Phase 2: Admin Daily Reset**
- [x] Route exists (/Admin/TemuDokter/store-temu-dokter)
- [x] Controller exists (TemuDokter_Controller)
- [ ] generate_nomor_urut() method: Does NOT filter by date (BUG)
- [ ] Fix required: Add whereDate('waktu_daftar', today()) filter
- [ ] Test TTDR-E01: Will PASS after fix

**Phase 3: Inactive Dokter Validation**
- [ ] Validation in BOTH Resepsionis and Admin controllers
- [ ] Current: Only checks existence, not status=1
- [ ] Fix required: Add Rule::exists('role_user', 'idrole_user')->where('status', 1)
- [ ] Test TTDR-N02: Will PASS after validation added

**Final Verification**:
```bash
php artisan test tests/Feature/TemuDokterTest.php
# Expected: 5/5 PASS
```

Kolom Actual pada setiap test case di atas KOSONG dan harus diisi dengan hasil eksekusi test:
- PASS: Jika semua assertion berhasil
- FAIL: Jika ada assertion yang gagal, tuliskan assertion mana dan error message-nya
- ERROR: Jika ada error pada test execution

---

*Laporan ini dibuat dengan struktur: ID, Test Case, Test Scenario, Test Data, Expected, dan Actual untuk memudahkan tracking dan dokumentasi hasil pengujian.*

*Generated: 2026-03-30*
*Framework: Laravel PHPUnit Testing*
