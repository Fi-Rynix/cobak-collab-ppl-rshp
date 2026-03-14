# 📄 Raw Logs — PHPUnit Test Output

**Command:** `php artisan test`  
**Tanggal:** 14 Maret 2026  
**Branch:** `test-unit-udin`  
**CWD:** `c:\my filess dudeee\Tugas Kuliah\Semester 4\PPL\cobak-collab-ppl-rshp`

---

```
ZaganJade@ZaganJade MINGW64 /c/my filess dudeee/Tugas Kuliah/Semester 4/PPL/cobak-collab-ppl-rshp (test-unit-udin)
$ php artisan test

   PASS  Tests\Unit\EditProfileTest
  ✓ admin profile update                                                                                                                                 0.55s
  ✓ admin update doctor profile                                                                                                                          0.05s
  ✓ admin update receptionist profile                                                                                                                    0.05s
  ✓ admin update perawat profile                                                                                                                         0.06s
  ✓ admin update pemilik profile                                                                                                                         0.06s
  ✓ valid password update                                                                                                                                0.08s
  ✓ email change                                                                                                                                         0.04s
  ✓ empty required fields                                                                                                                                0.05s
  ✓ invalid email format                                                                                                                                 0.05s
  ✓ duplicate email                                                                                                                                      3.55s
  ✓ extremely long input                                                                                                                                 0.05s
  ✓ sql injection input                                                                                                                                  0.07s
  ✓ xss input                                                                                                                                            0.04s
  ✓ unauthorized profile editing                                                                                                                         0.07s

   FAIL  Tests\Unit\PatientRegistrationTest
  ✓ admin register patient                                                                                                                               0.10s
  ⨯ receptionist register patient                                                                                                                        1.37s
  ✓ resepsionis missing required fields pemilik                                                                                                          0.07s
  ✓ resepsionis nama terlalu pendek                                                                                                                      0.06s
  ✓ resepsionis invalid phone number                                                                                                                     0.04s
  ✓ resepsionis phone too short                                                                                                                          0.06s
  ✓ resepsionis alamat terlalu pendek                                                                                                                    0.05s
  ✓ resepsionis duplicate email                                                                                                                          0.05s
  ✓ resepsionis invalid email format                                                                                                                     0.05s
  ✓ resepsionis invalid pet date                                                                                                                         0.07s
  ✓ resepsionis invalid pet gender                                                                                                                       0.07s
  ✓ resepsionis nama pet terlalu pendek                                                                                                                  0.05s
  ⨯ resepsionis sql injection nama pemilik                                                                                                               1.26s
  ⨯ resepsionis xss nama pemilik                                                                                                                         1.39s
  ✓ missing required fields pemilik                                                                                                                      0.04s
  ✓ invalid phone number                                                                                                                                 0.04s
  ✓ invalid date format pet                                                                                                                              0.07s
  ✓ duplicate patient record email                                                                                                                       0.07s
  ✓ sql injection attempt pet name                                                                                                                       0.06s
  ✓ xss attempt owner name                                                                                                                               0.06s
  ✓ unauthorized role attempting registration                                                                                                            0.04s
  ✓ admin read pemilik and pet                                                                                                                           0.10s
  ✓ admin update pemilik                                                                                                                                 0.06s
  ✓ admin update pet                                                                                                                                     0.05s
  ✓ admin delete pet                                                                                                                                     0.05s
  ✓ admin delete pemilik                                                                                                                                 0.05s
  ✓ resepsionis read pemilik and pet                                                                                                                     0.08s
  ⨯ resepsionis update pemilik                                                                                                                           1.26s
  ⨯ resepsionis update pet                                                                                                                               1.27s
  ✓ resepsionis delete pet                                                                                                                               0.07s
  ✓ resepsionis delete pemilik                                                                                                                           0.06s

   PASS  Tests\Unit\UserTest
  ✓ user creation and role assignment                                                                                                                    0.07s
  ✓ login authenticates user                                                                                                                             0.06s
  ✓ logout unauthenticates user                                                                                                                          0.05s

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > receptionist register patient
  Failed asserting that a row in the table [user] matches the attributes {
    "email": "pemilik.baru8676@gmail.com"
  }.

  Found: [
    {
        "email": "aaaaasss@mail.com"
    },
    {
        "email": "admin@mail.com"
    },
    {
        "email": "dokteraralan@mail.com"
    }
  ] and 10 others.

  at tests\Unit\PatientRegistrationTest.php:160
    156▕             'alamat' => 'Jl. Merpati No. 12, Surabaya'
    157▕         ]);
    158▕
    159▕         // Bypass assertSessionHas - langsung cek database
  ➜ 160▕         $this->assertDatabaseHas('user', ['email' => $email]);
    161▕
    162▕         $userPemilik = DB::table('user')->where('email', $email)->first();
    163▕         $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();
    164▕

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > resepsionis sql injection nama pemilik
  Failed asserting that a row in the table [user] matches the attributes {
    "email": "sqlinject5759@gmail.com"
  }.

  Found: [
    {
        "email": "aaaaasss@mail.com"
    },
    {
        "email": "admin@mail.com"
    },
    {
        "email": "dokteraralan@mail.com"
    }
  ] and 10 others.

  at tests\Unit\PatientRegistrationTest.php:413
    409▕             'alamat' => 'Jl. Test SQL Injection No. 1'
    410▕         ]);
    411▕
    412▕         // Laravel PDO parameterized → data masuk apa adanya, tabel tidak drop
  ➜ 413▕         $this->assertDatabaseHas('user', ['email' => $email]);
    414▕
    415▕         // Cleanup
    416▕         $userPemilik = DB::table('user')->where('email', $email)->first();
    417▕         if ($userPemilik) {

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > resepsionis xss nama pemilik
  Failed asserting that a row in the table [user] matches the attributes {
    "email": "xsstest2581@gmail.com"
  }.

  Found: [
    {
        "email": "aaaaasss@mail.com"
    },
    {
        "email": "admin@mail.com"
    },
    {
        "email": "dokteraralan@mail.com"
    }
  ] and 10 others.

  at tests\Unit\PatientRegistrationTest.php:441
    437▕             'alamat' => 'Jl. Test XSS Injection No. 2'
    438▕         ]);
    439▕
    440▕         // Data tetap masuk karena controller tidak sanitize HTML
  ➜ 441▕         $this->assertDatabaseHas('user', ['email' => $email]);
    442▕
    443▕         // Cleanup
    444▕         $userPemilik = DB::table('user')->where('email', $email)->first();
    445▕         if ($userPemilik) {

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > resepsionis update pemilik
  Failed asserting that a row in the table [user] matches the attributes {
    "iduser": 1257,
    "nama": "Siti Aminah"
  }.

  Found similar results: [
    {
        "iduser": 1257,
        "nama": "Dummy Pemilik"
    }
  ].

  at tests\Unit\PatientRegistrationTest.php:739
    735▕             'alamat' => 'Jl. Kenanga No. 45'
    736▕         ]);
    737▕
    738▕         // format_nama → 'Siti Aminah'
  ➜ 739▕         $this->assertDatabaseHas('user', ['iduser' => $dummy->iduser, 'nama' => 'Siti Aminah']);
    740▕         $this->assertDatabaseHas('pemilik', ['idpemilik' => $dummy->idpemilik, 'no_wa' => '087777777777']);
    741▕
    742▕         $this->cleanupUser((object)['iduser' => $dummy->iduser]);
    743▕         $this->cleanupUser($receptionist);

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > resepsionis update pet
  Failed asserting that a row in the table [pet] matches the attributes {
    "idpet": 112,
    "nama": "Simba"
  }.

  Found similar results: [
    {
        "idpet": 112,
        "nama": "Dummy Pet"
    }
  ].

  at tests\Unit\PatientRegistrationTest.php:765
    761▕             'idpemilik' => $dummyPemilik->idpemilik,
    762▕             'idras_hewan' => $rasId
    763▕         ]);
    764▕
  ➜ 765▕         $this->assertDatabaseHas('pet', ['idpet' => $dummyPet->idpet, 'nama' => 'Simba']);
    766▕
    767▕         DB::table('pet')->where('idpet', $dummyPet->idpet)->delete();
    768▕         $this->cleanupUser((object)['iduser' => $dummyPemilik->iduser]);
    769▕         $this->cleanupUser($receptionist);


  Tests:    5 failed, 43 passed (84 assertions)
  Duration: 13.44s
```
