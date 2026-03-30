# QUICK START GUIDE - RISK MITIGATION
## For Development Team

---

## 🚀 PHASE 1: DO THIS NOW (Week 1)

### TASK 1: Replace Hardcoded Password (RR-01)
**File:** `app/Http/Controllers/Resepsionis/PemilikResepsionis_Controller.php`  
**Current Code** (Line 69):
```php
'password' => Hash::make('123456'),
```

**Fix:**
```php
// Generate random password minimum 16 chars with complexity
$randomPassword = Str::password(16, letters: true, numbers: true, symbols: true);

// Store in database
'password' => Hash::make($randomPassword),

// Then email it to pemilik or display in modal
// Make sure to force password change on first login
```

**Test:**
- Create new Pemilik and verify password is unique each time
- Verify password is sent/displayed to user
- Verify force-change-password on first login

---

### TASK 2: Implement Account Lockout (RG-01)
**File:** `app/Http/Controllers/Auth/LoginController.php`

**Add to LoginController:**
```php
use Illuminate\Cache\RateLimiter;

protected function attemptLogin(Request $request)
{
    $email = $request->email;
    $limiter = app(RateLimiter::class);
    $key = "login-attempts:" . $email;
    
    // Check if account locked
    if ($limiter->tooManyAttempts($key, 5, 15)) { // 5 tries, 15 min
        return back()
            ->withErrors(['email' => 'Terlalu banyak percobaan. Silakan coba 15 menit lagi.'])
            ->withInput();
    }
    
    $user = User::where('email', $email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        $limiter->hit($key);
        return back()->withErrors(['password' => 'Password salah'])->withInput();
    }
    
    $limiter->clear($key); // Clear attempts on success
    // ... continue login
}
```

**Add to .env:**
```
CACHE_DRIVER=redis  # or database
```

**Test:**
- Try wrong password 5 times
- Verify account locked for 15 minutes
- Verify correct password works after lockout expires

---

### TASK 3: Secure Database Credentials (RG-08)
**File:** `.gitignore`  
**Check line 9:**
```
.env
.env.*
```

**Fix if missing:**
```bash
# Run in terminal
echo ".env" >> .gitignore
echo ".env.*.php" >> .gitignore
```

**Set file permissions:**
```bash
chmod 600 .env
```

**Create .env.example (with dummy values):**
```
# Copy .env to .env.example
# Replace actual values with placeholders
DB_PASSWORD=your_password_here
MAIL_PASSWORD=your_mail_password_here
```

---

### TASK 4: Fix Raw SQL Queries (RG-14)
**Search for raw SQL usage:**
```bash
grep -r "DB::raw\|DB::statement" app/
```

**Example fix:**
```php
// ❌ WRONG
DB::table('user')->where(DB::raw("email = '$email'"))->first();

// ✅ RIGHT
DB::table('user')->where('email', $email)->first();
```

---

### TASK 5: Custom Error Pages (RG-09)
**Create error pages:**
```bash
# Create files
touch resources/views/errors/404.blade.php
touch resources/views/errors/500.blade.php
touch resources/views/errors/503.blade.php
```

**Example 500.blade.php:**
```blade
<div class="container">
    <h1>Terjadi Kesalahan</h1>
    <p>Sistem kami sedang mengalami masalah. Tim kami sudah diberitahu.</p>
    <p>Silakan coba lagi nanti.</p>
    <a href="{{ route('login') }}">Kembali ke Login</a>
</div>
```

**Update config/app.php:**
```php
'debug' => env('APP_DEBUG', false), // Make sure false in production
```

---

## ⭐ PHASE 2: Complete This Week 2-3 (Week 2)

### TASK 6: Strengthen Password Validation (RR-02)
**File:** `app/Http/Controllers/Auth/LoginController.php`

**Update password validation rule:**
```php
// FROM:
'password' => 'required|string|min:6',

// TO:
'password' => 'required|string|min:10|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',
```

**Add custom message:**
```php
'password.regex' => 'Password harus minimal 10 karakter, dengan kombinasi huruf besar, huruf kecil, angka, dan simbol (!@#$%)',
```

**Update PemilikResepsionis_Controller validation:**
```php
// In validate_pemilik() - also update password validation if user changes password
```

---

### TASK 7: Verify CSRF Protection (RG-07)
**Check all forms in resources/views/**
```blade
<form action="{{ route('...') }}" method="POST">
    @csrf  <!-- MUST BE HERE -->
    ...
</form>
```

**For all state-changing routes (POST, PUT, DELETE):**
```bash
grep -r "@method\|method='PUT'\|method='DELETE'" resources/views/
# Verify all have @csrf
```

---

### TASK 8: Fix Input Validation (RR-06)
**File:** `app/Http/Controllers/Resepsionis/PetResepsionis_Controller.php`

**Enhance validation:**
```php
protected function validate_pet(Request $request)
{
    return $request->validate([
        'nama' => [
            'required', 
            'string', 
            'max:100', 
            'min:3',
            'regex:/^[a-zA-Z0-9\s\-]+$/'  // Only alphanumeric, space, hyphen
        ],
        'warna_tanda' => [
            'required', 
            'string', 
            'max:45',
            'regex:/^[a-zA-Z0-9\s\-,]+$/'  // Safe characters only
        ],
        'alamat' => [
            'required', 
            'string', 
            'min:5',
            'max:500'
        ],
        // ... rest of validation
    ]);
}
```

---

### TASK 9: Encrypt Sensitive Data (RG-06)
**File:** `app/Models/Pemilik.php` (if model exists, or directly in migration)

**Create migration:**
```bash
php artisan make:migration encrypt_pemilik_fields
```

**Migration content:**
```php
Schema::table('pemilik', function (Blueprint $table) {
    // These fields should be encrypted at application level
    // Or use database-level encryption
});
```

**Update PemilikResepsionis_Controller:**
```php
use Illuminate\Support\Facades\Crypt;

public function store_pemilik(Request $request)
{
    $validated = $this->validate_pemilik($request);
    
    $iduser = DB::table('user')->insertGetId([
        'nama' => $this->format_nama($validated['nama']),
        'email' => strtolower($validated['email']),
        'password' => Hash::make($randomPassword),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Encrypt sensitive fields
    DB::table('pemilik')->insert([
        'iduser' => $iduser,
        'no_wa' => Crypt::encrypt($validated['no_wa']),  // Encrypt
        'alamat' => Crypt::encrypt($validated['alamat']), // Encrypt
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    // ... rest of code
}
```

**When displaying (decrypt):**
```php
'no_wa' => Crypt::decrypt($pemilik->no_wa),
'alamat' => Crypt::decrypt($pemilik->alamat),
```

---

### TASK 10: Implement Email Verification (RG-03)
**Use Laravel built-in verification:**

**Update User model:**
```php
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}
```

**Add middleware to routes:**
```php
Route::middleware(['verified'])->group(function () {
    Route::get('Resepsionis/dashboard', ...);
});
```

**Send verification email after registration:**
```php
// In PemilikResepsionis_Controller or UserController
$user->sendEmailVerificationNotification();
```

---

## 🔥 PHASE 3: Schedule This (Week 3-4)

### TASK 11: Two-Factor Authentication (RG-02)
**Install package:**
```bash
composer require laravel-fortify
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan migrate
```

**Or use TOTP:**
```bash
composer require spomky-labs/otph
```

### TASK 12: Audit Logging (RR-07)
**Install package:**
```bash
composer require spatie/laravel-audit
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag=migrations
php artisan migrate
```

### TASK 13: Rate Limiting (RG-11)
**Add to routes/web.php:**
```php
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('throttle:3,60')->group(function () {
    Route::post('/password/email', [PasswordResetController::class, 'sendReset']);
});
```

---

## 📋 TESTING CHECKLIST

### Authentication
- [ ] New Pemilik account has unique random password
- [ ] Password must be 10+ chars with complexity
- [ ] Failed login locked after 5 attempts
- [ ] Email verification required for new accounts
- [ ] Password reset token expires after 1 hour
- [ ] Password reset token single-use only

### Data Protection
- [ ] Phone number masked in list view (except last 4 digits)
- [ ] Address only visible in detail view
- [ ] PII data encrypted in database
- [ ] Session times out after 30 min inactivity
- [ ] CSRF tokens on all forms

### Security
- [ ] No SQL injection possible (tested with SQLmap)
- [ ] No XSS vulnerabilities (tested in all input fields)
- [ ] Stack traces not shown in error messages
- [ ] Rate limiting works (test with multiple requests)
- [ ] .env file not accessible via web

---

## 🐛 DEBUG MODE (Development Only)

**For local testing, you can use:**
```php
// .env.local (development)
APP_DEBUG=true
LOG_CHANNEL=single

// .env.production
APP_DEBUG=false
LOG_CHANNEL=syslog
```

---

## 📞 SUPPORT & QUESTIONS

- **Security Issues:** Refer to SECURITY_ASSESSMENT_RECOMMENDATIONS.md
- **Detailed Risk Info:** Check RISK_REGISTER.md
- **Quick Reference:** RISK_REGISTER_SUMMARY.md
- **JSON Format:** RISK_REGISTER.json (for tools/import)

---

## ✅ COMPLETION TRACKING

Track your progress:

```
Week 1 (Critical):
- [ ] RR-01: Hardcoded password fixed
- [ ] RG-01: Account lockout implemented
- [ ] RG-08: Database credentials secured
- [ ] RG-14: SQL injection risks identified & fixed
- [ ] RG-09: Error pages customized
- [ ] RG-07: CSRF coverage verified

Week 2:
- [ ] RR-02: Password validation strengthened
- [ ] RR-06: Input validation improved
- [ ] RG-03: Email verification implemented
- [ ] Testing on staging environment

Week 3-4:
- [ ] RG-02: 2FA implemented
- [ ] RG-05: Secure password reset
- [ ] RG-06: Sensitive data encrypted
- [ ] RR-07: Audit logging setup
- [ ] RG-13: Monitoring configured
- [ ] Security penetration testing complete
```

---

**Last Updated:** 27 February 2026  
**Status:** Ready for Implementation  
**Estimated Effort:** 140-180 developer hours
