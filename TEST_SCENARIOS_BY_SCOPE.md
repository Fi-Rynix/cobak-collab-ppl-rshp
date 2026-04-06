# Test Scenarios by Scope - Singkat & Terstruktur
**5 Scope dengan 10-15 Scenarios per Scope**

---

## SCOPE 1: TemuDokter - Admin Features (13 scenarios)

### Create
1. ✅ Admin create appointment - valid pet & doctor → redirect success + DB created
2. ✅ Admin create multiple appointments same doctor → no_urut incremented globally (1, 2, 3...)
3. ✅ Admin create appointments different doctors → separate no_urut sequences
4. ❌ Admin create fail - missing idpet → validation error
5. ❌ Admin create fail - invalid idrole_user → validation error
6. ❌ Perawat attempt create (no route) → 404 error
7. ❌ Dokter attempt create (no route) → 404 error
8. ❌ Pemilik not authenticated → redirect to login

### Read/List
9. ✅ Admin list all appointments → view all records with eager load
10. ✅ Admin filter by date (today) → only today's appointments
11. ✅ Admin filter by doctor → only that doctor's appointments

### Cancel/Delete
12. ✅ Admin cancel appointment → soft delete (deleted_at set, deleted_by set)
13. ✅ Admin cancel then list → cancelled not visible in default query

---

## SCOPE 2: TemuDokter - Resepsionis Features (12 scenarios)

### Create
1. ✅ Resepsionis create appointment day 1 → no_urut=1
2. ✅ Resepsionis create appointment day 1 second → no_urut=2
3. ✅ Resepsionis create appointment day 2 same doctor → no_urut=1 (daily reset!)
4. ✅ Resepsionis create multiple pets same doctor same day → sequence increments
5. ❌ Resepsionis create fail - missing idreservasi → validation error
6. ❌ Resepsionis create fail - invalid pet ID → validation error
7. ❌ Admin attempt override Resepsionis (different controller) → verify separate controllers
8. ❌ Perawat attempt create via Resepsionis route → 404 or middleware block

### Read/List
9. ✅ Resepsionis list appointments → view queue for today
10. ✅ Resepsionis list grouped by doctor & date → proper organization

### Cancel
11. ✅ Resepsionis cancel appointment → soft delete with deleted_by set
12. ❌ Dokter attempt cancel via Resepsionis route → 404

---

## SCOPE 3: RekamMedis - Admin Features (14 scenarios)

### Create (Atomic Transaction)
1. ✅ Admin create RekamMedis with detail → both records created (1 RekamMedis + 1 DetailRekamMedis)
2. ✅ Admin create → appointment status changes 'W' to 'D'
3. ✅ Admin create with 1000-char fields → stored completely, no truncation
4. ❌ Admin create fail - anamnesa empty → validation error
5. ❌ Admin create fail - diagnosa empty → validation error
6. ❌ Admin create fail - detail empty → validation error
7. ❌ Admin create fail - invalid therapy code → validation error
8. ❌ Admin create fail - invalid appointment ID → validation error
9. ❌ Perawat attempt create (different flow - no detail) → verify Perawat route exists
10. ❌ Dokter attempt create (no route) → 404

### Read/List
11. ✅ Admin list all RekamMedis → view all with relationships (pet, owner, doctor, details)
12. ✅ Admin view RekamMedis with multiple details → all details shown

### Update
13. ✅ Admin update clinical findings → anamnesa, temuan_klinis, diagnosa updated
14. ✅ Admin update detail (multiple exist) → only FIRST detail updated (limitation!)

---

## SCOPE 4: RekamMedis - Perawat Features (12 scenarios)

### Create (Without Detail)
1. ✅ Perawat create RekamMedis → only main record created (NO DetailRekamMedis)
2. ✅ Perawat create → appointment status STAYS 'W' (NOT updated to 'D' - inconsistency!)
3. ✅ Perawat create with clinical findings → anamnesa, temuan_klinis, diagnosa stored
4. ❌ Perawat create fail - anamnesa missing → validation error
5. ❌ Perawat create fail - invalid appointment ID → validation error
6. ❌ Perawat create with detail field → ignored/not used (verify behavior)

### Read/List
7. ✅ Perawat list all RekamMedis → view all records
8. ✅ Perawat view detail → read-only access to record
9. ❌ Pemilik attempt view via Perawat route → 404 or filtered

### Update
10. ✅ Perawat update clinical findings → anamnesa, temuan, diagnosa changed
11. ❌ Perawat update fail - diagnosa empty → validation error
12. ❌ Perawat attempt update detail (no route) → 404

---

## SCOPE 5: DetailRekamMedis - Dokter Features (11 scenarios)

### Create (Add Treatment)
1. ✅ Dokter add detail to own RekamMedis → detail created with therapy code & description
2. ❌ Dokter add detail fail - empty description → validation error
3. ❌ Dokter add detail fail - invalid RekamMedis ID → 404 or error
4. ❌ Dokter attempt add to OTHER dokter's RekamMedis → "Anda tidak memiliki akses"
5. ❌ Perawat attempt add detail (no route) → 404
6. ❌ Admin attempt add detail directly (no route) → 404

### Update (Edit Treatment)
7. ✅ Dokter update own detail → therapy code & description changed
8. ❌ Dokter update fail - invalid therapy code → validation error
9. ❌ Dokter attempt update OTHER dokter's detail → "Anda tidak memiliki akses"

### Delete (Remove Treatment)
10. ✅ Dokter delete own detail → soft deleted (deleted_at set, deleted_by set)
11. ❌ Dokter attempt delete OTHER dokter's detail → "Anda tidak memiliki akses"

---

## Summary per Scope

| Scope | Module | Role | Positive | Negative | Auth Check | Total |
|-------|--------|------|----------|----------|-----------|-------|
| 1 | TemuDokter | Admin | 6 | 4 | 3 | 13 |
| 2 | TemuDokter | Resepsionis | 6 | 4 | 2 | 12 |
| 3 | RekamMedis | Admin | 5 | 5 | 4 | 14 |
| 4 | RekamMedis | Perawat | 5 | 4 | 3 | 12 |
| 5 | DetailRekamMedis | Dokter | 6 | 5 | 4 | 15 |
| **TOTAL** | | | **28** | **22** | **16** | **66** |

---

## Test Execution Notes

### Auth Pattern untuk Setiap Scope
- **Positive**: Login dengan role yang sesuai (Admin, Resepsionis, Dokter, atau Perawat)
- **Negative**: Coba login dengan role BERBEDA (cross-role attempts)
  - Scope 1 (Admin): Try Perawat, Dokter, Pemilik
  - Scope 2 (Resepsionis): Try Perawat, Admin route, Dokter
  - Scope 3 (Admin RekamMedis): Try Dokter, Perawat, Pemilik
  - Scope 4 (Perawat RekamMedis): Try Dokter, Admin, Pemilik
  - Scope 5 (Dokter Details): Try OTHER Dokter, Perawat, Admin, Pemilik
- **Unauthenticated**: Jika diperlukan, test tanpa login → redirect to login page

### Key Testing Patterns
- **Happy Path (✅)**: User terauthorisasi melakukan aksi sesuai role → success
- **Validation (❌)**: Missing/invalid required fields → validation error
- **Authorization (❌)**: Role lain mencoba akses → 404 or "Anda tidak memiliki akses"
- **Edge Cases**: Status transitions, soft delete, multiple records, max length

### Database Assertions untuk Setiap Test
- Positive: `assertDatabaseHas()` untuk verify created/updated data
- Negative: `assertDatabaseMissing()` atau `assertDatabaseCount()` untuk verify NOT created
- Auth fail: Verify response status (404, 403) atau redirect, dan DB unchanged

### Expected Inconsistencies/Issues to Document
1. **No_urut Difference**: Admin (global) vs Resepsionis (daily) - design choice or bug?
2. **Perawat Status Gap**: Perawat creates RekamMedis but doesn't update appointment status → incomplete workflow
3. **Admin Detail Update**: Only FIRST detail updated if multiple exist → functionality gap
4. **Inactive Doctor**: Admin can create appointment with inactive doctor (no validation)
5. **1:1 Constraint**: Verify if preventing duplicate RekamMedis per appointment
