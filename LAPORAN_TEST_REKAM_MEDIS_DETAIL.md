# LAPORAN PENGUJIAN (TEST REPORT)
Rekam Medis Admin - Create, Edit, Delete

**Tanggal**: 2026-03-14
**Framework**: Laravel PHPUnit
**Status**: Ready for Test Execution

---

## I. PENDAHULUAN

Laporan ini mendokumentasikan hasil pengujian fitur rekam medis (create, edit, delete) pada aplikasi manajemen klinik hewan. Pengujian mencakup skenario positif dan negatif untuk memastikan CRUD operation pada rekam medis dan detail rekam medis berfungsi dengan baik sesuai requirement.

---

## II. ADMIN REKAM MEDIS DETAIL TEST

**File Test**: tests/Feature/AdminRekamMedisDetailTest.php
**Total Cases**: 6 (3 Positif + 3 Negatif)
**Coverage**: Create (2), Edit (2), Delete (2)

---

### Test Case 1

**ID**: TRKM-P01

**Test Case**: Berhasil Create Rekam Medis Dan Detail

**Test Scenario**:
Login sebagai Admin
Create test TemuDokter dengan status W (waiting)
Create test KodeTindakanTerapi
Input data rekam medis dengan informasi yang valid (anamnesa, temuan_klinis, diagnosa, detail)
Submit form ke route Admin.RekamMedis.store-rekam-medis
Verifikasi redirect ke halaman daftar rekam medis
Verifikasi pesan success muncul di session
Verifikasi data rekam medis tersimpan di tabel rekam_medis
Verifikasi detail rekam medis tersimpan di tabel detail_rekam_medis
Verifikasi status TemuDokter berubah menjadi D (done)

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter dengan status W
Anamnesa: Hewan menunjukkan gejala demam tinggi dan lesu
Temuan_klinis: Suhu tubuh 39.5°C, mata merah, nafsu makan berkurang
Diagnosa: Suspek demam berdarah akibat virus
Detail: Injeksi cairan infus, pemberian vitamin, dan monitor ketat
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi

**Expected**:
Data rekam medis dan detail berhasil ditambahkan, sistem melakukan redirect ke halaman daftar dengan pesan sukses, dan status TemuDokter berubah dari W menjadi D (done).

**Actual**:

---

### Test Case 2

**ID**: TRKM-N01

**Test Case**: Gagal Create Rekam Medis Dan Detail Anamnesa Kosong

**Test Scenario**:
Login sebagai Admin
Create test TemuDokter dan KodeTindakanTerapi
Input data rekam medis dengan anamnesa kosong
Submit form
Verifikasi validation error pada field anamnesa
Verifikasi data tidak tersimpan di database

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter
Anamnesa: "" (kosong)
Temuan_klinis: Suhu tubuh 39.5°C, mata merah, nafsu makan berkurang
Diagnosa: Suspek demam berdarah akibat virus
Detail: Injeksi cairan infus, pemberian vitamin, dan monitor ketat
idkode_tindakan_terapi: Factory generated

**Expected**:
Data rekam medis dan detail gagal ditambahkan dan session menampilkan error validation pada field anamnesa karena wajib diisi.

**Actual**:

---

### Test Case 3

**ID**: TRKM-P02

**Test Case**: Berhasil Edit Rekam Medis Dan Detail

**Test Scenario**:
Login sebagai Admin
Create test rekam medis dengan data lama
Create test detail rekam medis dengan data lama dan KodeTindakan1
Create test KodeTindakan2 untuk update
Submit form update dengan data baru
Verifikasi redirect ke halaman daftar rekam medis
Verifikasi pesan success muncul
Verifikasi data rekam medis terupdate di tabel (anamnesa, temuan_klinis, diagnosa)
Verifikasi detail rekam medis terupdate di tabel (detail, idkode_tindakan_terapi dengan nilai baru)

**Test Data**:
idrekam_medis: Factory generated
Anamnesa: Anamnesa lama → Anamnesa baru - hewan menunjukkan perbaikan
Temuan_klinis: Temuan lama → Temuan baru - suhu normal, mata cerah
Diagnosa: Diagnosa lama → Diagnosa baru - pemulihan baik
Detail: Detail lama → Detail baru - lanjutkan antibiotik
idkode_tindakan_terapi: KodeTindakan1 (lama) → KodeTindakan2 (baru)

**Expected**:
Data rekam medis dan detail berhasil diupdate, sistem melakukan redirect ke halaman daftar dengan pesan sukses, dan semua field yang lama terubah menjadi yang baru.

**Actual**:

---

### Test Case 4

**ID**: TRKM-N02

**Test Case**: Gagal Edit Rekam Medis Dan Detail Diagnosa Kosong

**Test Scenario**:
Login sebagai Admin
Create test rekam medis dan detail
Input data update dengan diagnosa kosong
Submit form
Verifikasi validation error pada field diagnosa

**Test Data**:
idrekam_medis: Factory generated
Anamnesa: Anamnesa update
Temuan_klinis: Temuan update
Diagnosa: "" (kosong)
Detail: Detail update
idkode_tindakan_terapi: Factory generated

**Expected**:
Data rekam medis dan detail gagal diupdate dan session menampilkan error validation pada field diagnosa karena wajib diisi.

**Actual**:

---

### Test Case 5

**ID**: TRKM-P03

**Test Case**: Berhasil Delete Rekam Medis Dan Detail

**Test Scenario**:
Login sebagai Admin
Create test TemuDokter dengan status D (done)
Create test rekam medis yang terkait dengan TemuDokter
Create test detail rekam medis
Send delete request ke route Admin.RekamMedis.delete-rekam-medis
Verifikasi redirect ke halaman daftar rekam medis
Verifikasi pesan success muncul
Verifikasi rekam medis di soft delete (deleted_at not null)
Verifikasi detail rekam medis di soft delete (deleted_at not null)
Verifikasi status TemuDokter berubah kembali menjadi W (waiting)

**Test Data**:
idrekam_medis: Factory generated
idreservasi_dokter: Factory generated TemuDokter dengan status D

**Expected**:
Rekam medis dan detail berhasil dihapus (soft delete), sistem melakukan redirect ke halaman daftar dengan pesan sukses, dan status TemuDokter berubah kembali dari D menjadi W (waiting).

**Actual**:

---

### Test Case 6

**ID**: TRKM-N03

**Test Case**: Gagal Delete Rekam Medis Dan Detail

**Test Scenario**:
Login sebagai Admin
Send delete request dengan idrekam_medis yang tidak ada di database (ID 999)
Verifikasi server mengembalikan HTTP Status 404
Verifikasi tidak ada data yang dihapus

**Test Data**:
idrekam_medis: 999 (tidak ada di database)

**Expected**:
Server mengembalikan HTTP Status 404 (Not Found) karena rekam medis tidak ditemukan di database.

**Actual**:

---

## III. RINGKASAN & STATISTIK

**Total Test Cases**: 6
**Positif Cases**: 3 (50%)
**Negatif Cases**: 3 (50%)

Breakdown by Operation

Create: Total 2, Positif 1, Negatif 1
Edit: Total 2, Positif 1, Negatif 1
Delete: Total 2, Positif 1, Negatif 1

---

## IV. CATATAN PENTING

**Tentang Create Operation**:
Create operation melibatkan dua tabel:
- Memasukkan data ke tabel rekam_medis dengan field: idreservasi_dokter, anamnesa, temuan_klinis, diagnosa, dokter_pemeriksa
- Memasukkan data ke tabel detail_rekam_medis dengan field: idrekam_medis, idkode_tindakan_terapi, detail
- Update status TemuDokter dari W (waiting) menjadi D (done)

**Tentang Update Operation**:
Update operation memperbarui dua tabel:
- Update tabel rekam_medis dengan field: anamnesa, temuan_klinis, diagnosa
- Update tabel detail_rekam_medis dengan field: idkode_tindakan_terapi, detail

**Tentang Delete Operation**:
Delete dapat gagal dalam skenario:
- RekamMedis tidak ditemukan (HTTP 404) akan throw exception
- Jika ditemukan, akan melakukan soft delete yang melibatkan:
  - Soft delete detail_rekam_medis dengan set deleted_at dan deleted_by
  - Update TemuDokter status kembali ke W (waiting)
  - Soft delete rekam_medis dengan set deleted_at dan deleted_by

**Validasi Fields**:
- anamnesa: required, string, max:1000
- temuan_klinis: required, string, max:1000
- diagnosa: required, string, max:1000
- detail: required, string, max:1000
- idkode_tindakan_terapi: required, exists di tabel kode_tindakan_terapi
- idreservasi_dokter: required, exists di tabel temu_dokter (hanya untuk create)

---

## V. CATATAN EKSEKUSI

Cara Menjalankan Test:

```bash
php artisan test tests/Feature/AdminRekamMedisDetailTest.php
php artisan test tests/Feature/AdminRekamMedisDetailTest.php --verbose
php artisan test tests/Feature/AdminRekamMedisDetailTest.php --coverage
```

Kolom Actual pada setiap test case di atas KOSONG dan harus diisi dengan hasil eksekusi test:
PASS: Jika semua assertion berhasil
FAIL: Jika ada assertion yang gagal, tuliskan assertion mana dan error message-nya
ERROR: Jika ada error pada test execution

---

*Laporan ini dibuat dengan struktur: ID, Test Case, Test Scenario, Test Data, Expected, dan Actual untuk memudahkan tracking dan dokumentasi hasil pengujian.*

*Generated: 2026-03-14*
*Framework: Laravel PHPUnit Testing*
