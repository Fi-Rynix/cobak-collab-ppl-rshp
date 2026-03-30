# LAPORAN PENGUJIAN (TEST REPORT)
Detail Rekam Medis Dokter - Create

**Tanggal**: 2026-03-14
**Framework**: Laravel PHPUnit
**Status**: Ready for Test Execution

---

## I. PENDAHULUAN

Laporan ini mendokumentasikan hasil pengujian fitur detail rekam medis pada role Dokter dalam aplikasi manajemen klinik hewan. Pengujian mencakup skenario positif untuk dokter menambahkan detail tindakan ke rekam medis, serta skenario negatif untuk keterbatasan akses (dokumentasi rekam medis adalah tanggung jawab perawat, dokter hanya dapat menambah detail pada rekam medis miliknya sendiri) dan validasi data.

---

## II. DOKTER DETAIL REKAM MEDIS TEST

**File Test**: tests/Feature/DokterDetailRekamMedisTest.php
**Total Cases**: 4 (1 Positif + 3 Negatif)
**Coverage**: Create (1 Positif + 3 Negatif)

---

### Test Case 1

**ID**: TRDM-P01

**Test Case**: Dokter Berhasil Tambah Detail Rekam Medis

**Test Scenario**:
Login sebagai Dokter
Create test KodeTindakanTerapi
Create test RekamMedis dengan dokter_pemeriksa sesuai dokter yang login
Input data detail rekam medis dengan informasi yang valid (idkode_tindakan_terapi, detail)
Submit form ke route Dokter.RekamMedis.store-detail
Verifikasi redirect ke halaman detail rekam medis
Verifikasi pesan success muncul di session
Verifikasi data detail rekam medis tersimpan di tabel detail_rekam_medis

**Test Data**:
idrekam_medis: RekamMedis yang dibuat dengan dokter_pemeriksa sesuai dokter yang login
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi
Detail: Injeksi antibiotik diberikan intramuscular

**Expected**:
Data detail rekam medis berhasil ditambahkan, sistem melakukan redirect ke halaman detail dengan pesan sukses.

**Actual**:

---

### Test Case 2

**ID**: TRDM-N01

**Test Case**: Dokter Gagal Menambah Rekam Medis (Karena Rekam Medis Otoritas Perawat)

**Test Scenario**:
Login sebagai Dokter
Create test TemuDokter
Create test KodeTindakanTerapi
Attempt submit form ke route Dokter.RekamMedis.store-rekam-medis (route yang tidak terdaftar)
Verifikasi sistem melempar RouteNotFoundException

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter
Anamnesa: Pemilik melaporkan hewan mengalami diare selama 3 hari
Temuan_klinis: Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan
Diagnosa: Gastroenteritis bacterial suspect
Detail: Pemberian antibiotik spektrum luas dan terapi cairan
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi

**Expected**:
Sistem melempar RouteNotFoundException karena route dokter untuk create rekam medis tidak terdaftar, memastikan dokter tidak dapat membuat rekam medis.

**Actual**:

---

### Test Case 3

**ID**: TRDM-N02

**Test Case**: Dokter Gagal Menambah Detail Rekam Medis Yang Rekam Medis Bukan Atas Nama Dirinya

**Test Scenario**:
Login sebagai Dokter A
Create test KodeTindakanTerapi
Create RekamMedis dengan dokter_pemeriksa dari Dokter B (dokter lain)
Attempt submit detail ke route Dokter.RekamMedis.store-detail
Verifikasi redirect kembali
Verifikasi error message muncul di session bahwa user tidak memiliki akses
Verifikasi data detail tidak tersimpan di database

**Test Data**:
idrekam_medis: RekamMedis yang dibuat dengan dokter_pemeriksa dari Dokter B
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi
Detail: Injeksi antibiotik diberikan intramuscular

**Expected**:
Server mengembalikan error message "Anda tidak memiliki akses untuk menambah tindakan." karena rekam medis bukan milik dokter yang login, data tidak tersimpan.

**Actual**:

---

### Test Case 4

**ID**: TRDM-N03

**Test Case**: Gagal Create Detail Rekam Medis Dengan Detail Kosong

**Test Scenario**:
Login sebagai Dokter
Create test KodeTindakanTerapi
Create test RekamMedis dengan dokter_pemeriksa sesuai dokter yang login
Input data detail dengan field detail kosong
Submit form
Verifikasi validation error pada field detail
Verifikasi data tidak tersimpan di database

**Test Data**:
idrekam_medis: RekamMedis yang dibuat dengan dokter_pemeriksa sesuai dokter yang login
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi
Detail: "" (kosong)

**Expected**:
Data detail rekam medis gagal ditambahkan dan session menampilkan error validation pada field detail karena wajib diisi.

**Actual**:

---

## III. RINGKASAN & STATISTIK

**Total Test Cases**: 4
**Positif Cases**: 1 (25%)
**Negatif Cases**: 3 (75%)

Breakdown by Operation

Create: Total 4, Positif 1, Negatif 3

---

## IV. CATATAN PENTING

**Tentang Create Operation**:
Create detail operation menambahkan record ke tabel detail_rekam_medis dengan field:
- idrekam_medis: identifier rekam medis yang sudah ada
- idkode_tindakan_terapi: tindakan/terapi yang diberikan
- detail: deskripsi detail tindakan/terapi

**Tentang Authorization Check**:
Authorization check memastikan bahwa:
- Hanya dokter yang mendokumentasikan rekam medis yang dapat menambah detail tindakan
- Dokter tidak dapat membuat/mengedit rekam medis (itu adalah tanggung jawab perawat), hanya menambah detail tindakan yang diberikan

**Tentang Validasi Fields**:
- idkode_tindakan_terapi: required, must exist di tabel kode_tindakan_terapi
- detail: required, string, max:1000

---

## V. CATATAN EKSEKUSI

Cara Menjalankan Test:

```bash
php artisan test tests/Feature/DokterDetailRekamMedisTest.php
php artisan test tests/Feature/DokterDetailRekamMedisTest.php --verbose
php artisan test tests/Feature/DokterDetailRekamMedisTest.php --coverage
```

Kolom Actual pada setiap test case di atas KOSONG dan harus diisi dengan hasil eksekusi test:
PASS: Jika semua assertion berhasil
FAIL: Jika ada assertion yang gagal, tuliskan assertion mana dan error message-nya
ERROR: Jika ada error pada test execution

---

*Laporan ini dibuat dengan struktur: ID, Test Case, Test Scenario, Test Data, Expected, dan Actual untuk memudahkan tracking dan dokumentasi hasil pengujian.*

*Generated: 2026-03-14*
*Framework: Laravel PHPUnit Testing*
