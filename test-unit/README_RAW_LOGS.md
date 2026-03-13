# Laporan Log Raw Pengujian Unit (Test Execution Log)

Berikut ini adalah log terminal mentah hasil jalannya pengujian `$ php artisan test` yang menunjukkan bahwa semua tes _Unique Constraint_ (residu error duplikasi akun) sudah bersih secara sistem oleh `DatabaseTransactions`, sedangkan fungsi lain gagal karena bug murni (contoh: Validasi logic yang mencegah sistem menyimpan data yang dikirim dengan pesan \`Session is missing expected key [success]\`).

## Detail Output / Run Result

\`\`\`text
   FAIL  Tests\Unit\EditProfileTest
  ⨯ admin successful profile update                                                0.59s
  ⨯ admin update doctor profile                                                    0.07s
  ⨯ admin update receptionist profile                                              0.06s
  ⨯ valid password update                                                          0.06s
  ⨯ valid email change                                                             0.04s
  ⨯ empty required fields                                                          0.05s
  ⨯ invalid email format                                                           0.06s
  ✓ duplicate email                                                                0.11s
  ⨯ extremely long input                                                           0.05s
  ⨯ sql injection input                                                            0.08s
  ⨯ xss input                                                                      0.09s
  ✓ unauthorized profile editing                                                   0.18s

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                              0.04s

   FAIL  Tests\Unit\PatientRegistrationTest
  ⨯ admin register patient successful                                              0.30s
  ⨯ receptionist register patient successful                                       0.08s
  ⨯ missing required fields pemilik                                                0.04s
  ⨯ invalid phone number                                                           0.04s
  ⨯ invalid date format pet                                                        0.04s
  ⨯ duplicate patient record email                                                 0.05s
  ⨯ sql injection attempt pet name                                                 0.04s
  ⨯ xss attempt owner name                                                         0.06s
  ✓ unauthorized role attempting registration                                      0.04s

   PASS  Tests\Unit\UserTest
  ✓ user creation and role assignment                                              0.05s
  ✓ login authenticates user                                                       0.11s
  ✓ logout unauthenticates user                                                    0.06s

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                  0.07s
───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\EditProfileTest > admin successful profile update
  Session is missing expected key [success].
Failed asserting that false is true.

  at tests\Unit\EditProfileTest.php:56
     52▕             'nama' => 'Admin Updated Name',
     53▕             'email' => $admin->email,
     54▕         ]);
     55▕
  ➜  56▕         $response->assertSessionHas('success');
     57▕         $this->assertDatabaseHas('user', [
     58▕             'iduser' => $admin->iduser,
     59▕             'nama' => 'Admin Updated Name'
     60▕         ]);

───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\EditProfileTest > admin update doctor profile
  Session is missing expected key [success].
Failed asserting that false is true.

  at tests\Unit\EditProfileTest.php:75
     71▕             'nama' => 'Doctor Updated By Admin',
     72▕             'email' => $doctor->email,
     73▕         ]);
     74▕
  ➜  75▕         $response->assertSessionHas('success');
     76▕         $this->assertDatabaseHas('user', [
     77▕             'iduser' => $doctor->iduser,
     78▕             'nama' => 'Doctor Updated By Admin'
     79▕         ]);

───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\EditProfileTest > empty required fields
  Session is missing expected key [errors].
Failed asserting that false is true.

  at tests\Unit\EditProfileTest.php:149
    145▕             'nama' => '',
    146▕             'email' => '',
    147▕         ]);
    148▕
  ➜ 149▕         $response->assertSessionHasErrors(['nama', 'email']);
    150▕
    151▕         $this->cleanupUser($admin);
    152▕     }

───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\EditProfileTest > xss input
  Failed asserting that a row in the table [user] matches the attributes {
    "iduser": 151,
    "nama": "<script>alert('xss')<\/script>"
}.

  at tests\Unit\EditProfileTest.php:235
    231▕             'nama' => $xssInput,
    232▕             'email' => $admin->email,
    233▕         ]);
    234▕
  ➜ 235▕         $this->assertDatabaseHas('user', [
    236▕             'iduser' => $admin->iduser,
    237▕             'nama' => ucwords(strtolower($xssInput))
    238▕         ]);

───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > admin register patient successful
  Session is missing expected key [success].
Failed asserting that false is true.

  at tests\Unit\PatientRegistrationTest.php:81
     77▕             'no_wa' => '081234567890',
     78▕             'alamat' => 'Jl. Test No 123'
     79▕         ]);
     80▕
  ➜  81▕         $responsePemilik->assertSessionHas('success');
     82▕         $this->assertDatabaseHas('user', ['email' => $email]);

───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > missing required fields pemilik
  Session is missing expected key [errors].
Failed asserting that false is true.

  at tests\Unit\PatientRegistrationTest.php:165
    161▕             'no_wa' => '',
    162▕             'alamat' => ''
    163▕         ]);
    164▕
  ➜ 165▕         $response->assertSessionHasErrors(['nama', 'email', 'no_wa', 'alamat']);

───────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Unit\PatientRegistrationTest > sql injection attempt pet name
  ErrorException   
  Attempt to read property "iduser" on null

  at tests\Unit\PatientRegistrationTest.php:232
    228▕             'no_wa' => '081111111111',
    229▕             'alamat' => 'Alamat'
    230▕         ]);
    231▕         $userPemilik = DB::table('user')->where('email', $email)->first();
  ➜ 232▕         $pemilik = DB::table('pemilik')->where('iduser', $userPemilik->iduser)->first();

───────────────────────────────────────────────────────────────────────────────────────

  Tests:    18 failed, 8 passed (29 assertions)
  Duration: 3.06s
\`\`\`
