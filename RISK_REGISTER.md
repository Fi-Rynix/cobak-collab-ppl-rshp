# RISK REGISTER - RSHP (Rumah Sakit Hewan Peliharaan) System
## Risk Identification Report

---

## SCOPE RESEPSIONIS (RR-XX)

### RR-01: Hardcoded Default Password untuk Pemilik Baru
**Risk Title:** Hardcoded Default Password pada Create Pemilik  
**Risk Statement/Description:**  
Jika Resepsionis membuat akun Pemilik baru melalui form yang disediakan, sistem secara otomatis menambahkan password dengan nilai hardcoded "123456" ke database. Maka setiap Pemilik baru akan memiliki password yang identik dan mudah ditebak. Sehingga akun Pemilik dapat dengan mudah diakses oleh pihak yang tidak berwenang, termasuk Resepsionis lain atau orang eksternal yang mengetahui pola ini.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 4 (High)  
**Impact:** 4 (High)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 16 (4 × 4)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Generate random temporary password yang aman (minimal 12 karakter, kombinasi uppercase, lowercase, number, special char)
2. Email temporary password ke Pemilik atau display one-time only di form
3. Force password change pada login pertama
4. Implement password strength validation minimum

**Contingency Plan Description:**  
Jika email tidak terkirim, tampilkan temporary password dalam dialog dengan instruksi untuk dicatat. Sediakan link untuk reset password jika terlupa.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RR-02: Weak Password Validation pada Login
**Risk Title:** Minimum Password Length Hanya 6 Karakter  
**Risk Statement/Description:**  
Jika sistem login hanya memvalidasi password minimal 6 karakter tanpa requirement kompleksitas (mix uppercase, lowercase, number, symbol), maka password yang dibuat user bisa sangat lemah seperti "123456" atau "abcdef". Sehingga akun Resepsionis dan Pemilik akan mudah di-hack melalui brute force attack atau dictionary attack.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 3 (Medium-High)  
**Impact:** 5 (Critical)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 15 (3 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Terapkan password policy: minimum 10 karakter, harus mengandung uppercase, lowercase, number, special character
2. Implement password strength meter di UI untuk feedback real-time
3. Tambahkan validation rules di LoginController dan PemilikResepsionis_Controller
4. Dokumentasikan password policy untuk user

**Contingency Plan Description:**  
Jika user lupa password yang kompleks, sediakan forgot password flow dengan email verification.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RR-03: Resepsionis Akses Data Pribadi Pemilik Secara Langsung
**Risk Title:** Resepsionis Dapat Melihat Semua Nomor WA dan Alamat Pemilik  
**Risk Statement/Description:**  
Jika Resepsionis membuka halaman Daftar Pemilik atau Daftar Pet, sistem menampilkan nomor WhatsApp dan alamat lengkap dari semua Pemilik dalam satu tabel yang dapat di-scroll. Maka data pribadi (PII - Personally Identifiable Information) dari semua Pemilik menjadi visible dan bisa di-copy atau dicatat oleh Resepsionis dengan akses penuh tanpa audit trail. Sehingga terjadi potensi data leakage, pemanfaatan data untuk tujuan tidak sah, atau penjualan data ke pihak ketiga.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Processes  
**Probability:** 3 (Medium-High)  
**Impact:** 4 (High)  
**Timeframe:** Ongoing (Jangka Panjang)  
**Exposure:** 12 (3 × 4)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement data masking: tampilkan nomor WA hanya 3 digit akhir (0812xxxx3456 → 0812****3456)
2. Tampilkan alamat hanya pada detail page, bukan di list view
3. Implement audit logging untuk setiap akses data Pemilik (view, edit, delete)
4. Restrict export/copy functionality atau log setiap export
5. Role-based field visibility: Resepsionis tidak bisa edit data sensitif

**Contingency Plan Description:**  
Jika ada inkonsistensi masking, auto-disable akses dan alert admin. Implementasi data backup untuk investigasi breach.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RR-04: Race Condition pada Generate Nomor Urut Appointment
**Risk Title:** Concurrent Request Dapat Menghasilkan Nomor Urut Duplikat  
**Risk Statement/Description:**  
Jika dua Resepsionis secara bersamaan membuat appointment untuk dokter yang sama di hari yang sama, method `generate_nomor_urut()` akan melakukan query `max(no_urut)` dan menambah 1. Namun karena tidak ada database-level locking atau transaction isolation, kedua request bisa mendapat nomor urut yang sama (race condition). Maka sistem akan generate nomor urut duplikat. Sehingga terjadi confusion di sistem antrian, konflik data di database, dan appointment queue yang tidak sesuai.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 3 (Medium)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 6 (2 × 3)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement database transaction dengan `DB::transaction()`
2. Gunakan pessimistic locking: `lockForUpdate()` pada query
3. Alternative: Use database auto-increment atau UUID untuk nomor urut
4. Add unique constraint pada combination (idrole_user, tanggal, no_urut)
5. Test concurrent scenario dengan load testing

**Contingency Plan Description:**  
Monitor duplicate nomor urut melalui logging. Implement scheduled job untuk detect dan fix duplicates.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RR-05: Double Booking - Dokter Dapat Dijadwalkan Appointment Bersamaan
**Risk Title:** Tidak Ada Validasi Jadwal Dokter saat Membuat Appointment  
**Risk Statement/Description:**  
Jika Resepsionis membuat appointment untuk dokter tertentu, sistem hanya validasi bahwa dokter exist dan pet exist, tetapi tidak mengecek apakah dokter sudah memiliki appointment lain di slot waktu yang sama. Maka dokter bisa dijadwalkan untuk multiple appointments yang overlap/bersamaan. Sehingga terjadi overbooking dokter, penurunan quality of service, stress pada dokter, dan potential customer complaints bila appointment dibatalkan.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Processes  
**Probability:** 4 (High)  
**Impact:** 3 (Medium)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 12 (4 × 3)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer + Product Manager  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Add appointment time slot mechanism (duration per appointment)
2. Implement validation di TemuDokterResepsionis_Controller untuk check overlap
3. Show dokter availability calendar saat memilih dokter
4. Set max appointment per dokter per hari/hour
5. Add "status" field untuk track appointment execution (scheduled, in-progress, completed, cancelled)

**Contingency Plan Description:**  
Sediakan bulk reschedule functionality untuk move conflicted appointments. Alert dokter 30 min sebelum appointment.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RR-06: Insufficient Input Validation pada Text Fields
**Risk Title:** Potential SQL Injection dan XSS pada Warna Tanda dan Alamat  
**Risk Statement/Description:**  
Jika input fields seperti "warna_tanda", "alamat", dan "nama_jenis_hewan" hanya validate max length tanpa sanitization atau filtering special characters, user (baik Resepsionis maupun malicious attacker) bisa memasukkan nilai dengan SQL special characters atau JavaScript code. Maka data mungkin tidak ter-sanitize dengan sempurna dan bisa trigger SQL injection saat query atau XSS saat rendering di view. Sehingga attacker bisa ekstrasi database, inject malicious code, atau manipulate data.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology
**Probability:** 2 (Low-Medium)
**Impact:** 4 (High)
**Timeframe:** Immediate (Jangka Pendek)
**Exposure:** 8 (2 × 4)
**Severity:** Medium (6-12)
**Risk Owner:** Backend Developer
**Date Assigned:** #####
**Risk Response Strategy:** Mitigate
**Risk Response Plan Description:**
1. Validate input dengan regex pattern spesifik per field (contoh: warna hanya alphanumeric + space)
2. Use parameterized queries (Laravel Query Builder sudah safe, tapi ensure tidak ada raw queries)
3. Escape output di Blade dengan `{{ }}` instead of `{!! !!}`
4. Add HTML entity encoding untuk text output
5. Implement Content Security Policy (CSP) header

**Contingency Plan Description:**
Audit existing data untuk detect malicious payloads. Auto-sanitize jika ditemukan.

**Risk Status:** #####
**Risk Resolution:** #####
**Risk Closure Date:** #####

---

### RR-07: No Audit Trail untuk Resepsionis Actions
**Risk Title:** Resepsionis Activities Tidak Di-log secara Detail  
**Risk Statement/Description:**  
Jika sistem tidak mencatat siapa (Resepsionis mana) yang membuat, mengubah, atau menghapus data Pemilik, Pet, atau Appointment, hanya ada `deleted_by` field tapi tanpa operational logs untuk create/update, maka ketika terjadi data corruption atau unauthorized action, tidak bisa di-trace siapa pelakunya dan kapan terjadi. Sehingga sulit conduct investigation, accountability tidak jelas, dan compliance audit bisa gagal.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Processes  
**Probability:** 3 (Medium-High)  
**Impact:** 3 (Medium)  
**Timeframe:** Ongoing (Jangka Panjang)  
**Exposure:** 9 (3 × 3)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer + Project Manager  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Create audit_logs table: id, user_id, action (create/update/delete), entity_type, entity_id, old_value, new_value, timestamp
2. Implement audit logging middleware atau use Laravel auditing package
3. Log semua create/update/delete operations di Resepsionis controllers
4. Add audit log viewer di Admin dashboard
5. Retain logs untuk minimal 1 tahun

**Contingency Plan Description:**  
Implement database binlog untuk disaster recovery. Regular backup audit logs untuk compliance.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

## SCOPE UMUM/GENERAL (RG-XX)

### RG-01: Weak Authentication - Account Lockout tidak Diimplementasikan
**Risk Title:** No Account Lockout setelah Failed Login Attempts  
**Risk Statement/Description:**  
Jika user memasukkan password salah berkali-kali, sistem hanya menampilkan error message "Password salah" tetapi tidak melakukan account lockout, rate limiting, atau CAPTCHA challenge. Maka attacker bisa melakukan brute force attack dengan mencoba ratusan/ribuan password combinations tanpa henti untuk membobol account. Sehingga akun-akun penting seperti Admin dan Dokter bisa ter-compromise, data sensitif berbahaya untuk di-akses unauthorized user.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 3 (Medium-High)  
**Impact:** 5 (Critical)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 15 (3 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement failed login counter: track failed attempts per email/IP
2. Lock account setelah 5 failed attempts dalam 15 menit
3. Send email notification ke user jika banyak failed attempts
4. Add CAPTCHA setelah 3 failed attempts
5. Implement 2 minute delay per attempt (exponential backoff)
6. Log semua failed login attempts untuk security monitoring

**Contingency Plan Description:**  
Sediakan unlock functionality via email verification atau admin manual unlock. Implement IP-based blocking untuk suspicious activities.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-02: No Two-Factor Authentication (2FA)
**Risk Title:** Authentication Hanya Password, Tanpa Second Factor  
**Risk Statement/Description:**  
Jika sistem hanya menggunakan email dan password untuk authentication tanpa second factor seperti OTP via email/SMS, TOTP app, atau security key, maka attacker yang berhasil mendapat password (lewat phishing, data breach, malware) bisa langsung login ke account. Maka sensitive roles seperti Admin dan Dokter tidak memiliki extra layer of protection. Sehingga terjadi high risk unauthorized access, data manipulation, dan compliance gap.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 5 (Critical)  
**Timeframe:** Short-term (Jangka Pendek)  
**Exposure:** 10 (2 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement 2FA optional untuk semua user, mandatory untuk Admin/Dokter
2. Support multiple 2FA methods: Email OTP, SMS OTP, TOTP (Google Authenticator), backup codes
3. Use Laravel 2FA package atau build custom using TOTP library
4. Set OTP expiry 5-10 minutes
5. Add backup codes generate (10 codes) untuk emergency access jika device hilang

**Contingency Plan Description:**  
Admin bisa trigger OTP resend atau disable 2FA atas verifikasi identity. Backup codes valid untuk 1-time use only.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-03: Email Verification tidak Diimplementasikan
**Risk Title:** User Bisa Register dengan Email Tidak Valid  
**Risk Statement/Description:**  
Jika sistem tidak require email verification sebelum account fully active, user bisa register dengan email palsu, typo email, atau email milik orang lain. Maka jika account tersebut kemudian perlu password reset atau mendapat notifikasi penting, email tidak akan sampai, dan bisa terjadi confusion atau account hijacking. Sehingga data pemilik tidak akurat, komunikasi sistem tidak berjalan, dan account security tidak terjaga.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Processes  
**Probability:** 3 (Medium-High)  
**Impact:** 2 (Low-Medium)  
**Timeframe:** Medium-term (Jangka Menengah)  
**Exposure:** 6 (3 × 2)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Send verification email dengan unique token saat registration
2. Token valid hanya 24 jam
3. Account tidak fully active sampai email di-verify
4. Re-send verification email option available
5. Admin bisa manual verify account jika diperlukan
6. Track verification status di user table: `email_verified_at` field

**Contingency Plan Description:**  
Jika email tidak terkirim, sediakan resend button. For Admin roles, dapat di-bypass verification oleh Super Admin.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-04: Session Management Vulnerability
**Risk Title:** No Session Timeout dan Session Storage Not Encrypted  
**Risk Statement/Description:**  
Jika session di-store di file sistem (default Laravel) tanpa encryption dan tidak ada session timeout (user bisa stay logged in selamanya), maka session file bisa di-akses oleh attacker jika mendapat server file access, atau session bisa di-hijack jika device shared di public place tanpa explicit logout. Maka attacker bisa impersonate user dengan session yang sudah valid tanpa perlu password, terlebih jika device/browser tidak di-lock.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 3 (Medium-High)  
**Impact:** 4 (High)  
**Timeframe:** Ongoing (Jangka Panjang)  
**Exposure:** 12 (3 × 4)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Set session timeout 30 minutes inactivity (adjust per org policy)
2. Implement session refresh pada every user action (ajax heartbeat)
3. Encrypt session data: use `cookie` driver with encryption atau use Redis with encryption
4. Add "Remember Me" optional dengan 7 days expiry max
5. Implement logout everywhere option
6. Track active sessions di database untuk multi-device session management
7.Add IP & User-Agent validation untuk detect session hijacking

**Contingency Plan Description:**  
If session invalid detected, force re-login. Alert user dari other devices/IPs dengan logout option.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-05: Password Reset Vulnerability
**Risk Title:** Password Reset Token Security Not Defined  
**Risk Statement/Description:**  
Jika sistem implement password reset tapi token reset tidak properly validated (tidak cek token validity, expiry time, atau one-time use), atau token bisa di-guess (weak token generation), maka attacker bisa reset password user lain tanpa authorization, atau bisa reuse token untuk multiple reset attempts. Maka account takeover bisa terjadi untuk account apapun dengan mengetahui email saja.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 5 (Critical)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 10 (2 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Generate strong random token minimum 32 characters (use `Str::random(64)` atau similar)
2. Hash token sebelum store di database
3. Set token expiry 1 hour max
4. Validate token only dapat digunakan 1 kali (mark as used setelah reset)
5. Send reset link hanya ke verified email
6. Log Password reset attempts untuk auding
7. Require email verification sebelum password reset confirm
8. Use secure_token library atau JWT dengan short expiry

**Contingency Plan Description:**  
Expired token auto-delete setiap 24 jam. User bisa request multiple reset tokens, old ones auto-invalidate.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-06: Sensitive Data Stored in Plaintext
**Risk Title:** PII Data (Phone, Address) Not Encrypted di Database  
**Risk Statement/Description:**  
Jika nomor telepon, alamat, dan data sensitif lainnya disimpan di database dalam plaintext tanpa encryption, dan database backup atau file bisa di-akses (misalnya via unsecured backup storage, atau server compromise), maka data pribadi semua Pemilik akan terexpose ke attacker. Maka terjadi data breach, privacy violation, potential identity theft, dan regulatory non-compliance (GDPR, local privacy laws).

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 5 (Critical)  
**Timeframe:** Ongoing (Jangka Panjang)  
**Exposure:** 10 (2 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Encrypt PII fields: phone number, address, emergency contact
2. Use Laravel encryption: `Crypt::encrypt()` / `Crypt::decrypt()` dengan APP_KEY
3. Store encrypted data di database
4. Decrypt hanya saat display/edit di views
5. Implement column-level encryption atau DB-level encryption (MySQL AES_ENCRYPT)
6. Secure APP_KEY storage - use environment variable, rotate periodically
7. Implement key rotation mechanism untuk data yang sudah encrypted lama
8. Add access logging untuk sensitive data

**Contingency Plan Description:**  
Maintain encrypted backup. Jika key compromised, implement emergency re-encryption workflow.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-07: CSRF Protection Incomplete
**Risk Title:** Potential CSRF Vulnerability pada Form Submission  
**Risk Statement/Description:**  
Jika form submission di application tidak consistently validate CSRF token, atau CSRF token validation bisa di-bypass (misalnya via GET request yang seharusnya POST, atau missing validation di beberapa endpoint), maka attacker bisa craft malicious web page yang ketika di-open user yang sudah login, akan trigger undesired action (delete user, change role, dll) pada behalf of user tanpa user aware. Sehingga terjadi unauthorized data modification, account compromise, malicious action execution.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 3 (Medium)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 6 (2 × 3)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Verify CSRF token di SEMUA form submission (POST, PUT, DELETE)
2. Use `@csrf` directive di setiap form di Blade templates
3. Validate session-based CSRF token atau use double-submit cookie pattern
4. Implement SameSite cookie attribute: `SameSite=Lax` atau `SameSite=Strict`
5. Use Middleware VerifyCsrfToken yang already ada di Laravel
6. Add CSRF token validation test di unit tests
7. Regular security audit untuk detect CSRF bypass

**Contingency Plan Description:**  
Monitor suspicious POST request patterns. Implement rate limiting pada state-changing operations.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-08: Database Credentials Exposed
**Risk Title:** DB Password Stored di .env File Without Proper Protection  
**Risk Statement/Description:**  
Jika file .env yang berisi DB_PASSWORD dan DB_USERNAME tidak properly protected (commited ke repository, stored di accessible location, atau file permissions terlalu open), maka attacker yang bisa akses repository history atau server file system bisa mendapat database credentials. Maka attacker bisa connect langsung ke database, query semua data, modify data, atau delete database entirely. Sehingga complete system compromise, data loss, dan service outage.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Environment  
**Probability:** 2 (Low-Medium)  
**Impact:** 5 (Critical)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 10 (2 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** DevOps / Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Never commit .env ke version control - ensure .gitignore includes .env
2. Use .env.example template dengan dummy values untuk documentation
3. Set proper file permissions chmod 600 pada .env production
4. Use secrets management tool (AWS Secrets Manager, HashiCorp Vault, atau Docker Secrets)
5. Rotate DB credentials quarterly
6. Implement database user dengan minimum required privileges (no root password)
7. Use strong password untuk DB (minimum 20 characters, mix of char types)
8. Regular scan codebase untuk detect hardcoded secrets

**Contingency Plan Description:**  
If .env leaked, immediately rotate all credentials. Scan database logs untuk unauthorized access. Implement infrastructure as code dengan secrets injection.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-09: Insufficient Error Handling dan Information Disclosure
**Risk Title:** Error Messages Mengungkap System Information  
**Risk Statement/Description:**  
Jika aplikasi menampilkan stack trace, database error details, atau system path saat error terjadi (baik di development mode di production, atau di logs yang bisa di-access), maka attacker bisa mendapat banyak info tentang system architecture, library versions, database structure, dan potential vulnerabilities. Maka attacker bisa plan targeted attack lebih efektif, exploit known vulnerabilities di specific library versions.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 3 (Medium-High)  
**Impact:** 2 (Low-Medium)  
**Timeframe:** Ongoing (Jangka Panjang)  
**Exposure:** 6 (3 × 2)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Set APP_DEBUG=false di production .env
2. Implement custom error pages untuk 404, 500, 503 errors dengan generic messages
3. Log detailed errors ke file log (not display to user)
4. Implement centralized logging (ELK stack, Sentry, atau similar)
5. Never expose database error messages ke frontend - return generic "Something went wrong"
6. Implement exception handler di app/Exceptions/Handler.php untuk graceful error handling
7. Monitor logs untuk potential attacks atau suspicious patterns
8. Sanitize error messages sebelum log jika contain sensitive data

**Contingency Plan Description:**  
Use Sentry atau similar untuk real-time error alerting. Implement log rotation dan retention policy.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-10: Cross-Site Scripting (XSS) Vulnerability
**Risk Title:** Potential XSS jika Output tidak di-Escape Properly  
**Risk Statement/Description:**  
Jika user input di-display di view tanpa proper escaping (using `{!! !!}` instead of `{{ }}`), atau jika JavaScript libraries tidak sanitize user input saat update DOM, maka attacker bisa inject malicious JavaScript code via user input field. Maka script akan execute di browser visitor, bisa steal session cookie, redirect ke phishing page, atau modify page content. Sehingga user session compromise, data theft, malware distribution, reputation damage.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 4 (High)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 8 (2 × 4)  
**Severity:** Medium (6-12)  
**Risk Owner:** Frontend Developer + Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Use `{{ }}` untuk display user input (auto-escaped)
2. Never use `{!! !!}` untuk user-generated content
3. Implement Content Security Policy (CSP) header: restrict script sources
4. Sanitize user input dengan HTMLPurifier atau similar library
5. Use DOMPurify di client-side untuk dynamically added content
6. Implement output encoding per context (HTML, URL, JavaScript)
7. Add XSS vulnerability testing di security tests
8. Educate developers tentang XSS prevention best practices

**Contingency Plan Description:**  
Regular security scanning tools (OWASP ZAP, Burp Suite) untuk detect XSS. Implement Web Application Firewall (WAF).

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-11: No Rate Limiting pada API/Forms
**Risk Title:** No Protection Against Abuse (Brute Force, DoS)  
**Risk Statement/Description:**  
Jika sistem tidak implement rate limiting pada endpoints (login, register, password reset, atau CRUD operations), maka attacker bisa spam request berkali-kali dalam waktu singkat. Untuk login bisa lakukan brute force, untuk register bisa spam create accounts, untuk password reset bisa spam requests ke email, untuk CRUD bisa overload server. Sehingga legitimate user tidak bisa access service (DoS attack), overwhelming email system, server resource exhaustion.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 3 (Medium-High)  
**Impact:** 3 (Medium)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 9 (3 × 3)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement Laravel rate limiting: `RateLimit::class` middleware
2. Configure per-endpoint: login 5 attempts/15min, email send 3 attempts/hour, API general 100 requests/minute
3. Use Redis untuk rate limit storage (faster than database)
4. Implement sliding window algorithm untuk accurate rate limiting
5. Return 429 Too Many Requests status code saat limit exceeded
6. Log rate limit violations untuk security monitoring
7. Implement CAPTCHA setelah limit threshold
8. Add admin dashboard untuk monitor rate limit violations

**Contingency Plan Description:**  
Whitelist trusted IPs atau important users. Auto-purge old rate limit data untuk prevent table bloat.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-12: Role-Based Access Control (RBAC) Single Point of Failure
**Risk Title:** Centralized Role Assignment tanpa backup atau review process  
**Risk Statement/Description:**  
Jika Admin adalah satu-satunya yang bisa assign role dan tidak ada approval process, backup admin, atau review mechanism, maka jika Admin account compromise atau Admin make mistake dalam assign role (accidentally give Admin role to unauthorized user), terjadi potential privilege escalation atau access control bypass. Maka unauthorized user bisa mendapat admin privileges, security perimeter broken, sensitive operations bisa di-abuse.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Resources  
**Probability:** 1 (Low)  
**Impact:** 5 (Critical)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 5 (1 × 5)  
**Severity:** Medium (6-12)  
**Risk Owner:** Project Manager + Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement role change audit trail (audit_logs table track role changes)
2. Add approval workflow: role change request -> approval by super admin
3. Minimum 2 admins untuk critical role assignments
4. Implement role change notification ke affected user
5. Add admin activity logging di Admin dashboard
6. Regular role audit: quarterly review semua user roles
7. Implement time-limited admin roles dengan auto-expiry
8. Set role change protection policy di organization

**Contingency Plan Description:**  
Super admin bisa override approval saat emergency. Implement emergency access process dengan strong verification. Regular backup list admin users untuk recovery.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-13: Insufficient Logging and Monitoring
**Risk Title:** Limited Audit Trail untuk Security Events  
**Risk Statement/Description:**  
Jika sistem tidak log login attempts, logout events, permission changes, admin actions, atau suspicious activities secara comprehensive, maka ketika security incident terjadi, tidak bisa trace riwayat event untuk investigate root cause, timeline, dan impact. Maka incident response becomes reactive instead of proactive, audit compliance gagal, dan repeat attack bisa terjadi.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Processes
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Create comprehensive audit logging system:
   - Login/logout events dengan timestamp, IP, user-agent
   - Failed authentication attempts
   - Permission/role changes
   - Admin data modifications
   - Sensitive data access (PII fields)
   - API key generation/revocation
2. Implement structured logging (JSON format) untuk easier parsing
3. Set log retention 1 year minimum
4. Implement log aggregation (ELK, Splunk, atau cloud-based)
5. Create alerts untuk suspicious patterns (multiple failed logins, unusual admin actions)
6. Build admin audit log dashboard
7. Regular log analysis untuk security trend detection

**Contingency Plan Description:**  
Centralize logs dari semua servers. Backup logs ke separate secure storage. Implement log integrity verification untuk prevent tampering.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-14: SQL Injection Risk di Beberapa Query
**Risk Title:** Potential SQL Injection jika Raw Query Digunakan  
**Risk Statement/Description:**  
Jika developer menggunakan raw SQL queries (DB::raw()) atau concatenate user input langsung ke query strings instead of using parameterized queries, maka attacker bisa inject SQL code ke query. Jika kombinasi dengan weak input validation, attacker bisa bypass authentication, extract sensitive data, modify data, atau execute arbitrary SQL. Sehingga complete database compromise, data loss, unauthorized access.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 5 (Critical)  
**Timeframe:** Immediate (Jangka Pendek)  
**Exposure:** 10 (2 × 5)  
**Severity:** High (15-25)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Audit semua query di codebase untuk detect raw SQL usage
2. Convert raw queries ke Eloquent/Query Builder dengan parameter binding
3. Use parameterized queries: `where('field', '=', $input)` instead of `where(DB::raw("field = '$input'"))`
4. Implement input validation dan type casting untuk query parameters
5. Use prepared statements untuk complex queries
6. Implement ORM soft delete untuk prevent accidental data exposure
7. Add static code analysis (phpstan, psalm) untuk detect potential SQL injection
8. Regular code review focusing pada database interactions
9. Penetration testing untuk SQL injection validation

**Contingency Plan Description:**  
Monitor database query logs untuk suspicious SQL patterns. Implement database user restrictions (read-only untuk application user if possible).

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

### RG-15: Soft Delete Data Tidak Dienkripsi atau Di-Archive
**Risk Title:** Deleted Data Masih Accessible di Database  
**Risk Statement/Description:**  
Jika deleted data menggunakan soft delete (hanya set deleted_at timestamp) tanpa encryption atau moving ke separate archive storage, maka data masih fisik di dalam database records. Jika database backup atau file system bisa di-access (backup theft, server compromise), attacker bisa query deleted records dan recover sensitive data. Maka data privacy tidak terjaga untuk deleted user/pemilik data, compliance issue untuk data minimization principle.

**Date Risk Identified:** 27/02/2026  
**Risk Originator:** System Analysis  
**Risk Category:** Technology  
**Probability:** 2 (Low-Medium)  
**Impact:** 3 (Medium)  
**Timeframe:** Medium-term (Jangka Menengah)  
**Exposure:** 6 (2 × 3)  
**Severity:** Medium (6-12)  
**Risk Owner:** Backend Developer  
**Date Assigned:** #####  
**Risk Response Strategy:** Mitigate  
**Risk Response Plan Description:**  
1. Implement secure deletion policy:
   - Option 1: Hard delete sensitive data after retention period (GDPR right to be forgotten)
   - Option 2: Archive deleted data ke separate encrypted storage
   - Option 3: Encrypt deleted records immediately saat soft delete
2. Set automated hard delete process untuk deleted records > 90 days (configurable)
3. Implement shred mechanism untuk physically overwrite data sebelum hard delete
4. Create archive table for deleted data dengan encryption
5. Restrict query access pada deleted records (where deleted_at is null):
   - Use global scope pada models untuk auto-exclude deleted
6. Implement data retention policy dokumen
7. Regular verification bahwa deleted data tidak accessible

**Contingency Plan Description:**  
Maintain encrypted backup untuk deleted data untuk audit purposes. Implement secure disposal procedure untuk data yang sudah retention period expired.

**Risk Status:** #####  
**Risk Resolution:** #####  
**Risk Closure Date:** #####

---

## SUMMARY STATISTICS

**Total Risks Identified:** 15
- **Resepsionis Scope (RR):** 7 risks
- **General/Umum Scope (RG):** 8 risks

**Severity Distribution:**
- **High (15-25):** 10 risks (RR-01, RG-01, RG-02, RG-04, RG-05, RG-08, RG-09, RG-14)
- **Medium (6-12):** 5 risks (RR-02, RR-03, RR-04, RR-05, RR-06, RR-07, RG-03, RG-06, RG-07, RG-10, RG-11, RG-12, RG-13, RG-15)

**Risk Category Distribution:**
- **Security:** 11 risks
- **Technical:** 2 risks
- **Business:** 1 risk
- **Governance:** 2 risks

**Priority (Top 5 Critical - Harus diselesaikan segera):**
1. RR-01: Hardcoded Default Password
2. RG-01: Account Lockout Missing
3. RG-02: No 2FA
4. RG-05: Password Reset Vulnerability
5. RG-06: Sensitive Data Plaintext

---

**Risk Register Created:** 27 February 2026  
**Analyst:** System Analysis / Security Assessment Team  
**Next Review Date:** 15 March 2026
