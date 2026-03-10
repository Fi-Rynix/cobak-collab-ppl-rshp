# RISK REGISTER - SUMMARY TABLE (QUICK REFERENCE)

| ID | Risk Title | Risk Category | Probability | Impact | Exposure | Severity | Risk Owner | Response Strategy | Status |
|---|---|---|---|---|---|---|---|---|---|
| RR-01 | Hardcoded Default Password pada Create Pemilik | Security | 4 | 4 | 16 | HIGH | Backend Dev | Mitigate | ##### |
| RR-02 | Weak Password Validation (min 6 char) | Security | 3 | 5 | 15 | HIGH | Backend Dev | Mitigate | ##### |
| RR-03 | Resepsionis Dapat Melihat Semua PII Pemilik | Security | 3 | 4 | 12 | MEDIUM | Backend Dev | Mitigate | ##### |
| RR-04 | Race Condition - Nomor Urut Duplicate | Technical | 2 | 3 | 6 | MEDIUM | Backend Dev | Mitigate | ##### |
| RR-05 | Double Booking - Tidak Ada Validasi Jadwal | Business | 4 | 3 | 12 | MEDIUM | Backend Dev + PM | Mitigate | ##### |
| RR-06 | Insufficient Input Validation (SQL Injection Risk) | Security | 2 | 4 | 8 | MEDIUM | Backend Dev | Mitigate | ##### |
| RR-07 | No Audit Trail untuk Resepsionis Actions | Governance | 3 | 3 | 9 | MEDIUM | Backend Dev + PM | Mitigate | ##### |
| RG-01 | No Account Lockout setelah Failed Login | Security | 3 | 5 | 15 | HIGH | Backend Dev | Mitigate | ##### |
| RG-02 | No Two-Factor Authentication (2FA) | Security | 2 | 5 | 10 | HIGH | Backend Dev | Mitigate | ##### |
| RG-03 | Email Verification Not Implemented | Security | 3 | 2 | 6 | MEDIUM | Backend Dev | Mitigate | ##### |
| RG-04 | Session Timeout & Encryption Missing | Security | 3 | 4 | 12 | MEDIUM | Backend Dev | Mitigate | ##### |
| RG-05 | Password Reset Token Security | Security | 2 | 5 | 10 | HIGH | Backend Dev | Mitigate | ##### |
| RG-06 | Sensitive Data (PII) Not Encrypted | Security | 2 | 5 | 10 | HIGH | Backend Dev | Mitigate | ##### |
| RG-07 | CSRF Protection Incomplete | Security | 2 | 3 | 6 | MEDIUM | Backend Dev | Mitigate | ##### |
| RG-08 | Database Credentials Exposed (.env) | Security | 2 | 5 | 10 | HIGH | DevOps + Backend | Mitigate | ##### |
| RG-09 | Error Messages Reveal System Info | Security | 3 | 2 | 6 | MEDIUM | Backend Dev | Mitigate | ##### |
| RG-10 | Cross-Site Scripting (XSS) Vulnerability | Security | 2 | 4 | 8 | MEDIUM | Frontend + Backend | Mitigate | ##### |
| RG-11 | No Rate Limiting on API/Forms | Technical | 3 | 3 | 9 | MEDIUM | Backend Dev | Mitigate | ##### |
| RG-12 | RBAC Single Point of Failure (Centralized Admin) | Security | 1 | 5 | 5 | MEDIUM | PM + Backend Dev | Mitigate | ##### |
| RG-13 | Insufficient Logging & Monitoring | Governance | 3 | 3 | 9 | MEDIUM | Backend Dev + DevOps | Mitigate | ##### |
| RG-14 | SQL Injection Risk dalam Raw Queries | Security | 2 | 5 | 10 | HIGH | Backend Dev | Mitigate | ##### |
| RG-15 | Soft Delete Data Not Encrypted/Archived | Security | 2 | 3 | 6 | MEDIUM | Backend Dev | Mitigate | ##### |

---

## CRITICAL RISKS TO ADDRESS FIRST (Priority Order)

### 🔴 CRITICAL - Must Address Immediately (Exposure ≥ 15 or Severity = HIGH with Probability ≥ 2)

1. **RR-01: Hardcoded Default Password** (Exp: 16)
   - Status: Will expose all new Pemilik accounts to compromise
   - Action: Replace with random generation + email/display temporary password
   
2. **RR-02: Weak Password Validation** (Exp: 15)
   - Status: Current min 6 chars insufficient against brute force
   - Action: Enforce min 10 chars with complexity requirements

3. **RG-01: No Account Lockout** (Exp: 15)
   - Status: Open to brute force attacks
   - Action: Implement 5-attempt lockout with 15-min timeout + CAPTCHA

4. **RG-02: No 2FA** (Exp: 10, but Impact=5)
   - Status: No second layer of authentication protection
   - Action: Implement OTP via email or TOTP for Admin/Dokter roles

5. **RG-05: Password Reset Token Security** (Exp: 10, but Impact=5)
   - Status: If not properly implemented, allows account takeover
   - Action: Use strong random tokens (64 chars), 1-hour expiry, single use

6. **RG-06: Sensitive Data Not Encrypted** (Exp: 10, but Impact=5)
   - Status: PII exposed if database captured
   - Action: Encrypt phone number, address fields at database level

7. **RG-08: DB Credentials Exposed** (Exp: 10, but Impact=5)
   - Status: If .env accessible, complete database compromise
   - Action: Ensure .gitignore, use secrets manager, set proper file permissions

8. **RG-14: SQL Injection Risk** (Exp: 10, but Impact=5)
   - Status: Potential for database manipulation
   - Action: Audit all raw queries, convert to parameterized queries

---

## RISK OVERVIEW BY SCOPE

### 📊 Resepsionis Role Specific Risks (RR)
- **Total:** 7 risks
- **HIGH Severity:** 2 (RR-01, RR-02)
- **MEDIUM Severity:** 5
- **Key Issues:** Default password, weak validation, PII exposure, audit trail missing

💡 **Mitigation Focus:** Authentication hardening, data protection, audit logging

### 📊 General/Cross-Application Risks (RG)
- **Total:** 8 risks
- **HIGH Severity:** 6 (RG-01, RG-02, RG-05, RG-06, RG-08, RG-14)
- **MEDIUM Severity:** 5
- **Key Issues:** Authentication gaps, encryption missing, logging insufficient

💡 **Mitigation Focus:** Authentication mechanisms, encryption, security monitoring

---

## TIMELINE RECOMMENDATION

**Phase 1 (Week 1-2) - CRITICAL:**
- [ ] RR-01: Replace hardcoded password with random generation
- [ ] RG-01: Implement account lockout mechanism
- [ ] RG-08: Audit .env security and use secrets manager
- [ ] RG-14: Convert raw SQL queries to parameterized

**Phase 2 (Week 3-4) - URGENT:**
- [ ] RR-02: Strengthen password validation rules
- [ ] RG-02: Implement 2FA (start with optional)
- [ ] RG-05: Implement secure password reset flow
- [ ] RG-06: Encrypt PII fields (phone, address)

**Phase 3 (Week 5-6) - HIGH:**
- [ ] RR-03: Implement PII masking and audit logging
- [ ] RR-04: Fix race condition with database locking
- [ ] RG-04: Implement session timeout and encryption
- [ ] RG-13: Build comprehensive audit logging system

**Phase 4 (Week 7-8) - MEDIUM:**
- [ ] RR-05: Add appointment scheduling conflict validation
- [ ] RR-06: Enhanced input validation and sanitization
- [ ] RR-07: Audit trail implementation
- [ ] RG-03: Email verification on registration
- [ ] RG-07: Verify CSRF protection coverage
- [ ] RG-09: Custom error pages for production
- [ ] RG-10: XSS prevention audit
- [ ] RG-11: Rate limiting implementation
- [ ] RG-12: RBAC improvement with approval workflow
- [ ] RG-15: Soft delete security enhancement

---

## NOTES & RECOMMENDATIONS

1. **Security is Shared Responsibility:**
   - Backend: Authentication, encryption, input validation, SQL injection prevention
   - Frontend: XSS prevention, CSRF tokens, input sanitization
   - DevOps: Infrastructure security, secrets management, secure deployment

2. **Use Laravel Built-in Features:**
   - Laravel already has good CSRF protection via middleware
   - Use Eloquent ORM for SQL injection prevention
   - Leverage Laravel's encryption for sensitive data
   - Use throttle middleware for rate limiting

3. **Compliance & Documentation:**
   - Document password policy for user communication
   - Maintain audit logs for regulatory compliance (if applicable)
   - Create security incident response plan
   - Regular security training for team

4. **Testing:**
   - Add security-focused unit and feature tests
   - Perform penetration testing after each major security fix
   - Automate security scanning in CI/CD pipeline
   - Regular vulnerability scanning of dependencies

5. **Monitoring & Maintenance:**
   - Set up security monitoring dashboards
   - Daily review of failed login attempts
   - Weekly security logs analysis
   - Monthly security audit meetings
   - Quarterly dependency updates and vulnerability patching

---

**Document Generated:** 27 February 2026  
**Last Updated:** 27 February 2026  
**Next Review:** 15 March 2026  
**Status:** DRAFT - Awaiting stakeholder review
