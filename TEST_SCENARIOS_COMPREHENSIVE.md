# Comprehensive Test Scenarios: TemuDokter & RekamMedis
**Veterinary Clinic Management System - Integration Testing Plan**
**Date**: 2026-04-05
**Total Scenarios**: 85+ test cases
**Coverage**: All roles, positive/negative/edge cases

---

## Executive Summary

This document outlines comprehensive integration test scenarios for the TemuDokter (Appointment) and RekamMedis (Medical Records) modules. Testing covers:

- **5 User Roles**: Admin, Resepsionis, Dokter, Perawat, Pemilik
- **6 Operations**: Create, Read/List, Update, Delete, Cancel, Authorization
- **3 Scenario Types**: Happy Path (positive), Error Cases (validation), Edge Cases (business logic)
- **Distribution**: 5 developers, ~17 test cases each

---

# PART 1: TEMUDOKTER TEST SCENARIOS

## Module 1A: TemuDokter - Create (Appointment Creation)

### Category 1A1: Admin Create - Positive Cases

**T1A1-001: Admin creates appointment with valid pet and doctor**
```
Precondition: Admin logged in, Pet exists, Active doctor exists
Steps:
  1. POST /Admin/TemuDokter/store-temu-dokter with valid idpet & idrole_user
  2. Verify response redirects to daftar-temu-dokter
  3. Verify session has 'success' message
  4. Verify TemuDokter created in DB with status='W'
  5. Verify no_urut generated (global increment)
Expected: Appointment created successfully
Database: temu_dokter count +1, status='W'
```

**T1A1-002: Admin creates multiple appointments for same doctor (no_urut sequence)**
```
Precondition: Admin logged in
Steps:
  1. Create appointment 1 for Doctor X
  2. Verify no_urut = 1
  3. Create appointment 2 for Doctor X (same or next day)
  4. Verify no_urut = 2
  5. Query all appointments for Doctor X
Expected: no_urut correctly incremented globally (NOT daily reset)
Business Logic: Admin uses all-time numbering (different from Resepsionis)
```

**T1A1-003: Admin creates appointments for different doctors (separate sequences)**
```
Precondition: Two active doctors exist
Steps:
  1. Create appointment for Doctor A
  2. Verify Doctor A no_urut = 1
  3. Create appointment for Doctor B
  4. Verify Doctor B no_urut = 1 (not 2)
Expected: Each doctor has independent no_urut sequence
```

**T1A1-004: Admin creates appointment with new pet (via factory)**
```
Precondition: Admin logged in, Pet factory available
Steps:
  1. Create new Pet via factory
  2. POST with this new pet ID
  3. Verify appointment created with correct idpet
Expected: Appointment linked to correct pet
```

**T1A1-005: Admin creates appointment for inactive doctor (should succeed)**
```
Precondition: Doctor with status=0 (inactive)
Steps:
  1. POST appointment with inactive doctor idrole_user
  2. Verify appointment created successfully
Expected: Admin can create for any doctor (validation missing)
Risk: Business logic may not enforce active doctor requirement
```

---

### Category 1A2: Admin Create - Negative Cases

**T1A1-006: Admin create fails - missing idpet**
```
Precondition: Admin logged in
Steps:
  1. POST without idpet field
  2. Verify validation error returned
Expected: Validation error on idpet field
Database: No appointment created
```

**T1A1-007: Admin create fails - missing idrole_user**
```
Precondition: Admin logged in
Steps:
  1. POST without idrole_user field
  2. Verify validation error
Expected: Validation error on idrole_user field
```

**T1A1-008: Admin create fails - invalid idpet (non-existent)**
```
Precondition: Admin logged in
Steps:
  1. POST with idpet=99999 (doesn't exist)
  2. Verify validation error
Expected: Validation error: "idpet must exist"
```

**T1A1-009: Admin create fails - invalid idrole_user (non-existent)**
```
Precondition: Admin logged in
Steps:
  1. POST with idrole_user=99999
  2. Verify validation error
Expected: Validation error: "idrole_user must exist"
```

**T1A1-010: Admin create fails - non-admin user (authorization)**
```
Precondition: Perawat logged in
Steps:
  1. POST /Admin/TemuDokter/store-temu-dokter
  2. Verify 404 or 403 error
Expected: Access denied, route not accessible for Perawat
```

---

### Category 1A3: Resepsionis Create - Positive Cases

**T1A3-011: Resepsionis creates appointment with daily no_urut reset**
```
Precondition: Resepsionis logged in, Day 1
Steps:
  1. Create appointment for Doctor X on Day 1
  2. Verify no_urut = 1
  3. Create second appointment for Doctor X on Day 1
  4. Verify no_urut = 2
  5. Travel to Day 2 (Carbon::tomorrow())
  6. Create appointment for Doctor X on Day 2
  7. Verify no_urut = 1 (RESET, not 3)
Expected: Daily reset per doctor (different from Admin)
Key Test: Validates time-based logic
```

**T1A3-012: Resepsionis creates first appointment of day**
```
Precondition: Resepsionis logged in, Fresh day
Steps:
  1. POST appointment for Doctor X
  2. Verify no_urut = 1
Expected: First appointment gets no_urut=1
```

**T1A3-013: Resepsionis creates appointment with multiple pets same day**
```
Precondition: Multiple pets exist
Steps:
  1. Create appointment for Pet A, Doctor X
  2. Create appointment for Pet B, Doctor X (same day)
  3. Verify both appointments have different no_urut (1, 2)
  4. Verify both linked to same doctor
Expected: Sequence increments per doctor per day
```

---

### Category 1A4: Resepsionis Create - Negative Cases

**T1A4-014: Resepsionis create fails - non-existent pet**
```
Precondition: Resepsionis logged in
Steps:
  1. POST with invalid idpet
  2. Verify validation error
Expected: Validation error
```

**T1A4-015: Resepsionis create fails - authorization (not logged in)**
```
Precondition: Not authenticated
Steps:
  1. POST /Resepsionis/TemuDokter/store-temu-dokter
  2. Verify redirect to login
Expected: Redirected to authentication
```

---

### Category 1A5: Other Roles - Authorization Check

**T1A5-016: Perawat cannot create appointment (route doesn't exist)**
```
Precondition: Perawat logged in
Steps:
  1. Attempt POST /Perawat/TemuDokter/store-temu-dokter
  2. Verify 404 error
Expected: Route not found for Perawat
Business Rule: Only Admin & Resepsionis can create appointments
```

**T1A5-017: Dokter cannot create appointment**
```
Precondition: Dokter logged in
Steps:
  1. Attempt POST /Dokter/TemuDokter/store-temu-dokter
  2. Verify 404 error
Expected: Route not found
```

**T1A5-018: Pemilik cannot create appointment**
```
Precondition: Pemilik logged in
Steps:
  1. Attempt POST /Pemilik/TemuDokter/store-temu-dokter
  2. Verify 404 error
Expected: Route not found
```

---

## Module 1B: TemuDokter - Read/List (View Appointments)

### Category 1B1: Admin List - Positive Cases

**T1B1-019: Admin lists all appointments**
```
Precondition: Multiple appointments exist
Steps:
  1. GET /Admin/TemuDokter/daftar-temu-dokter
  2. Verify response contains all appointments
  3. Verify pagination/count correct
Expected: All appointments visible with relationships loaded
```

**T1B1-020: Admin filters appointments by date (today)**
```
Precondition: Appointments exist from today and previous days
Steps:
  1. GET /Admin/TemuDokter/daftar-temu-dokter?filter=today
  2. Verify only today's appointments returned
Expected: Date filter works correctly
```

**T1B1-021: Admin filters appointments by doctor**
```
Precondition: Appointments for different doctors exist
Steps:
  1. GET /Admin/TemuDokter/daftar-temu-dokter?dokter={id}
  2. Verify only appointments for that doctor shown
Expected: Doctor filter works
```

**T1B1-022: Admin views appointment details (eager load)**
```
Precondition: Appointment with relationships exists
Steps:
  1. GET list appointments
  2. Verify pet name, doctor name, owner loaded (no N+1)
Expected: All relationships populated
```

---

### Category 1B2: Resepsionis List - Positive Cases

**T1B2-023: Resepsionis lists appointments with daily grouping**
```
Precondition: Multiple appointments from different days
Steps:
  1. GET /Resepsionis/TemuDokter/daftar-temu-dokter
  2. Verify appointments grouped by doctor & date
Expected: Proper organization for queue management
```

---

### Category 1B3: View Access Control

**T1B3-024: Perawat cannot access list (route doesn't exist)**
```
Precondition: Perawat logged in
Steps:
  1. Attempt GET /Perawat/TemuDokter/daftar-temu-dokter
  2. Verify 404
Expected: Route not found
```

**T1B3-025: Pemilik can view only own appointments**
```
Precondition: Pemilik logged in
Steps:
  1. GET /Pemilik/TemuDokter/daftar-reservasi-saya
  2. Verify only own pet appointments shown
Expected: Filtered to Pemilik's pets only
```

---

## Module 1C: TemuDokter - Cancel/Delete

### Category 1C1: Admin Cancel - Positive Cases

**T1C1-026: Admin cancels appointment soft delete**
```
Precondition: Appointment exists
Steps:
  1. PUT /Admin/TemuDokter/cancel-temu-dokter/{id}
  2. Verify response redirects to list
  3. Verify session has 'success' message
  4. Check temu_dokter table: deleted_at is set, deleted_by is set
Expected: Soft delete executed, audit trail recorded
Database: deleted_at != null, deleted_by = admin's iduser
```

**T1C1-027: Admin cancels appointment removes from list**
```
Precondition: Appointment exists
Steps:
  1. Cancel appointment
  2. GET list (default query)
  3. Verify cancelled appointment NOT in list
Expected: Soft deleted records excluded from list
```

**T1C1-028: Admin cancels appointment with existing RekamMedis**
```
Precondition: Appointment with RekamMedis exists
Steps:
  1. Cancel appointment
  2. Verify soft delete succeeds
  3. Verify RekamMedis still exists (not cascaded)
Expected: Appointment soft deleted, RekamMedis unaffected
Connection: Check if RekamMedis orphaned or foreign key constraint
```

**T1C1-029: Admin cancels multiple appointments**
```
Precondition: Multiple appointments exist
Steps:
  1. Cancel appointment 1
  2. Cancel appointment 2
  3. Verify both soft deleted with different deleted_at timestamps
Expected: Each has unique deletion timestamp
```

---

### Category 1C2: Admin Cancel - Error Cases

**T1C2-030: Admin cancel fails - invalid appointment ID**
```
Precondition: Admin logged in
Steps:
  1. PUT /Admin/TemuDokter/cancel-temu-dokter/99999
  2. Verify error response (404 or validation error)
Expected: Error handling
```

**T1C2-031: Admin cancel fails - already cancelled**
```
Precondition: Appointment already soft deleted
Steps:
  1. PUT cancel same appointment twice
  2. Verify appropriate error or idempotent behavior
Expected: Either error or idempotent (succeeds both times)
```

---

### Category 1C3: Resepsionis Cancel - Positive Cases

**T1C3-032: Resepsionis cancels appointment**
```
Precondition: Resepsionis logged in, Appointment exists
Steps:
  1. PUT /Resepsionis/TemuDokter/cancel-temu-dokter/{id}
  2. Verify soft delete succeeds
Expected: Soft delete with deleted_by = resepsionis's iduser
```

---

### Category 1C4: Cancel Authorization

**T1C4-033: Perawat cannot cancel (no route)**
```
Precondition: Perawat logged in
Steps:
  1. PUT /Perawat/TemuDokter/cancel-temu-dokter/1
  2. Verify 404
Expected: Route not found
```

---

## Module 1D: TemuDokter - Edge Cases & Business Logic

### Category 1D1: Status & Workflow

**T1D1-034: Appointment status initially 'W' (menunggu)**
```
Precondition: New appointment created
Steps:
  1. Create appointment
  2. Query DB directly
  3. Verify status = 'W'
Expected: Initial status is waiting
Database: temu_dokter.status = 'W'
```

**T1D1-035: Appointment status changes to 'D' when RekamMedis created (by Admin)**
```
Precondition: Appointment exists with status='W'
Steps:
  1. Admin creates RekamMedis for this appointment
  2. Query appointment from DB
  3. Verify status = 'D'
Expected: Status automatically updated
Trigger Point: When RekamMedis created
```

**T1D1-036: Cannot create RekamMedis for appointment with invalid ID**
```
Precondition: Invalid idreservasi_dokter value
Steps:
  1. Attempt to create RekamMedis with non-existent appointment ID
  2. Verify validation error
Expected: Foreign key constraint or validation error
```

---

### Category 1D2: Doctor Status Validation

**T1D2-037: Cannot create appointment with inactive doctor (SHOULD but DOESN'T)**
```
Precondition: Doctor with status=0 exists
Steps:
  1. Admin creates appointment with inactive doctor
  2. Observe behavior
Expected: SHOULD fail with validation error
Actual: MAY succeed (validation gap)
Risk: Business logic issue identified in review
```

**T1D2-038: Active doctor has status=1 in role_user table**
```
Precondition: Active doctor exists
Steps:
  1. Verify role_user.status = 1 for doctor
  2. Create appointment
  3. Verify succeeds
Expected: Creates successfully for active doctors
```

---

### Category 1D3: Time & Timing Issues

**T1D3-039: Appointment waktu_daftar is auto-set to current time**
```
Precondition: Appointment created
Steps:
  1. Create appointment
  2. Check waktu_daftar in DB
  3. Verify equals or close to now()
Expected: Timestamp auto-populated
```

**T1D3-040: Daily reset considers waittime_daftar for Resepsionis sequence**
```
Precondition: Multiple appointments same doctor, different dates
Steps:
  1. Day 1: Create 2 appointments
  2. Day 2: Create 1 appointment
  3. Day 3: Create 1 appointment
  4. Verify no_urut resets each day for Resepsionis
Expected: Based on date of waktu_daftar, not insert order
```

---

### Category 1D4: Duplicate/Constraint Testing

**T1D4-041: Can create multiple appointments same pet same day**
```
Precondition: Pet exists
Steps:
  1. Create appointment for Pet A, Doctor X, Day 1
  2. Create another for Pet A, Doctor X, Day 1
  3. Verify both created (no unique constraint)
Expected: Multiple appointments allowed for same pet
```

**T1D4-042: Can create appointment same pet different doctors same day**
```
Precondition: Pet exists, multiple doctors exist
Steps:
  1. Create appointment Pet A, Doctor X
  2. Create appointment Pet A, Doctor Y (same day)
  3. Verify both created
Expected: No constraint preventing this
Business Rule: May be valid (multiple examinations)
```

---

# PART 2: REKAMMEDIS TEST SCENARIOS

## Module 2A: RekamMedis - Create (All Roles)

### Category 2A1: Admin Create - Positive Cases

**T2A1-001: Admin creates RekamMedis with detail in one transaction**
```
Precondition: Admin logged in, Valid appointment (W status) exists
Steps:
  1. POST /Admin/RekamMedis/store-rekam-medis with:
     - idreservasi_dokter: valid appointment ID
     - anamnesa: valid text
     - temuan_klinis: valid text
     - diagnosa: valid text
     - detail: valid treatment description
     - idkode_tindakan_terapi: valid therapy code ID
  2. Verify response redirects to daftar-rekam-medis
  3. Verify session has 'success' message
  4. Check DB: RekamMedis created with all fields
  5. Check DB: DetailRekamMedis created with idkode_tindakan_terapi
  6. Check DB: TemuDokter status changed from 'W' to 'D'
Expected: Atomic transaction - all created successfully together
Database: +1 row in rekam_medis, +1 row in detail_rekam_medis, appointment updated
```

**T2A1-002: Admin creates RekamMedis with maximum length fields (1000 chars)**
```
Precondition: Admin logged in, Appointment exists
Steps:
  1. POST with each text field = 1000 characters
  2. Verify created successfully
Expected: Fields stored completely, no truncation
```

**T2A1-003: Admin creates RekamMedis with long therapy description**
```
Precondition: Valid appointment, therapy code exists
Steps:
  1. POST with detail field = long multi-line treatment description
  2. Verify created successfully
Expected: Long text preserved in DB
```

**T2A1-004: Admin creates RekamMedis assigns dokter_pemeriksa correctly**
```
Precondition: Admin logged in
Steps:
  1. Create RekamMedis
  2. Check dokter_pemeriksa field in DB
  3. Verify set to current admin's idrole_user or expected doctor
Expected: Correct doctor ID recorded
Business Logic: Who should be dokter_pemeriksa for admin-created? Verify logic
```

---

### Category 2A2: Admin Create - Validation Failures

**T2A2-005: Admin create RekamMedis fails - anamnesa kosong**
```
Precondition: Admin logged in
Steps:
  1. POST with anamnesa = "" (empty)
  2. Verify validation error on anamnesa
Expected: Validation message
```

**T2A2-006: Admin create RekamMedis fails - anamnesa exceeds 1000 chars**
```
Precondition: Admin logged in
Steps:
  1. POST with anamnesa = 1001 characters
  2. Verify validation error
Expected: Max length validation error
```

**T2A2-007: Admin create RekamMedis fails - temuan_klinis kosong**
```
Precondition: Admin logged in
Steps:
  1. POST with temuan_klinis = ""
  2. Verify validation error
Expected: Validation error on field
```

**T2A2-008: Admin create RekamMedis fails - diagnosa kosong**
```
Precondition: Admin logged in
Steps:
  1. POST with diagnosa = ""
  2. Verify validation error
Expected: Validation error
```

**T2A2-009: Admin create RekamMedis fails - detail kosong**
```
Precondition: Admin logged in
Steps:
  1. POST with detail = ""
  2. Verify validation error
Expected: Validation error (detail required because therapy code needs description)
```

**T2A2-010: Admin create RekamMedis fails - invalid idkode_tindakan_terapi**
```
Precondition: Admin logged in
Steps:
  1. POST with idkode_tindakan_terapi = 99999 (doesn't exist)
  2. Verify validation error
Expected: Foreign key validation error
```

**T2A2-011: Admin create RekamMedis fails - invalid idreservasi_dokter**
```
Precondition: Admin logged in
Steps:
  1. POST with idreservasi_dokter = 99999
  2. Verify validation error
Expected: Foreign key validation error
```

**T2A2-012: Admin create RekamMedis fails - appointment already has RekamMedis**
```
Precondition: Appointment already has RekamMedis created
Steps:
  1. Attempt to create second RekamMedis for same appointment
  2. Observe behavior
Expected: SHOULD fail (1:1 relationship), check if validation exists
Risk: Potential duplicate records if validation missing
```

---

### Category 2A3: Perawat Create - Positive Cases

**T2A3-013: Perawat creates RekamMedis WITHOUT detail**
```
Precondition: Perawat logged in, Appointment (W status) exists
Steps:
  1. POST /Perawat/RekamMedis/store-rekam-medis with:
     - idreservasi_dokter: valid appointment
     - anamnesa: valid text
     - temuan_klinis: valid text
     - diagnosa: valid text
     (NO detail field, NO idkode_tindakan_terapi)
  2. Verify response redirects with success
  3. Check DB: RekamMedis created
  4. Check DB: DetailRekamMedis NOT created
  5. Check DB: TemuDokter status still 'W' (NOT updated to 'D')
Expected: RekamMedis created alone, no detail, no status update
Key Difference: Unlike Admin, Perawat doesn't update appointment status
Risk: Business logic inconsistency - appointment never marked done?
```

**T2A3-014: Perawat creates RekamMedis for appointment without dokter_pemeriksa**
```
Precondition: Perawat logged in, Appointment exists (no doctor assigned yet)
Steps:
  1. Create RekamMedis
  2. Check dokter_pemeriksa in DB
Expected: Verify what Perawat puts in dokter_pemeriksa (self, null, or error?)
Concern: Business logic - who's the examining doctor if Perawat creates?
```

---

### Category 2A4: Perawat Create - validation

**T2A4-015: Perawat create fails - anamnesa missing**
```
Precondition: Perawat logged in
Steps:
  1. POST without anamnesa
  2. Verify validation error
Expected: Validation error
```

**T2A4-016: Perawat create fails - invalid idreservasi_dokter**
```
Precondition: Perawat logged in
Steps:
  1. POST with invalid appointment ID
  2. Verify validation error
Expected: Foreign key error
```

**T2A4-017: Perawat create fails - trying to add detail (should not exist)**
```
Precondition: Perawat logged in
Steps:
  1. POST with detail & idkode_tindakan_terapi fields
  2. Observe behavior (assumed ignored by controller)
Expected: Fields ignored or accepted but not used
Business: Perawat doesn't create details
```

---

### Category 2A5: Dokter & Pemilik Create - Authorization

**T2A5-018: Dokter cannot create RekamMedis (no route)**
```
Precondition: Dokter logged in
Steps:
  1. POST /Dokter/RekamMedis/store-rekam-medis
  2. Verify 404 or 403
Expected: Route not found
Business Rule: Dokter can only add details, not create main record
```

**T2A5-019: Pemilik cannot create RekamMedis**
```
Precondition: Pemilik logged in
Steps:
  1. Attempt POST /Pemilik/RekamMedis/store-rekam-medis
  2. Verify 404
Expected: Route not found (read-only access only)
```

---

## Module 2B: RekamMedis - Read/List (View Medical Records)

### Category 2B1: Admin List

**T2B1-020: Admin lists all RekamMedis records**
```
Precondition: Multiple records exist
Steps:
  1. GET /Admin/RekamMedis/daftar-rekam-medis
  2. Verify all records visible
  3. Verify relationships loaded (pet name, owner, doctor, details)
Expected: Complete list with eager loading
```

**T2B1-021: Admin views RekamMedis with details**
```
Precondition: RekamMedis with multiple details exists
Steps:
  1. Access RekamMedis detail view
  2. Verify all DetailRekamMedis shown
Expected: All treatments/procedures visible
```

---

### Category 2B2: Dokter List

**T2B2-022: Dokter lists all RekamMedis (not filtered)**
```
Precondition: Dokter logged in, Multiple RekamMedis exist
Steps:
  1. GET /Dokter/RekamMedis/daftar-rekam-medis
  2. Verify all records visible (not filtered by dokter)
Expected: Read access to all records
```

**T2B2-023: Dokter views RekamMedis detail**
```
Precondition: RekamMedis exists
Steps:
  1. GET /Dokter/RekamMedis/detail-rekam-medis/{id}
  2. Verify authorization check passes
  3. Verify all details shown
Expected: Detail view allowed
```

---

### Category 2B3: Perawat List

**T2B3-024: Perawat lists all RekamMedis with details**
```
Precondition: Perawat logged in, Records exist
Steps:
  1. GET /Perawat/RekamMedis/daftar-rekam-medis
  2. Verify all records visible
Expected: Full read access
```

**T2B3-025: Perawat views RekamMedis detail**
```
Precondition: RekamMedis exists
Steps:
  1. GET /Perawat/RekamMedis/detail-rekam-medis/{id}
  2. Verify detail view shown
Expected: Read access
```

---

### Category 2B4: Pemilik List - Authorization

**T2B4-026: Pemilik lists only own pet's RekamMedis**
```
Precondition: Pemilik logged in, Multiple records exist for different pets
Steps:
  1. GET /Pemilik/RekamMedis/daftar-rekam-medis
  2. Verify only records for own pet shown
  3. Verify cannot see other pemilik's pets' records
Expected: Filtered by pemilik.iduser = auth()->id()
Authorization: Pemilik-level access control
```

**T2B4-027: Pemilik cannot view other pemilik's pet record**
```
Precondition: Two pets from different pemilik
Steps:
  1. Login as Pemilik A
  2. Attempt to GET detail of Pemilik B's pet record
  3. Verify 403 or error
Expected: Access denied
Authorization Check: Pet ownership validation
```

**T2B4-028: Pemilik views detail of own pet's RekamMedis**
```
Precondition: Pemilik A has pet, record exists
Steps:
  1. GET /Pemilik/RekamMedis/detail-rekam-medis/{id}
  2. Verify detail visible
  3. Verify read-only (no edit/delete buttons)
Expected: View allowed, write denied
```

---

## Module 2C: RekamMedis - Update (Edit)

### Category 2C1: Admin Update - Positive Cases

**T2C1-029: Admin updates RekamMedis clinical findings**
```
Precondition: RekamMedis exists with current data
Steps:
  1. GET view/edit form
  2. PUT with new anamnesa, temuan_klinis, diagnosa values
  3. Verify response redirects with success
  4. Check DB: Fields updated with new values
Expected: Update succeeds, data changed
```

**T2C1-030: Admin updates RekamMedis first detail therapy code & description**
```
Precondition: RekamMedis with 1 detail exists
Steps:
  1. PUT with new idkode_tindakan_terapi & detail text
  2. Verify detail record updated
Expected: Detail therapy code changed
Limitation: Only FIRST detail updated if multiple exist
```

**T2C1-031: Admin updates RekamMedis with multiple details (only first updated)**
```
Precondition: RekamMedis with 3 details exists
Steps:
  1. PUT with new therapy code & description
  2. Verify first detail updated
  3. Verify second & third details unchanged
Expected: Only first detail modified
Risk: Admin can't update non-first details (gap in functionality)
```

**T2C1-032: Admin updates RekamMedis with maximum length fields**
```
Precondition: RekamMedis exists
Steps:
  1. PUT with 1000-char text fields
  2. Verify updated successfully
Expected: Full length preserved
```

---

### Category 2C2: Admin Update - Validation

**T2C2-033: Admin update fails - anamnesa becomes empty**
```
Precondition: RekamMedis exists with data
Steps:
  1. PUT with anamnesa = ""
  2. Verify validation error
Expected: Validation error on anamnesa
```

**T2C2-034: Admin update fails - diagnosa exceeds 1000 chars**
```
Precondition: RekamMedis exists
Steps:
  1. PUT with diagnosa = 1001 characters
  2. Verify validation error
Expected: Max length validation
```

**T2C2-035: Admin update fails - invalid idkode_tindakan_terapi**
```
Precondition: RekamMedis exists with detail
Steps:
  1. PUT with idkode_tindakan_terapi = 99999
  2. Verify validation error
Expected: Foreign key validation error
```

---

### Category 2C3: Perawat Update

**T2C3-036: Perawat updates RekamMedis clinical findings**
```
Precondition: Perawat logged in, RekamMedis exists
Steps:
  1. PUT /Perawat/RekamMedis/update-rekam-medis/{id}
  2. Update anamnesa, temuan_klinis, diagnosa
  3. Verify updated successfully
Expected: Perawat can update main findings
```

**T2C3-037: Perawat cannot update RekamMedis detail (doesn't have route)**
```
Precondition: Perawat logged in
Steps:
  1. Attempt PUT /Perawat/RekamMedis/update-detail/{id}
  2. Verify 404
Expected: Route not found (detail management is dokter-only)
```

---

### Category 2C4: Dokter & Others - Authorization

**T2C4-038: Dokter cannot update RekamMedis main record**
```
Precondition: Dokter logged in, RekamMedis exists
Steps:
  1. Attempt PUT /Dokter/RekamMedis/update-rekam-medis (should not exist)
  2. Verify 404
Expected: No route for dokter to update main record
```

**T2C4-039: Pemilik cannot update RekamMedis**
```
Precondition: Pemilik logged in
Steps:
  1. Attempt PUT /Pemilik/RekamMedis/update-rekam-medis/{id}
  2. Verify 404 or 403
Expected: No write permission
```

---

## Module 2D: DetailRekamMedis - Create (Add Treatments)

### Category 2D1: Dokter Add Detail - Positive Cases

**T2D1-040: Dokter adds detail to own RekamMedis**
```
Precondition: Dokter A logged in, RekamMedis with dokter_pemeriksa=Dokter A exists
Steps:
  1. POST /Dokter/RekamMedis/store-detail/{idrekam_medis} with:
     - idkode_tindakan_terapi: valid therapy code
     - detail: treatment description
  2. Verify response redirects with success
  3. Check DB: DetailRekamMedis created with correct idrekam_medis & therapy code
Expected: Detail added successfully
Database: +1 row in detail_rekam_medis
```

**T2D1-041: Dokter adds multiple details to same RekamMedis**
```
Precondition: Dokter logged in, RekamMedis owned by dokter
Steps:
  1. Add detail 1 (treatment A)
  2. Add detail 2 (treatment B)
  3. Add detail 3 (treatment C)
  4. Verify all 3 details created independently
  5. Query RekamMedis details count = 3
Expected: Multiple treatments allowed, each modifiable independently
```

**T2D1-042: Dokter adds detail with long description**
```
Precondition: Dokter logged in
Steps:
  1. Add detail with 1000-char description
  2. Verify stored completely
Expected: Full text preserved
```

---

### Category 2D2: Dokter Add Detail - Validation

**T2D2-043: Dokter add detail fails - detail text empty**
```
Precondition: Dokter logged in
Steps:
  1. POST with detail = ""
  2. Verify validation error
Expected: Validation error on detail field
```

**T2D2-044: Dokter add detail fails - invalid idkode_tindakan_terapi**
```
Precondition: Dokter logged in
Steps:
  1. POST with idkode_tindakan_terapi = 99999
  2. Verify validation error
Expected: Foreign key validation error
```

**T2D2-045: Dokter add detail fails - invalid idrekam_medis**
```
Precondition: Dokter logged in
Steps:
  1. POST /Dokter/RekamMedis/store-detail/99999
  2. Verify error (record not found)
Expected: 404 or validation error
```

---

### Category 2D3: Dokter Authorization on Detail

**T2D3-046: Dokter CANNOT add detail to another dokter's RekamMedis**
```
Precondition: Dokter A logged in, RekamMedis created by Dokter B exists
Steps:
  1. POST /Dokter/RekamMedis/store-detail/{idrekam_medis_B} (dokter_pemeriksa=Dokter B)
  2. Verify authorization error
Expected: Error: "Anda tidak memiliki akses" or similar
Authorization Check: current_idrole_user == rekam_medis->dokter_pemeriksa
```

**T2D3-047: Dokter authorization check compares idrole_user correctly**
```
Precondition: Setup with explicit idrole_user comparison
Steps:
  1. Create RekamMedis with dokter_pemeriksa = specific idrole_user
  2. Dokter with DIFFERENT idrole_user attempts to add detail
  3. Verify denied
Expected: ID comparison works correctly (not comparing User ID)
```

---

### Category 2D4: Other Roles - Authorization

**T2D4-048: Admin cannot add detail directly (no route)**
```
Precondition: Admin logged in
Steps:
  1. POST /Admin/RekamMedis/store-detail (should not exist)
  2. Verify 404
Expected: Route not for admin (admin creates detail with RekamMedis)
```

**T2D4-049: Perawat cannot add detail**
```
Precondition: Perawat logged in
Steps:
  1. POST /Perawat/RekamMedis/store-detail/{id}
  2. Verify 404
Expected: Route not found (perawat cannot manage details)
```

**T2D4-050: Pemilik cannot add detail**
```
Precondition: Pemilik logged in
Steps:
  1. POST /Pemilik/RekamMedis/store-detail/{id}
  2. Verify 404
Expected: Route not found
```

---

## Module 2E: DetailRekamMedis - Update & Delete

### Category 2E1: Dokter Update Detail - Positive Cases

**T2E1-051: Dokter updates own detail**
```
Precondition: Dokter A logged in, Detail owned by Dokter A exists
Steps:
  1. PUT /Dokter/RekamMedis/update-detail/{iddetail} with:
     - idkode_tindakan_terapi: new therapy code
     - detail: new description
  2. Verify response success
  3. Check DB: DetailRekamMedis updated
Expected: Detail changed successfully
```

**T2E1-052: Dokter updates detail therapy code**
```
Precondition: Detail with therapy code A
Steps:
  1. PUT with new therapy code B
  2. Verify code changed in DB
Expected: Therapy code updated
```

**T2E1-053: Dokter updates detail description**
```
Precondition: Detail with description A
Steps:
  1. PUT with new description B (1000 chars)
  2. Verify updated
Expected: Description changed, full length preserved
```

---

### Category 2E2: Dokter Update Detail - Validation

**T2E2-054: Dokter update detail fails - empty description**
```
Precondition: Detail exists
Steps:
  1. PUT with detail = ""
  2. Verify validation error
Expected: Validation error
```

**T2E2-055: Dokter update detail fails - invalid therapy code**
```
Precondition: Detail exists
Steps:
  1. PUT with idkode_tindakan_terapi = 99999
  2. Verify validation error
Expected: Foreign key validation error
```

---

### Category 2E3: Dokter Update Detail - Authorization

**T2E3-056: Dokter CANNOT update detail of another dokter**
```
Precondition: Dokter A logged in, Detail owned by Dokter B exists
Steps:
  1. PUT /Dokter/RekamMedis/update-detail/{detail_B}
  2. Verify authorization error
Expected: Error: "Anda tidak memiliki akses"
Checks: RekamMedis lookup, then dokter comparison
```

**T2E3-057: Dokter update detail fails - detail not found**
```
Precondition: Dokter logged in
Steps:
  1. PUT /Dokter/RekamMedis/update-detail/99999
  2. Verify 404 or error
Expected: Detail not found error
```

---

### Category 2E4: Dokter Delete Detail - Positive Cases

**T2E4-058: Dokter soft deletes own detail**
```
Precondition: Dokter A logged in, Detail owned by Dokter A exists
Steps:
  1. DELETE /Dokter/RekamMedis/delete-detail/{iddetail}
  2. Verify response redirects with success
  3. Check DB: deleted_at != null, deleted_by = dokter's iduser
Expected: Soft delete executed, audit trail recorded
Database: detail soft deleted
```

**T2E4-059: Dokter delete detail removes from list**
```
Precondition: Detail exists, visible in list
Steps:
  1. Delete detail
  2. Query RekamMedis details (should exclude soft deleted)
  3. Verify detail not in list
Expected: Soft deleted excluded from queries
```

**T2E4-060: Dokter deletes one of multiple details**
```
Precondition: RekamMedis with 3 details, all by Dokter A
Steps:
  1. Delete detail 2
  2. Verify details 1 & 3 still exist
  3. Verify detail 2 soft deleted
Expected: Only target detail deleted
```

---

### Category 2E5: Dokter Delete Detail - Authorization

**T2E5-061: Dokter CANNOT delete detail of another dokter**
```
Precondition: Dokter A logged in, Detail by Dokter B exists
Steps:
  1. DELETE /Dokter/RekamMedis/delete-detail/{detail_B}
  2. Verify authorization error
Expected: Error: "Anda tidak memiliki akses"
```

**T2E5-062: Dokter delete detail fails - detail not found**
```
Precondition: Dokter logged in
Steps:
  1. DELETE /Dokter/RekamMedis/delete-detail/99999
  2. Verify 404 or error
Expected: Handle gracefully
```

---

### Category 2E6: Other Roles - Delete Detail

**T2E6-063: Admin cannot delete detail (no route)**
```
Precondition: Admin logged in
Steps:
  1. DELETE /Admin/RekamMedis/delete-detail/{id}
  2. Verify 404
Expected: Route not found (admin deletes via RekamMedis delete)
```

**T2E6-064: Perawat cannot delete detail**
```
Precondition: Perawat logged in
Steps:
  1. DELETE /Perawat/RekamMedis/delete-detail/{id}
  2. Verify 404
Expected: Route not found
```

---

## Module 2F: RekamMedis - Delete (Full Record)

### Category 2F1: Admin Delete - Positive Cases

**T2F1-065: Admin deletes RekamMedis reverts appointment to 'W'**
```
Precondition: RekamMedis exists (status='D'), with details
Steps:
  1. DELETE /Admin/RekamMedis/delete-rekam-medis/{id}
  2. Verify response redirects with success
  3. Check DB: RekamMedis soft deleted (deleted_at != null)
  4. Check DB: All DetailRekamMedis soft deleted first
  5. Check DB: TemuDokter status reverted to 'W'
Expected: Cascade soft delete + status revert
Workflow: Enables re-creating RekamMedis for same appointment
```

**T2F1-066: Admin delete cascades to all details**
```
Precondition: RekamMedis with 5 details
Steps:
  1. DELETE RekamMedis
  2. Query all details
  3. Verify all 5 soft deleted
Expected: All details deleted with same deleted_at
Database: detail_rekam_medis all have deleted_at set
```

**T2F1-067: Admin delete allows re-creating RekamMedis**
```
Precondition: RekamMedis deleted, appointment reverted to 'W'
Steps:
  1. Create new RekamMedis for same appointment
  2. Verify created successfully
  3. Verify first delete record still exists (soft deleted)
Expected: Can recreate, old records preserved
```

---

### Category 2F2: Admin Delete - Data Integrity

**T2F2-068: Admin delete preserves audit trail**
```
Precondition: RekamMedis exists
Steps:
  1. Delete RekamMedis
  2. Check deleted_by = admin iduser, deleted_at = timestamp
Expected: Audit information recorded
```

**T2F2-069: Admin delete with non-existent RekamMedis**
```
Precondition: Admin logged in
Steps:
  1. DELETE /Admin/RekamMedis/delete-rekam-medis/99999
  2. Verify error handling
Expected: 404 or graceful error
```

---

### Category 2F3: Perawat Delete - Positive Cases

**T2F3-070: Perawat deletes RekamMedis**
```
Precondition: Perawat logged in, RekamMedis exists (no details, created by Perawat)
Steps:
  1. DELETE /Perawat/RekamMedis/delete-rekam-medis/{id}
  2. Verify response success
  3. Check DB: soft deleted
Expected: Perawat can delete own RekamMedis
```

**T2F3-071: Perawat delete reverts appointment status**
```
Precondition: RekamMedis (no details) exists
Steps:
  1. Check if appointment status is 'W' (since Perawat doesn't update on create)
  2. Delete RekamMedis
  3. Verify appointment still 'W' (no change expected)
Expected: Appointment status logic depends on implementation
Concern: Business logic inconsistency (Perawat never sets to 'D')
```

---

### Category 2F4: Delete Authorization

**T2F4-072: Dokter cannot delete RekamMedis (no route)**
```
Precondition: Dokter logged in
Steps:
  1. DELETE /Dokter/RekamMedis/delete-rekam-medis/{id}
  2. Verify 404
Expected: Route not found (dokter manages details, not main record)
```

**T2F4-073: Pemilik cannot delete RekamMedis**
```
Precondition: Pemilik logged in
Steps:
  1. DELETE /Pemilik/RekamMedis/delete-rekam-medis/{id}
  2. Verify 404
Expected: Route not found
```

---

## Module 2G: Cross-Feature Integration & Edge Cases

### Category 2G1: Appointment → RekamMedis Workflow

**T2G1-074: Complete workflow: Create appointment → RekamMedis → Details → Delete**
```
Precondition: All roles available
Steps:
  1. Resepsionis creates appointment (status='W', no_urut=1)
  2. Admin creates RekamMedis with detail (status changed to 'D')
  3. Dokter adds second detail
  4. Dokter edits second detail
  5. Dokter deletes second detail
  6. Admin deletes RekamMedis (status reverted to 'W')
  7. Resepsionis creates new RekamMedis (status changes to 'D' again)
Expected: Complete lifecycle works end-to-end
Key Validates: State transitions, authorization at each step
```

**T2G1-075: Multiple RekamMedis for same appointment (should fail)**
```
Precondition: RekamMedis already exists for appointment
Steps:
  1. Attempt to create second RekamMedis for same appointment
  2. Observe behavior
Expected: SHOULD fail (1:1 relationship), check if validation exists
Risk: If allowed, data integrity issue
```

---

### Category 2G2: Status Consistency

**T2G2-076: Appointment status='D' implies RekamMedis exists**
```
Precondition: Appointment with status='D'
Steps:
  1. Query appointment
  2. Get related RekamMedis
  3. Verify RekamMedis exists
Expected: Consistent state (no status='D' without RekamMedis)
```

**T2G2-077: Cannot create RekamMedis for appointment status='D'**
```
Precondition: Appointment with status='D' already has RekamMedis
Steps:
  1. Attempt second create
  2. Verify denied
Expected: Prevents duplicates
```

---

### Category 2G3: Dokter-Pemilik-Pet Relationships

**T2G3-078: RekamMedis correctly links dokter → pet → pemilik**
```
Precondition: RekamMedis exists
Steps:
  1. Query RekamMedis
  2. Load relationships: dokter → pet → pemilik
  3. Verify chain complete and correct
Expected: All relationships traversable
Database: No orphaned records
```

**T2G3-079: Pemilik can only see own pet's RekamMedis with dokter details**
```
Precondition: Pemilik A owns Pet X, RekamMedis by Dokter Y exists
Steps:
  1. Login Pemilik A
  2. View RekamMedis for Pet X
  3. Verify Dokter Y details shown (but cannot edit)
Expected: Pemilik sees full medical history read-only
Authorization: Filtered by pet ownership
```

---

### Category 2G4: Factory & Test Data

**T2G4-080: TemuDokterFactory creates valid appointment with all relationships**
```
Precondition: Factory available
Steps:
  1. Create appointment via TemuDokterFactory::create()
  2. Verify pet exists, doctor exists, status correct
  3. Verify no_urut generated
Expected: Factory creates complete test data
```

**T2G4-081: RekamMedisFactory creates record with all required fields**
```
Precondition: Factory available
Steps:
  1. Create RekamMedis via factory
  2. Verify all text fields populated
  3. Verify appointment linked
Expected: Factory generates realistic test data
```

**T2G4-082: DetailRekamMedisFactory->forRekamMedis() creates linked detail**
```
Precondition: Factory with relationship helper
Steps:
  1. Create RekamMedis
  2. Create detail via factory->forRekamMedis(rekamMedis)
  3. Verify idrekam_medis matches
Expected: Factory relationship helper works
```

---

### Category 2G5: Soft Delete & Restore Scenarios

**T2G5-083: Soft deleted records excluded from normal queries**
```
Precondition: RekamMedis and Details soft deleted
Steps:
  1. Query RekamMedis::all()
  2. Verify soft deleted records NOT in results
Expected: Soft delete filtering works
Laravel: withoutTrashed() in query
```

**T2G5-084: Can force-query soft deleted records with withTrashed()**
```
Precondition: Records soft deleted
Steps:
  1. Query RekamMedis::withTrashed()
  2. Verify soft deleted records included
Expected: Hidden records retrievable if needed
```

**T2G5-085: Soft delete does not permanently lose data**
```
Precondition: Record soft deleted
Steps:
  1. Query DB directly with deleted_at != null
  2. Verify all data preserved
Expected: No data loss, recovery possible
```

---

# PART 3: TEST DISTRIBUTION FOR 5 DEVELOPERS

## Allocation Strategy

**Basis for Distribution:**
1. **By Module Scope**: Clear boundaries (TemuDokter, RekamMedis Admin/Dokter, RekamMedis Perawat, Cross-Role, Integration)
2. **Balanced Workload**: Each person ~17 test cases
3. **Independence**: Minimal overlap, can work in parallel
4. **Skill Progression**: Mix of basic CRUD and complex authorization/workflows
5. **Business Logic Coverage**: Each person covers positiveegative/edge cases for their scope

---

## PERSON 1: TemuDokter - All Roles (21 test cases)
**Responsibility**: Complete TemuDokter module (Create, Read, Cancel, Edge Cases)
**Test IDs**: T1A1-001 to T1D4-042

**Subtasks:**
1. Create (Positive & Negative):
   - T1A1-001 to T1A4-015 (Admin & Resepsionis create, validation, authorization)
   - Focus: Dual numbering schemes (global vs daily)

2. Read/List (Positive & Authorization):
   - T1B1-019 to T1B3-025 (List by role, filters, access control)
   - Focus: Permission checks per role

3. Cancel/Delete (Positive, Error, Edge):
   - T1C1-026 to T1C4-033 (Soft delete, idempotency, cascade)
   - Focus: State preservation when RekamMedis exists

4. Edge Cases & Business Logic:
   - T1D1-034 to T1D4-042 (Status workflow, doctor validation, duplicates)
   - Focus: State transitions, doctor inactive edge case

**Key Test Scenarios:**
- ✅ Admin creates, generates no_urut globally
- ✅ Resepsionis creates with daily reset
- ✅ Multiple doctors have separate sequences
- ✅ Cancel appointment (soft delete)
- ✅ Status='W' initial, changes to 'D' when RekamMedis created
- ⚠️ Cannot create for inactive doctor (SHOULD but may not)
- ⚠️ Duplicate appointment handling (1 pet, 2 doctors)

**Prerequisites:**
- Pet factory, Doctor/RoleUser factory, Carbon time travel
- Understanding of no_urut logic differences

**Output Files to Create:**
- `tests/Feature/TemuDokterCompleteTest.php`

---

## PERSON 2: RekamMedis - Admin & Create/Update (19 test cases)
**Responsibility**: Admin RekamMedis operations + Perawat create (without details)
**Test IDs**: T2A1-001 to T2A5-019 (Create), T2C1-029 to T2C2-035 (Admin Update)

**Subtasks:**
1. Admin Create (Positive, Validation, Edge):
   - T2A1-001 to T2A2-012 (Atomic transaction, field validation, 1:1 constraint check)
   - Focus: RekamMedis + DetailRekamMedis created together, appointment status → 'D'

2. Perawat Create (Positive, Validation):
   - T2A3-013 to T2A4-017 (Create without detail, no status update)
   - Focus: Business logic inconsistency - appointment doesn't change status

3. Authorization (Create):
   - T2A5-018 to T2A5-019 (Dokter & Pemilik denied)

4. Admin Update (Positive, Validation):
   - T2C1-029 to T2C2-035 (Edit fields, update first detail only, max length)
   - Focus: Multiple details edge case (only first updated)

**Key Test Scenarios:**
- ✅ Admin creates RekamMedis + detail atomically
- ✅ Status changes from 'W' to 'D'
- ✅ All fields validated (max 1000 chars)
- ⚠️ Perawat creates without detail (incomplete workflow)
- ⚠️ Perawat doesn't update appointment status (inconsistency)
- ⚠️ Admin can only update first detail if multiple exist
- ⚠️ Check 1:1 relationship (prevent duplicate RekamMedis per appointment)

**Prerequisites:**
- Appointment with status='W', KodeTindakanTerapi factory, detailed validation knowledge

**Output Files to Create:**
- `tests/Feature/AdminRekamMedisTest.php`
- `tests/Feature/PerawatRekamMedisSimpleTest.php`

---

## PERSON 3: RekamMedis - Perawat Full Operations (18 test cases)
**Responsibility**: Perawat create (done above), update, delete + Dokter authorization pattern
**Test IDs**: T2C3-036 to T2F3-071

**Subtasks:**
1. Perawat Update & Delete:
   - T2C3-036 to T2C3-037 (Update findings, cannot update details)
   - T2F3-070 to T2F3-071 (Delete RekamMedis, status implications)
   - Focus: What Perawat can/cannot do

2. Authorization Patterns (Setup for next person):
   - T2C4-038 to T2C4-039 (Dokter & Pemilik denied on main record)
   - T2D4-048 to T2D4-050 (Admin, Perawat, Pemilik denied on details)
   - T2E6-063 to T2E6-064 (Admin, Perawat denied on detail delete)
   - T2F4-072 to T2F4-073 (Dokter, Pemilik denied on record delete)
   - Focus: Clear authorization matrix

3. Delete Cascade Behavior:
   - T2F2-068 to T2F2-069 (Audit trail, error handling)
   - Focus: Soft delete implementation details

**Key Test Scenarios:**
- ✅ Perawat updates clinical findings
- ✅ Perawat cannot update/delete details
- ✅ Authorization checks prevent cross-role access
- ⚠️ Soft delete doesn't revert status for Perawat-created records (inconsistency)

**Prerequisites:**
- Understanding of authorization patterns from review
- Soft delete queries (withoutTrashed, withTrashed)

**Output Files to Create:**
- `tests/Feature/PerawatRekamMedisFullTest.php`

---

## PERSON 4: DetailRekamMedis - Dokter Management (22 test cases)
**Responsibility**: Dokter add/edit/delete details + complex authorization
**Test IDs**: T2D1-040 to T2E5-062

**Subtasks:**
1. Dokter Add Detail (Positive, Validation):
   - T2D1-040 to T2D2-045 (Create, multiple details, validation)
   - Focus: Independent detail management

2. Dokter Authorization on Detail (Critical):
   - T2D3-046 to T2D3-047 (Cannot add to other dokter's RekamMedis, idrole_user comparison)
   - Focus: `rekam_medis->dokter_pemeriksa == current_idrole_user` check

3. Dokter Update Detail (Positive, Validation, Authorization):
   - T2E1-051 to T2E3-057 (Update therapy code & description, authorization, error handling)
   - Focus: Authorization applies to both add & update

4. Dokter Delete Detail (Positive, Authorization):
   - T2E4-058 to T2E5-062 (Soft delete, cascade to list, authorization, error handling)
   - Focus: Audit trail (deleted_at, deleted_by)

**Key Test Scenarios:**
- ✅ Dokter can add multiple independent details
- ✅ Dokter cannot access another dokter's details
- ✅ Authorization check: idrole_user comparison (not User ID)
- ✅ Soft delete with audit trail
- ✅ Deleted details excluded from list

**Prerequisites:**
- Authorization patterns, soft delete, audit columns (deleted_at, deleted_by)
- Two dokter role_users with different idrole_user values

**Output Files to Create:**
- `tests/Feature/DokterDetailRekamMedisTest.php` (extend existing)

---

## PERSON 5: Cross-Feature Integration & Pemilik (15 test cases)
**Responsibility**: Pemilik read-only access + end-to-end workflows + data integrity
**Test IDs**: T2B4-026 to T2G5-085

**Subtasks:**
1. Pemilik Authorization (Read-Only):
   - T2B4-026 to T2B4-028 (List own records, cannot view other's, detail access)
   - Focus: Ownership-based filtering (pemilik.iduser == auth()->id())

2. Cross-Feature Integration (End-to-End Flows):
   - T2G1-074 to T2G1-075 (Complete workflow: appt → RekamMedis → details → delete)
   - Focus: Multiple roles interacting in workflow

3. Data Integrity & Consistency:
   - T2G2-076 to T2G2-077 (Status consistency, prevent dual RekamMedis)
   - T2G3-078 to T2G3-079 (Relationship chains, Pemilik sees medical history)
   - Focus: No orphaned data, logical consistency

4. Factory & Soft Delete:
   - T2G4-080 to T2G4-082 (Factory creates linked data correctly)
   - T2G5-083 to T2G5-085 (Soft delete filtering, data preservation)
   - Focus: Testing infrastructure reliability

**Key Test Scenarios:**
- ✅ Pemilik views only own pet records
- ✅ Pemilik cannot see other owner's records
- ✅ Read-only enforcement (no edit/delete buttons/routes)
- ✅ Complete workflow: appointment → medical record → treatment → deletion
- ✅ Appointment status properly managed throughout lifecycle
- ✅ Soft delete preserves data (with deleted_at, deleted_by)
- ✓ Factories generate syntactically correct test data
- ⚠️ Prevent duplicate RekamMedis per appointment (1:1 relationship)
- ⚠️ Perawat inconsistency: doesn't update appointment status on create

**Prerequisites:**
- Understanding of all modules (have reviewed all Person 1-4 work)
- Soft delete mechanics, factory usage, role-based filtering
- Carbon time travel for complex scenarios

**Output Files to Create:**
- `tests/Feature/PemilikRekamMedisTest.php`
- `tests/Feature/IntegrationTemuDokterRekamMedisTest.php` (end-to-end workflows)

---

## Summary Table

| Person | Primary Module | Test IDs | Count | Story Type |
|--------|---|---|---|---|
| 1 | TemuDokter (all) | T1A1-T1D4 | 42 | Create, Read, Cancel, Edge Cases |
| 2 | RekamMedis Admin + Perawat Create | T2A1-T2A5 + T2C1-T2C2 | 27 | Create transactions, Validation, Authorization |
| 3 | RekamMedis Perawat Full + Auth patterns | T2C3-T2F4 | 30 | Update, Delete, Cross-role auth |
| 4 | DetailRekamMedis Dokter (manage) | T2D1-T2E5 | 22 | Add/Edit/Delete details, Dokter auth |
| 5 | Pemilik + Integration + Data Integrity | T2B4 + T2G1-T2G5 | 21 | Read-only auth, E2E workflows, Factories |
| **TOTAL** | | | **~85 tests** |  |

---

## Success Criteria for Test Suite

1. **Coverage**: All 85+ scenarios implemented
2. **Pass Rate**: 100% on happy path (positive cases)
3. **Error Handling**: Validation & authorization errors properly tested
4. **Edge Cases**: Status transitions, soft deletes, authorization boundaries
5. **Database Integrity**: Soft delete queries work, no orphans
6. **Roles**: All 5 roles tested for their specific permissions
7. **Workflow**: End-to-end TemuDokter → RekamMedis cycles work
8. **Issues Found**: Document any inconsistencies (e.g., Perawat status issue)

---

## Next Steps

1. **Person 1** starts first (TemuDokter is prerequisite for RekamMedis testing)
2. **Person 2 & 3** can start in parallel once Person 1 shares completed tests
3. **Person 4** starts once Person 2 confirms RekamMedis creation works
4. **Person 5** integrates all and documents findings
5. **Team Sync**: Weekly review of blockers, failures, inconsistencies found

