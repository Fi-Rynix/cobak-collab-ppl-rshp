# Panduan Menjalankan Unit Test Laravel

## 1. Laravel Testing Overview
Proyek ini menggunakan bawaan PHPUnit dari Laravel untuk menjalankan pengujian otomatis pada unit dan fitur. Konfigurasi tersimpan dalam file `phpunit.xml`.
Testing memanfaatkan sistem environment test, pastikan database sesuai file `.env` sudah siap sebelum eksekusi (seperti `test_kuliahwf`).

## 2. Commands to Run Tests

**Menjalankan Seluruh Testing:**
\```bash
php artisan test
\```

**Menjalankan Spesifik Unit Test:**
\```bash
php artisan test tests/Unit/EditProfileTest.php
\```
\```bash
php artisan test tests/Unit/PatientRegistrationTest.php
\```
\```bash
php artisan test tests/Unit/UserTest.php
\```

## 3. How to Read Test Results

Setelah menjalankan command testing, terminal akan memberikan tabel berdurasi dan warna:
* **✅ PASS results (Hijau):** Menandakan sebuah skenario testing berhasil terpenuhi (sukses dijalankan, mengembalikan expected output / behavior).
* **❌ FAIL results (Merah):** Menandakan bahwa testing gagal terverifikasi sesuai ekspektasi.
* **Assertion errors:** Output teks di bagian bawah layar akan mendeskripsikan secara jelas apa yang salah. Misalnya `Session is missing expected key [success]` atau `Failed asserting that false is true`. Periksa baris file beserta kodenya di log error.

## 4. How to Add New Test Cases

Untuk membuat Test Case baru, gunakan artisan command:

\```bash
php artisan make:test ExampleTest --unit
\```

File akan digenerate otomatis ke folder `tests/Unit/`. Anda dapat mulai menambahkan fungsi pengujian di dalamnya (metode yang dites **harus** diawali dengan nama fungsi `test_`).

## 5. Database Testing Notes

- Pastikan Laravel memiliki user `root` (atau kredensial DB sesuai Laragon `.env` anda) dengan akses ke database utama.
- Saat pengujian, baris dummy dimasukkan ke database dan kami menggunakan cleanup mandiri di setup method (`$this->cleanupUser()` pada skrip).
- Anda juga dapat memakai trait `RefreshDatabase` atau `DatabaseTransactions` milik Laravel agar setelah tiap test, DB secara otomatis ter-rollback tanpa menyimpan sisa sampah. Saat ini implementasi menggunakan pendekatan penghapusan baris data mandiri di akhir setiap skenario.
