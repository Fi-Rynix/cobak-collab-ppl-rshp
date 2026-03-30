# LAPORAN PENGUJIAN (TEST REPORT)
Rekam Medis Perawat - Create

**Tanggal**: 2026-03-14
**Framework**: Laravel PHPUnit
**Status**: Ready for Test Execution

---

## I. PENDAHULUAN

Laporan ini mendokumentasikan hasil pengujian fitur rekam medis pada role Perawat dalam aplikasi manajemen klinik hewan. Pengujian mencakup skenario positif untuk perawat melakukan create rekam medis, serta skenario negatif untuk keterbatasan akses role lain (dokter dan resepsionis) dan validasi data.

---

## II. PERAWAT REKAM MEDIS TEST

**File Test**: tests/Feature/PerawatRekamMedisTest.php
**Total Cases**: 4 (1 Positif + 3 Negatif)
**Coverage**: Create (1 Positif + 3 Negatif)

---

### Test Case 1

**ID**: TRPM-P01

**Test Case**: Perawat Berhasil Create Rekam Medis

**Test Scenario**:
Login sebagai Perawat
Create test TemuDokter
Create test KodeTindakanTerapi
Input data rekam medis dengan informasi yang valid (anamnesa, temuan_klinis, diagnosa, detail)
Submit form ke route Perawat.RekamMedis.store-rekam-medis
Verifikasi redirect ke halaman daftar rekam medis
Verifikasi pesan success muncul di session
Verifikasi data rekam medis tersimpan di tabel rekam_medis
Verifikasi detail rekam medis tersimpan di tabel detail_rekam_medis

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter
Anamnesa: Pemilik melaporkan hewan mengalami diare selama 3 hari
Temuan_klinis: Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan
Diagnosa: Gastroenteritis bacterial suspect
Detail: Pemberian antibiotik spektrum luas dan terapi cairan
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi

**Expected**:
Data rekam medis dan detail berhasil ditambahkan, sistem melakukan redirect ke halaman daftar dengan pesan sukses.

**Actual**:

---

### Test Case 2

**ID**: TRPM-N01

**Test Case**: Dokter Gagal Create Rekam Medis

**Test Scenario**:
Login sebagai Dokter
Create test TemuDokter
Create test KodeTindakanTerapi
Attempt submit form ke route Perawat.RekamMedis.store-rekam-medis
Verifikasi server mengembalikan HTTP Status 404 (Not Found)

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter
Anamnesa: Pemilik melaporkan hewan mengalami diare selama 3 hari
Temuan_klinis: Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan
Diagnosa: Gastroenteritis bacterial suspect
Detail: Pemberian antibiotik spektrum luas dan terapi cairan
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi

**Expected**:
Server mengembalikan HTTP Status 404 (Not Found) karena route perawat tidak dapat diakses oleh dokter.

**Actual**:

---

### Test Case 3

**ID**: TRPM-N02

**Test Case**: Resepsionis Gagal Create Rekam Medis

**Test Scenario**:
Login sebagai Resepsionis
Create test TemuDokter
Create test KodeTindakanTerapi
Attempt submit form ke route Perawat.RekamMedis.store-rekam-medis
Verifikasi server mengembalikan HTTP Status 404 (Not Found)

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter
Anamnesa: Pemilik melaporkan hewan mengalami diare selama 3 hari
Temuan_klinis: Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan
Diagnosa: Gastroenteritis bacterial suspect
Detail: Pemberian antibiotik spektrum luas dan terapi cairan
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi

**Expected**:
Server mengembalikan HTTP Status 404 (Not Found) karena route perawat tidak dapat diakses oleh resepsionis.

**Actual**:

---

### Test Case 4

**ID**: TRPM-N03

**Test Case**: Gagal Create Rekam Medis Dengan Anamnesa Kosong

**Test Scenario**:
Login sebagai Perawat
Create test TemuDokter dan KodeTindakanTerapi
Input data rekam medis dengan anamnesa kosong
Submit form
Verifikasi validation error pada field anamnesa
Verifikasi data tidak tersimpan di database

**Test Data**:
idreservasi_dokter: Factory generated TemuDokter
Anamnesa: "" (kosong)
Temuan_klinis: Terdapat lesi pada mukosa mulut, feses cair berwarna kecokelatan
Diagnosa: Gastroenteritis bacterial suspect
Detail: Pemberian antibiotik spektrum luas dan terapi cairan
idkode_tindakan_terapi: Factory generated KodeTindakanTerapi

**Expected**:
Data rekam medis dan detail gagal ditambahkan dan session menampilkan error validation pada field anamnesa karena wajib diisi.

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
Create operation melibatkan dua tabel:
- Memasukkan data ke tabel rekam_medis dengan field: idreservasi_dokter, anamnesa, temuan_klinis, diagnosa, dokter_pemeriksa
- Memasukkan data ke tabel detail_rekam_medis dengan field: idrekam_medis, idkode_tindakan_terapi, detail

**Tentang Authorization Test**:
Authorization test menggunakan middleware check dengan assertion status 403 untuk memastikan role yang tidak berhak tidak dapat mengakses route perawat.

**Validasi Fields**:
- anamnesa: required, string, max:1000
- temuan_klinis: required, string, max:1000
- diagnosa: required, string, max:1000
- detail: required, string, max:1000
- idkode_tindakan_terapi: required, exists di tabel kode_tindakan_terapi
- idreservasi_dokter: required, exists di tabel temu_dokter

---

## V. CATATAN EKSEKUSI

Cara Menjalankan Test:

```bash
php artisan test tests/Feature/PerawatRekamMedisTest.php
php artisan test tests/Feature/PerawatRekamMedisTest.php --verbose
php artisan test tests/Feature/PerawatRekamMedisTest.php --coverage
```

Kolom Actual pada setiap test case di atas KOSONG dan harus diisi dengan hasil eksekusi test:
PASS: Jika semua assertion berhasil
FAIL: Jika ada assertion yang gagal, tuliskan assertion mana dan error message-nya
ERROR: Jika ada error pada test execution

---

*Laporan ini dibuat dengan struktur: ID, Test Case, Test Scenario, Test Data, Expected, dan Actual untuk memudahkan tracking dan dokumentasi hasil pengujian.*

*Generated: 2026-03-14*
*Framework: Laravel PHPUnit Testing*
