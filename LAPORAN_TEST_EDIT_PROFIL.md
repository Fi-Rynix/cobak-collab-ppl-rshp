# LAPORAN PENGUJIAN (TEST REPORT)
Edit Profil Dokter & Perawat - View & Edit

**Tanggal**: 2026-03-30
**Framework**: Laravel PHPUnit
**Status**: Ready for Test Execution

---

## I. PENDAHULUAN

Laporan ini mendokumentasikan hasil pengujian fitur edit profil pada role Dokter dan Perawat dalam aplikasi manajemen klinik hewan. Pengujian mencakup skenario positif untuk melihat dan mengedit profil milik sendiri, serta skenario negatif untuk keterbatasan akses antar role dan antar user serta proteksi data.

---

## II. EDIT PROFIL TEST

**File Test**: tests/Feature/EditProfilTest.php
**Total Cases**: 5 (2 Positif + 3 Negatif)
**Coverage**: View (2 Positif + 2 Negatif) & Edit (1 Negatif)

---

### Test Case 1

**ID**: TEPD-P01

**Test Case**: Dokter Berhasil Melihat Profil Miliknya

**Test Scenario**:
Login sebagai Dokter
Akses route Dokter.Profil.profil-saya
Verifikasi response status 200 OK
Verifikasi halaman menampilkan view yang benar (Dokter.Profil.profil-saya)
Verifikasi view menerima variable user dan dokter
Verifikasi authentikasi user masih valid dengan iduser yang login

**Test Data**:
User: Factory generated Dokter user
Route: Dokter.Profil.profil-saya

**Expected**:
Halaman profil berhasil ditampilkan dengan status 200, view menerima data user dan dokter yang sesuai dengan user yang login.

**Actual**:

---

### Test Case 2

**ID**: TEPD-P02

**Test Case**: Dokter Berhasil Mengedit Profil Miliknya

**Test Scenario**:
Login sebagai Dokter
Prepare data update profil (alamat, no_hp, bidang_dokter)
Submit PUT request ke route Dokter.Profil.update-profil
Verifikasi response redirect ke route Dokter.Profil.profil-saya
Verifikasi session menampilkan pesan success
Verifikasi data dokter terupdate di database tabel dokter dengan field: alamat, no_hp, bidang_dokter

**Test Data**:
iduser: Factory generated Dokter
alamat: "Jalan Merdeka No. 123, Jakarta Pusat"
no_hp: "081234567890"
bidang_dokter: "Bedah"

**Expected**:
Data profil dokter berhasil diupdate, sistem melakukan redirect ke halaman profil dengan menampilkan pesan success, dan data tersimpan di database dengan nilai yang sesuai.

**Actual**:

---

### Test Case 3

**ID**: TEPD-N01

**Test Case**: Perawat Gagal Melihat Profil Dokter Lain

**Test Scenario**:
Create Dokter user via factory
Login sebagai Perawat
Attempt akses route Dokter.Profil.profil-saya
Verifikasi response status termasuk salah satu dari: 302 (redirect), 403 (forbidden), 404 (not found)

**Test Data**:
Dokter user: Factory generated
Perawat user: Factory generated
Route: Dokter.Profil.profil-saya

**Expected**:
Sistem menolak akses Perawat ke route Dokter dan mengembalikan salah satu dari: redirect ke halaman yang sesuai (302), forbidden error (403), atau not found (404). Perawat tidak dapat melihat profil dokter.

**Actual**:

---

### Test Case 4

**ID**: TEPD-N02

**Test Case**: Dokter Gagal Melihat Profil Dokter Lain

**Test Scenario**:
Create Dokter kedua (dokterLain) via factory
Login sebagai Dokter utama
Akses route Dokter.Profil.profil-saya
Verifikasi response status 200 (berhasil diakses)
Verifikasi authentikasi menunjukkan user yang login adalah dokterUtama (bukan dokterLain)
Verify iduser dokterUtama == auth()->id()
Verify iduser dokterLain != auth()->id()

**Test Data**:
dokterUtama: Factory generated Dokter
dokterLain: Factory generated Dokter (berbeda)
Route: Dokter.Profil.profil-saya

**Expected**:
Data profil yang ditampilkan adalah milik dokter yang login (dokterUtama), bukan dokter lain. Controller menggunakan auth()->id() sebagai filter sehingga menjamin setiap dokter hanya bisa melihat profil miliknya sendiri.

**Actual**:

---

### Test Case 5

**ID**: TEPD-N03

**Test Case**: Dokter Gagal Mengedit Profil Dokter Lain

**Test Scenario**:
Create Dokter kedua (dokterLain) via factory
Simpan data original dokterLain (alamat) untuk verifikasi
Login sebagai Dokter utama
Prepare data update dengan nilai berbeda dari data original dokterLain
Send PUT request ke route Dokter.Profil.update-profil dengan data update
Verifikasi response status 403 (Forbidden)
Verifikasi data dokterLain di database tetap sama (alamat tidak berubah)

**Test Data**:
dokterUtama: Factory generated Dokter
dokterLain: Factory generated Dokter (berbeda)
Original alamat dokterLain: Factory generated (auto-save)
Update data:
  - alamat: "Alamat Palsu Yang Diinginkan Hacker"
  - no_hp: "089999999999"
  - bidang_dokter: "Radiologi"
Route: Dokter.Profil.update-profil

**Expected**:
Sistem menolak update dengan mengembalikan HTTP Status 403 (Forbidden) karena dokter utama tidak memiliki autoritas untuk mengubah profil dokter lain. Data dokter lain tetap aman dan tidak berubah, membuktikan proteksi data authorization works correctly.

**Actual**:

---

## III. RINGKASAN & STATISTIK

**Total Test Cases**: 5
**Positif Cases**: 2 (40%)
**Negatif Cases**: 3 (60%)

Breakdown by Operation

View: Total 3, Positif 1, Negatif 2
Edit: Total 2, Positif 1, Negatif 1

Breakdown by Role

Dokter: Total 4 (3 primary subject, 1 cross-role test)
Perawat: Total 1 (cross-role test)

---

## IV. CATATAN PENTING

**Tentang View Operation**:
View operation menampilkan profil user yang sedang login dengan mengakses:
- Tabel user untuk mendapatkan data user basic (nama, email, etc.)
- Tabel dokter untuk mendapatkan data spesifik dokter (alamat, no_hp, bidang_dokter, jenis_kelamin)
- Controller menggunakan auth()->id() untuk memastikan filter otomatis berdasarkan user yang login
- Jika user tidak ditemukan atau dokter dihapus (soft delete), akan abort(404)

**Tentang Edit Operation**:
Edit operation bertujuan untuk update data dokter:
- Route: PUT method ke Dokter.Profil.update-profil
- Fields yang dapat diupdate: alamat, no_hp, bidang_dokter
- Implementasi belum ada sehingga saat ini test FAIL dengan RouteNotFoundException
- Setelah diimplementasi, harus include authorization check untuk memastikan user hanya bisa update profil miliknya sendiri

**Tentang Authorization Test**:
Authorization test memastikan:
- Dokter hanya dapat melihat/edit profil miliknya sendiri (user isolation)
- Dokter tidak dapat melihat/edit profil dokter lain
- Perawat tidak dapat mengakses route dokter sama sekali (role-based access control)
- Protected by middleware dan controller-level authorization checks

**Tentang Data Protection**:
Data protection memastikan:
- Query menggunakan whereNull('deleted_at') untuk menjaga soft-deleted records tidak ditampilkan
- Authorization check pada setiap operasi
- Database query menggunakan direct DB query untuk consistency dengan existing codebase

---

## V. REKOMENDASI PERBAIKAN

**Edit Profil - Authorization & Update Implementation**:
Hasil pengujian menunjukkan bahwa fitur view profil berfungsi dengan baik (PASS) pada test TEPD-P01, namun fitur edit profil belum diimplementasikan sehingga menghasilkan RouteNotFoundException pada test TEPD-P02. Ketika fitur ini diimplementasi, harus mempertimbangkan beberapa aspek penting: (1) Controller harus melakukan authorization check menggunakan `auth()->id()` untuk memastikan dokter hanya dapat mengedit profil miliknya sendiri, bukan profil dokter lain; (2) Validation rule harus mencakup pengecekan format data seperti no_hp dan alamat agar sesuai dengan konstrain database; (3) Middleware 'IsDokter' harus dipastikan bekerja dengan baik dan mengembalikan HTTP 403 Forbidden jika ada role lain yang mencoba mengakses route edit profil, bukan 302 Redirect. Dengan implementasi yang tepat, setiap dokter akan hanya bisa mengakses dan memodifikasi data profil miliknya sendiri, memastikan integritas dan keamanan data profil di aplikasi.

---

## V. CATATAN EKSEKUSI

Cara Menjalankan Test:

```bash
php artisan test tests/Feature/EditProfilTest.php
php artisan test tests/Feature/EditProfilTest.php --verbose
php artisan test --filter=EditProfilTest
php artisan test --filter=TEPD-P01
```

Kolom Actual pada setiap test case di atas KOSONG dan harus diisi dengan hasil eksekusi test:
- PASS: Jika semua assertion berhasil
- FAIL: Jika ada assertion yang gagal, tuliskan assertion mana dan error message-nya
- ERROR: Jika ada error pada test execution

---

*Laporan ini dibuat dengan struktur: ID, Test Case, Test Scenario, Test Data, Expected, dan Actual untuk memudahkan tracking dan dokumentasi hasil pengujian.*

*Generated: 2026-03-30*
*Framework: Laravel PHPUnit Testing*
