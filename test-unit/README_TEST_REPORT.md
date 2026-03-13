# Laporan Pengujian Unit (Unit Test Report)

## 1. Total Test Case
Terdapat **26 Test Cases** secara keseluruhan yang telah dieksekusi melalui PHPUnit untuk fitur **Edit Profile**, **Patient Registration**, dan **User Authentication/Role**.

## 2. Positive Test Case Scenario

| ID | Deskripsi | Langkah-Langkah | Data Uji | Hasil yang diharapkan |
| -- | --------- | --------------- | -------- | --------------------- |
| TC-P1 | Edit profil oleh admin | Login admin -> Ke halaman edit user -> Submit perubahan nama dan email | Nama = "Admin Updated Name", Email = "testemail01@test.com" | Data profil berhasil diperbarui, session menampilkan success |
| TC-P2 | Edit profil dokter oleh admin | Login admin -> Ke halaman edit dokter -> Submit perubahan profil | Nama = "Doctor Updated By Admin", Email = "testemail02@test.com" | Data profil dokter berhasil diperbarui |
| TC-P3 | Edit profil resepsionis oleh admin | Login admin -> Ke halaman edit resepsionis -> Submit perubahan profil | Nama = "Receptionist Updated", Email = "testemail03@test.com" | Data profil resepsionis berhasil diperbarui |
| TC-P4 | Reset/Update password valid | Login admin -> Pilih user -> Klik tombol reset password | (Tidak ada, mengenerate "123456" otomatis) | Password berubah, user dapat menggunakan password bawaan baru |
| TC-P5 | Ubah email profil | Login admin -> Edit user -> Masukkan email baru format benar | Email = "testemail04@test.com" | Email berhasil diubah ke dalam database |
| TC-P6 | Registrasi pasien oleh admin | Login admin -> Tambah pemilik baru -> Tambah data pet baru | Nama Pemilik = "John Doe Pemilik", Nama Pet = "Fluffy" | Pemilik dan Pasien (Pet) berhasil tersimpan di DB |
| TC-P7 | Registrasi pasien oleh resepsionis | Login resepsionis -> Tambah pemilik baru -> Tambah data pet baru | Nama Pemilik = "Jane Doe Pemilik", Nama Pet = "Snowball" | Pemilik dan Pasien (Pet) berhasil tersimpan di DB |

---

## 3. Negative Test Case Scenario

| ID | Deskripsi | Langkah-Langkah | Data Uji | Hasil yang diharapkan |
| -- | --------- | --------------- | -------- | --------------------- |
| TC-N1 | Kolom wajib edit profil kosong | Login admin -> Edit user -> Kosongkan form nama/email -> Submit | Nama = "", Email = "" | Gagal disimpan, mengembalikan pesan validasi "Nama wajib diisi" |
| TC-N2 | Format email tidak valid saat edit | Login -> Form Edit User -> Input email salah -> Submit | Email = "invalid-email-format" | Gagal disimpan, mengembalikan pesan validasi email |
| TC-N3 | Duplikasi email user | Login -> Form Edit User -> Input email milik profil lain -> Submit | Email = email user lain di database | Gagal disimpan, mengembalikan pesan error unique email / 500 error |
| TC-N4 | Input panjang ilegal edit profil | Login -> Form Edit -> Input nama sangat panjang -> Submit | karakter 'a' x505 | Gagal disimpan, memunculkan pesan error max character |
| TC-N5 | XSS Injection pada input form | Login -> Form Edit/Registrasi -> Input kode JS pada field nama | Nama = `<script>alert('xss')</script>`| Sistem membersihkan input / menolak script |
| TC-N6 | Role tidak memiliki akses edit | Login dengan role Dokter -> Akses endpoint Admin (put update-user) | (Data sama seperti TC-P1) | HTTP 403 / Redirect akses ditolak (Unauthorized) |
| TC-N7 | Registrasi pasien field wajib kosong | Login admin -> Registrasi Pemilik -> Kosongkan semua data | Nama = "", No WA = "" | Gagal divalidasi, tampil pesan field harus diisi |
| TC-N8 | Nomor telepon tidak valid | Login admin -> Registrasi Pemilik -> Input String / format salah | No_WA = "not-a-number" | Gagal divalidasi, nomor hp harus berupa angka numerik |
| TC-N9 | Format tanggal salah | Login admin -> Registrasi Pet -> Form tanggal lahir salah | Tanggal = "invalid-date" | Gagal divalidasi, tidak memproses pembuatan record |
| TC-N10| SQL injection bypass | Login admin -> Insert ke DB | Payload = `Drop' OR '1'='1` | Eloquent secara native menangani escaping string |

---

## 4. Testing Summary

- **Total Test Case:** 26
- **Passed Tests:** 8
- **Failed Tests:** 18

Sebagian besar tes skenario positif **Error / Failed** murni karena adanya kesalahan (bug) pada level validasi Controller bawaan (bukan efek samping duplikasi tes beruntun).

---

## 5. Bugs Found

Beberapa masalah fungsionalitas utama yang terdeteksi di dalam core code aplikasi Laravel:
1. **Validasi Panjang Karakter (max) Ekstrem Pendek pada `User_Controller`:**
Pada fungsi `validate_user`, panjang maksimal nama `max:2` (bug logic/typo): 
`'nama' => ['required', 'string', 'max:2', 'min:1']`.
Akibatnya semua input valid untuk mengubah profile secara otomatis ditolak karena melewati 2 huruf.
2. **Duplikasi Data Tidak Tertangani dengan Baik atau Unique Rule:**
Rule untuk duplikasi di `User_Controller` tidak menyertakan constraint `unique:user,email`, yang mengakibatkan kegagalan database constraint (500 Error DB Exception) alih-alih me-return error session validation ketika input email duplikat.
3. **Redireksi/Intervensi Validasi Null:**
Pada modul pembuatan Pasien, terdapat beberapa error terkait foreign ID yang mem-bypass flow validasi atau properti user belum dicreate secara proper karena session validation fail lebih dulu.

---

## 6. Security Vulnerabilities

1. **XSS (Cross-Site Scripting) Stored:**
Aplikasi meng-insert field `nama` / form bebas teks tanpa melalui disinfeksi/pembersihan payload. Skrip `<script>` berhasil tersimpan di level database dan jika view blade `{!! format !!}` merender value ini (terutama di datatables), bisa memicu **Stored XSS**.
2. **Validation Bypass (Missing Validation):**
Form "Update User" tidak mengecek duplikasi `email`. Pengguna bisa menembak API (PUT Request) yang mengarah mengakibatkan internal sistem DB constraint gagal dan ter-expose (Broken Access / Info Leak 500 Route).
3. **SQL Injection - Native Escape Works:**
Eloquent / Query Builder Laravel berhasil memitigasi serang SQL injection secara asali karena binding PDO native. Parameter aman.

---

## 7. Recommendations

1. **Perbaikan Kode Validasi:** Lakukan perbaikan secepatnya di file `app/Http/Controllers/Admin/User_Controller.php` pada validasi atribut nama menjadi batas yang proporsional seperti `max:255` dibandingkan `max:2`.
2. **Tambahkan Fitur Unique Constraint Validation:** Tambahkan constraint `unique:users,email,{id}` kepada semua form yang melakukan pengecekan data di profil milik masing-masing agar tak trigger Exception 500 DB.
3. **Sanitasi HTML Entities:** Pastikan semua string yang dimasukkan dilakukan standarisasi HTML Entities via input middleware, atau memakai fungsi `htmlspecialchars()` sebagai pelapis extra.
4. **Perkuat Route Middleware:** Middleware `IsAdmin`, `IsResepsionis`, `IsDokter` secara global sudah berjalan baik, tetap pertahankan agar role tidak saling overlap (Broken Access Control teratasi).
