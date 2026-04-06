# Comprehensive System Review: TemuDokter & RekamMedis Features
## Laravel Veterinary Clinic Management System

---

## 1. Models Overview

### 1.1 TemuDokter (Doctor Appointments)

**Table:** `temu_dokter`

**Primary Key:** `idreservasi_dokter` (auto-increment)

**Fillable Fields:**
- `no_urut` (integer, nullable) - Sequential appointment number per doctor per day
- `status` (string, max 50, default: 'menunggu') - Appointment status
- `idpet` (bigInteger) - Foreign key to Pet
- `idrole_user` (bigInteger) - Foreign key to RoleUser (Doctor)
- `deleted_at` (timestamp, nullable) - Soft delete timestamp
- `deleted_by` (bigInteger, nullable) - User ID who deleted the appointment
- `waktu_daftar` (timestamp) - Registration/appointment creation time

**Key Characteristics:**
- Custom CREATED_AT field: `waktu_daftar`
- UPDATED_AT: null (not tracked)
- Uses soft deletes

**Status Values Detected:**
- `'W'` (Waiting/menunggu) - Initial state, awaiting doctor examination
- `'D'` (Done/selesai) - Appointment completed, medical record created

**Relationships:**
```
TemuDokter
├── hasOne: RekamMedis (1:1)
│   FK: idreservasi_dokter → RekamMedis.idreservasi_dokter
├── belongsTo: Pet (M:1)
│   FK: idpet → Pet.idpet
└── belongsTo: RoleUser (M:1)
    FK: idrole_user → RoleUser.idrole_user
```

---

### 1.2 RekamMedis (Medical Records)

**Table:** `rekam_medis`

**Primary Key:** `idrekam_medis` (auto-increment)

**Fillable Fields:**
- `anamnesa` (longText) - Patient history/anamnesis
- `temuan_klinis` (longText) - Clinical findings
- `diagnosa` (longText) - Medical diagnosis
- `dokter_pemeriksa` (bigInteger) - FK to RoleUser (examining doctor)
- `idreservasi_dokter` (bigInteger) - FK to TemuDokter
- `deleted_at` (timestamp, nullable) - Soft delete timestamp
- `deleted_by` (bigInteger, nullable) - User ID who deleted the record
- `created_at` (timestamp) - Record creation time

**Key Characteristics:**
- Custom UPDATED_AT: null (created once, history tracked via DetailRekamMedis)
- Uses soft deletes
- Each TemuDokter has max 1 RekamMedis

**Validation Rules (Admin & Perawat):**
- `idreservasi_dokter`: required, exists in temu_dokter
- `anamnesa`: required, string, max 1000 chars
- `temuan_klinis`: required, string, max 1000 chars
- `diagnosa`: required, string, max 1000 chars
- `detail` (DetailRekamMedis): required, string, max 1000 chars
- `idkode_tindakan_terapi`: required, exists in kode_tindakan_terapi

**Relationships:**
```
RekamMedis
├── hasMany: DetailRekamMedis (1:M)
│   FK: idrekam_medis → DetailRekamMedis.idrekam_medis
├── belongsTo: TemuDokter (M:1)
│   FK: idreservasi_dokter → TemuDokter.idreservasi_dokter
└── belongsTo: RoleUser (dokter) (M:1)
    FK: dokter_pemeriksa → RoleUser.idrole_user
```

---

### 1.3 DetailRekamMedis (Medical Record Details/Actions)

**Table:** `detail_rekam_medis`

**Primary Key:** `iddetail_rekam_medis` (auto-increment)

**Fillable Fields:**
- `idrekam_medis` (bigInteger) - FK to RekamMedis
- `idkode_tindakan_terapi` (bigInteger) - FK to KodeTindakanTerapi (treatment actions)
- `detail` (longText, nullable) - Detailed description of the action/therapy
- `deleted_at` (timestamp, nullable) - Soft delete timestamp
- `deleted_by` (bigInteger, nullable) - User ID who deleted the detail

**Key Characteristics:**
- No timestamps (not tracked)
- Uses soft deletes
- One RekamMedis can have multiple DetailRekamMedis (procedures/treatments)

**Validation Rules (Dokter only):**
- `idkode_tindakan_terapi`: required, exists in kode_tindakan_terapi
- `detail`: required, string, max 1000 chars

**Relationships:**
```
DetailRekamMedis
├── belongsTo: RekamMedis (M:1)
│   FK: idrekam_medis → RekamMedis.idrekam_medis
└── belongsTo: KodeTindakanTerapi (M:1)
    FK: idkode_tindakan_terapi → KodeTindakanTerapi.idkode_tindakan_terapi
```

---

### 1.4 Supporting Models

**Pet Model:**
- Relationships: `belongsTo(Pemilik)`, `belongsTo(RasHewan)`, `hasMany(RekamMedis)`

**RoleUser Model:**
- Relationships: `belongsTo(User)`, `belongsTo(Role)`
- Tracks role assignments and status

**KodeTindakanTerapi Model:**
- Fields: `kode`, `deskripsi_tindakan_terapi`, `idkategori`, `idkategori_klinis`
- Relationships: `belongsTo(Kategori)`, `belongsTo(KategoriKlinis)`, `hasMany(DetailRekamMedis)`

---

## 2. Controllers Mapping

### 2.1 Admin Controllers

#### **Admin\TemuDokter_Controller**

| Method | Purpose | Business Logic |
|--------|---------|-----------------|
| `daftar_temu_dokter(Request)` | List all appointments | Filters by date (today/all), doctor; eager loads relationships; returns view |
| `store_temu_dokter(Request)` | Create appointment | Validates pet & doctor; generates `no_urut`; sets status='W'; redirects to list |
| `cancel_temu_dokter($id)` | Soft delete appointment | Sets deleted_at=now(), deleted_by=session(iduser); redirects to list |

**Validation:**
```
- idpet: required, exists:pet.idpet
- idrole_user: required, exists:role_user.idrole_user
```

**`generate_nomor_urut()` Logic:**
- Gets max `no_urut` for doctor across ALL appointments (not per-day)
- Returns max + 1 (or 1 if no previous appointments)
- ⚠️ **Issue:** Differs from Resepsionis version (see below)

---

#### **Admin\RekamMedis_Controller**

| Method | Purpose | Business Logic |
|--------|---------|-----------------|
| `daftar_rekam_medis(Request)` | List all medical records | Eager loads temuDokter, pet, pemilik, roleUser, detailRekamMedis; filters for status=W reservations |
| `store_rekam_medis(Request)` | Create medical record | Validates all fields; creates RekamMedis + DetailRekamMedis; updates TemuDokter status to 'D' |
| `update_rekam_medis($id, Request)` | Edit medical record | Validates; updates anamnesa, temuan_klinis, diagnosa; updates first DetailRekamMedis if exists |
| `delete_rekam_medis($id)` | Soft delete record | Soft deletes detail records first; reverts TemuDokter status to 'W'; soft deletes RekamMedis |

**Validation (Create):**
```
- idreservasi_dokter: required, exists:temu_dokter
- anamnesa: required, string, max:1000
- temuan_klinis: required, string, max:1000
- diagnosa: required, string, max:1000
- detail: required, string, max:1000
- idkode_tindakan_terapi: required, exists:kode_tindakan_terapi
```

**Validation (Update):**
```
Same as create, except idreservasi_dokter not required
```

**Key Logic:**
- Admin can create RekamMedis with detail in one transaction
- Updating RekamMedis updates FIRST detail record only (not all)
- Deleting RekamMedis reverts appointment back to 'W' status

---

### 2.2 Resepsionis Controllers

#### **Resepsionis\TemuDokterResepsionis_Controller**

| Method | Purpose | Business Logic |
|--------|---------|-----------------|
| `daftar_temu_dokter(Request)` | List appointments | Uses raw query builder; filters by date (today/all), doctor; different selection |
| `store_temu_dokter(Request)` | Create appointment | Validates pet & doctor; generates `no_urut` per-day per-doctor; sets status='W'; manual insert |
| `cancel_temu_dokter($id)` | Soft delete appointment | Sets deleted_at=now(), deleted_by=session(iduser); manual update |

**Validation:**
Same as Admin (identical)

**`generate_nomor_urut()` Logic:**
- Gets max `no_urut` for doctor **on today's date only**
- Returns max + 1 (or 1 if no appointments today)
- ⚠️ **Difference:** Daily reset logic (unlike Admin version)

**Key Differences from Admin:**
- Uses raw query builder instead of Eloquent models
- Handles Pemilik + Pet management in addition to appointments
- Daily numbering reset for appointment queue

---

### 2.3 Dokter Controllers

#### **Dokter\RekamMedisDokter_Controller**

| Method | Purpose | Business Logic |
|--------|---------|-----------------|
| `daftar_rekam_medis()` | List my records | Queries all RekamMedis; loops to add dokter_nama, detail_list |
| `detail_rekam_medis($id)` | View record details | Gets RekamMedis with full pet/pemilik/breed info; loads doctor name & details; checks ownership |
| `store_detail($idrekam_medis, Request)` | Add treatment detail | Validates detail; checks dokter authorization; inserts into detail_rekam_medis |
| `update_detail($iddetail_rekam_medis, Request)` | Edit treatment | Validates; checks both detail & RekamMedis existence; verifies doctor authorization; updates |
| `delete_detail($iddetail_rekam_medis)` | Remove treatment | Checks detail & RekamMedis exist; verifies authorization; soft deletes detail |

**Validation (Detail):**
```
- idkode_tindakan_terapi: required, exists:kode_tindakan_terapi
- detail: required, string, max:1000
```

**Key Authorization:**
- Doctor can only add/edit/delete details for their own RekamMedis
- Checks: `current_idrole_user == rekam_medis->dokter_pemeriksa`
- Access denied returns error with redirect

**Key Logic:**
- Doctor views RekamMedis created by anyone
- Doctor can add multiple DetailRekamMedis (multiple procedures/treatments)
- Each detail is independent (can be edited/deleted separately)

---

### 2.4 Perawat Controllers

#### **Perawat\RekamMedisPerawat_Controller**

| Method | Purpose | Business Logic |
|--------|---------|-----------------|
| `daftar_rekam_medis()` | List all records | Queries RekamMedis + joined details; adds doctor names; shows reservations without records |
| `detail_rekam_medis($id)` | View record | Gets record with pet/pemilik/breed; read-only view |
| `store_rekam_medis(Request)` | Create record | Validates; creates RekamMedis (anamnesa, temuan_klinis, diagnosa); **no detail creation** |
| `update_rekam_medis($id, Request)` | Edit record | Updates only anamnesa, temuan_klinis, diagnosa |
| `delete_rekam_medis($id)` | Soft delete | Soft deletes RekamMedis record |

**Validation (Create):**
```
- idreservasi_dokter: required, exists:temu_dokter
- anamnesa: required, string, max:1000
- temuan_klinis: required, string, max:1000
- diagnosa: required, string, max:1000
```
(Note: No detail/idkode_tindakan_terapi validation)

**Validation (Update):**
```
Same as create, without idreservasi_dokter
```

**Key Logic:**
- Perawat creates RekamMedis WITHOUT detail records
- Perawat cannot add treatments/details (doctor role only)
- Perawat can update clinical findings but not delete TemuDokter
- Shows available reservations (status='W' without RekamMedis yet)
- **Note:** Doesn't update TemuDokter status when creating RekamMedis

---

### 2.5 Pemilik Controllers

#### **Pemilik\RekamMedisPemilik_Controller**

| Method | Purpose | Business Logic |
|--------|---------|-----------------|
| `daftar_rekam_medis()` | List my pet's records | Filters by current user; read-only; loads doctor names & details |
| `detail_rekam_medis($id)` | View my record | Authorization check: verify record belongs to user's pet; read-only |

**Key Characteristics:**
- **No write operations** (no create, update, delete)
- Owner can only view their own pet's records
- Filters by `pemilik.iduser = auth()->id()`
- Read-only access to full medical history

---

## 3. Routes Summary

### 3.1 TemuDokter Routes

| HTTP | Route | Name | Role(s) | Purpose |
|------|-------|------|---------|---------|
| GET | `/Admin/TemuDokter/daftar-temu-dokter` | `Admin.TemuDokter.daftar-temu-dokter` | Admin | List all appointments |
| POST | `/Admin/TemuDokter/store-temu-dokter` | `Admin.TemuDokter.store-temu-dokter` | Admin | Create appointment |
| PUT | `/Admin/TemuDokter/cancel-temu-dokter/{id}` | `Admin.TemuDokter.cancel-temu-dokter` | Admin | Cancel/soft delete |
| GET | `/Resepsionis/TemuDokter/daftar-temu-dokter` | `Resepsionis.TemuDokter.daftar-temu-dokter` | Resepsionis | List appointments |
| POST | `/Resepsionis/TemuDokter/store-temu-dokter` | `Resepsionis.TemuDokter.store-temu-dokter` | Resepsionis | Create appointment |
| PUT | `/Resepsionis/TemuDokter/cancel-temu-dokter/{id}` | `Resepsionis.TemuDokter.cancel-temu-dokter` | Resepsionis | Cancel/soft delete |

**Route Notes:**
- Admin & Resepsionis have same functionality (different controllers, slight implementation variations)
- No routes for Dokter, Perawat, or Pemilik to manage appointments directly
- Pemilik views reservations via `Pemilik.TemuDokter.daftar-reservasi-saya` in different controller

---

### 3.2 RekamMedis Routes

| HTTP | Route | Name | Role(s) | Purpose |
|------|-------|------|---------|---------|
| GET | `/Admin/RekamMedis/daftar-rekam-medis` | `Admin.RekamMedis.daftar-rekam-medis` | Admin | List all records |
| POST | `/Admin/RekamMedis/store-rekam-medis` | `Admin.RekamMedis.store-rekam-medis` | Admin | Create + detail |
| PUT | `/Admin/RekamMedis/update-rekam-medis/{id}` | `Admin.RekamMedis.update-rekam-medis` | Admin | Edit record |
| DELETE | `/Admin/RekamMedis/delete-rekam-medis/{id}` | `Admin.RekamMedis.delete-rekam-medis` | Admin | Soft delete |
| GET | `/Dokter/RekamMedis/daftar-rekam-medis` | `Dokter.RekamMedis.daftar-rekam-medis` | Dokter | List records |
| GET | `/Dokter/RekamMedis/detail-rekam-medis/{id}` | `Dokter.RekamMedis.detail-rekam-medis` | Dokter | View details |
| POST | `/Dokter/RekamMedis/store-detail/{idrekam_medis}` | `Dokter.RekamMedis.store-detail` | Dokter | Add treatment |
| PUT | `/Dokter/RekamMedis/update-detail/{id}` | `Dokter.RekamMedis.update-detail` | Dokter | Edit treatment |
| DELETE | `/Dokter/RekamMedis/delete-detail/{id}` | `Dokter.RekamMedis.delete-detail` | Dokter | Remove treatment |
| GET | `/Perawat/RekamMedis/daftar-rekam-medis` | `Perawat.RekamMedis.daftar-rekam-medis` | Perawat | List records |
| GET | `/Perawat/RekamMedis/detail-rekam-medis/{id}` | `Perawat.RekamMedis.detail-rekam-medis` | Perawat | View details |
| POST | `/Perawat/RekamMedis/store-rekam-medis` | `Perawat.RekamMedis.store-rekam-medis` | Perawat | Create record |
| PUT | `/Perawat/RekamMedis/update-rekam-medis/{id}` | `Perawat.RekamMedis.update-rekam-medis` | Perawat | Edit record |
| DELETE | `/Perawat/RekamMedis/delete-rekam-medis/{id}` | `Perawat.RekamMedis.delete-rekam-medis` | Perawat | Soft delete |
| GET | `/Pemilik/RekamMedis/daftar-rekam-medis` | `Pemilik.RekamMedis.daftar-rekam-medis` | Pemilik | View my records |
| GET | `/Pemilik/RekamMedis/detail-rekam-medis/{id}` | `Pemilik.RekamMedis.detail-rekam-medis` | Pemilik | View record details |

**Role Access Matrix:**

```
                   Admin    Dokter    Perawat    Pemilik
Create RekamMedis   ✓         ✗          ✓          ✗
Edit RekamMedis     ✓         ✗          ✓          ✗
Delete RekamMedis   ✓         ✗          ✓          ✗
View All Records    ✓         ✓          ✓          ✗ (own only)
Add Treatment       ✗         ✓          ✗          ✗
Edit Treatment      ✗         ✓          ✗          ✗
Delete Treatment    ✗         ✓          ✗          ✗
```

---

## 4. Factory Capabilities

### 4.1 TemuDokterFactory

**Base Definition:**
```php
return [
    'no_urut' => fake()->unique()->numberBetween(1, 100),
    'status' => fake()->randomElement(['menunggu', 'sedang_diperiksa', 'selesai']),
    'idpet' => Pet::factory(),
    'idrole_user' => RoleUser::factory(),
];
```

**Helper Methods:**
- `forPet(Pet $pet)` - Specify exact pet
- `forRoleUser(RoleUser $roleUser)` - Specify exact doctor

**Generated Data:**
- Unique no_urut: 1-100 per factory call
- Random status: menunggu, sedang_diperiksa, selesai
- Creates new Pet & RoleUser if not specified
- waktu_daftar: auto-generated by model

---

### 4.2 RekamMedisFactory

**Base Definition:**
```php
return [
    'anamnesa' => fake()->sentence(),
    'temuan_klinis' => fake()->sentence(),
    'diagnosa' => fake()->sentence(),
    'dokter_pemeriksa' => RoleUser::factory(),
    'idreservasi_dokter' => temuDokter::factory(),
];
```

**Helper Methods:**
- `forDokter(RoleUser $dokter)` - Specify exact doctor (idrole_user)
- `forTemuDokter(temuDokter $temuDokter)` - Specify exact appointment

**Generated Data:**
- All text fields: single fake sentences
- Creates new RoleUser & TemuDokter if not specified
- created_at: auto-generated by model

---

### 4.3 DetailRekamMedisFactory

**Base Definition:**
```php
return [
    'idrekam_medis' => RekamMedis::factory(),
    'idkode_tindakan_terapi' => KodeTindakanTerapi::factory(),
    'detail' => fake()->sentence(),
];
```

**Helper Methods:**
- `forRekamMedis(RekamMedis $rekamMedis)` - Specify exact record
- `forKodeTindakanTerapi(KodeTindakanTerapi $kodeTindakanTerapi)` - Specify exact therapy code

**Generated Data:**
- Creates new RekamMedis & KodeTindakanTerapi if not specified
- detail: single fake sentence

---

## 5. Key Business Rules

### 5.1 Appointment Workflow

**Status Progression:**
```
Created (status='W')
    ↓
[Doctor Examines Pet]
    ↓
RekamMedis Created (status='D')
```

**Important Rules:**
1. **Status Values:** Only 'W' and 'D' used in actual code (factory values differ)
2. **One Record Per Appointment:** Each TemuDokter can have max ONE RekamMedis (1:1 relationship enforced)
3. **Sequential Number Generation:**
   - **Admin:** `no_urut` increments globally (per doctor, all-time)
   - **Resepsionis:** `no_urut` resets daily per doctor (appointment queue order)
   - ⚠️ **Inconsistency Alert:** Two different numbering strategies in same system
4. **Soft Deletes:** Cancelled appointments keep `deleted_at` & `deleted_by` timestamps

---

### 5.2 Medical Record Lifecycle

**Creation:**
- **Admin:** Creates RekamMedis + DetailRekamMedis + updates TemuDokter status to 'D' (atomic)
- **Perawat:** Creates RekamMedis only (NO detail records) + **DOES NOT** update TemuDokter status
- **Dokter:** Cannot create RekamMedis (viewing only), adds DetailRekamMedis to existing records

**Post-Creation:**
- **Dokter:** Can add multiple DetailRekamMedis (procedures/treatments) independently
- **Dokter:** Can edit/delete own details with authorization check
- **Perawat:** Can view & edit RekamMedis (clinical findings) but cannot touch details
- **Admin:** Can update RekamMedis + FIRST detail only (bulk edit)

**Deletion:**
- **Reverse Flow:** Details → RekamMedis → TemuDokter status reverted to 'W'
- Enables re-creation of RekamMedis for same appointment

---

### 5.3 Authorization Rules

**Dokter-Specific:**
- Can only add/edit/delete DetailRekamMedis for their own RekamMedis
- Authorization check: `current_idrole_user == rekam_medis->dokter_pemeriksa`
- Accessing others' details returns error: "Anda tidak memiliki akses"

**Pemilik-Specific:**
- Can only view RekamMedis for their own pets
- Authorization check: `pet.pemilik.iduser == auth()->id()`
- No write permissions on any medical records

**Admin & Perawat:**
- No specific authorization checks (can access all records)

---

### 5.4 Data Validation Thresholds

| Field | Max Length | Type | Required |
|-------|-----------|------|----------|
| anamnesa | 1000 chars | string | ✓ |
| temuan_klinis | 1000 chars | string | ✓ |
| diagnosa | 1000 chars | string | ✓ |
| detail (RekamMedis) | 1000 chars | string | ✓ |
| detail (Treatment) | 1000 chars | string | ✓ |
| no_urut | unlimited | integer | ✗ |
| status | 50 chars | string | ✓ |

---

### 5.5 Data Integrity Concerns

**Identified Issues:**

1. **Perawat doesn't update TemuDokter status:**
   - When Perawat creates RekamMedis, appointment stays status='W'
   - Admin/Dokter expect status='D' after creation
   - **Risk:** Inconsistent state, data quality issues

2. **Multiple numbering strategies:**
   - Admin & Resepsionis use different `no_urut` logic
   - Can cause confusion in queue management
   - **Risk:** Duplicate numbers, ordering issues

3. **DetailRekamMedis creation:**
   - Admin creates with RekamMedis (atomic)
   - Perawat creates RekamMedis but NO details (requires Dokter to add)
   - **Risk:** Incomplete records if Dokter doesn't follow up

4. **Update limitations:**
   - Admin can only update FIRST detail (if multiple exist)
   - Other details ignored in update
   - **Risk:** Data loss if multiple details exist

5. **Soft delete reversal:**
   - Deleting RekamMedis reverts TemuDokter to 'W'
   - Can re-register same appointment multiple times
   - **Risk:** Duplicate records if not controlled

6. **Foreign Key Constraints:**
   - No explicit FK constraints in migrations
   - Orphaned records possible if Pet/RoleUser deleted
   - **Risk:** Data integrity, referential issues

---

### 5.6 Workflow by Role

**Admin:**
```
TemuDokter (create, list, cancel)
    ↓
RekamMedis (create with detail, list, update first detail only, delete)
    ↓
[System reverts appointment if deleted]
```

**Resepsionis:**
```
TemuDokter (create with daily numbering, list, cancel)
    ↓
[Other roles handle RekamMedis]
```

**Dokter:**
```
RekamMedis (view, list)
    ↓
DetailRekamMedis (create, edit own, delete own, with authorization)
```

**Perawat:**
```
TemuDokter (reserved, not yet recorded)
    ↓
RekamMedis (create without detail, list, update, delete)
    ↓
[Dokter adds details later]
```

**Pemilik:**
```
RekamMedis (view own pets' records only, read-only)
```

---

## 6. Testing Recommendations

### Critical Test Scenarios

**Appointment Flow:**
- [ ] Admin creates appointment, generates correct no_urut
- [ ] Resepsionis creates appointment, no_urut resets daily
- [ ] Cancel appointment soft deletes correctly
- [ ] Cannot create RekamMedis without valid TemuDokter

**RekamMedis Creation Variants:**
- [ ] Admin creates RekamMedis + detail atomically
- [ ] Perawat creates RekamMedis without detail
- [ ] Appointment status updates to 'D' (Admin only)
- [ ] Perawat creation doesn't update appointment status

**DetailRekamMedis Management:**
- [ ] Dokter adds multiple details to same RekamMedis
- [ ] Dokter can only edit own details (org by dokter_pemeriksa)
- [ ] Dokter unauthorized for other doctors' details
- [ ] Admin updates first detail only (multiple details edge case)

**Deletion & Reversal:**
- [ ] Deleting RekamMedis reverts TemuDokter to 'W'
- [ ] Can create new RekamMedis after deletion
- [ ] Soft delete timestamp preserved correctly

**Authorization:**
- [ ] Pemilik sees only own pet records
- [ ] Dokter cannot create RekamMedis
- [ ] Perawat cannot add details
- [ ] Unauthorized access returns errors

**Data Validation:**
- [ ] Text fields reject >1000 chars
- [ ] Required fields enforced
- [ ] Foreign key validation works
- [ ] Unique appointment number prevents duplicates

---

## 7. Database Schema

### Table: temu_dokter

```
idreservasi_dokter (PK, unsigned int, auto-increment)
idpet (bigint)
idrole_user (bigint)
no_urut (int, nullable)
status (varchar 50, default='menunggu')
waktu_daftar (timestamp)
deleted_at (timestamp, nullable)
deleted_by (bigint, nullable)
```

### Table: rekam_medis

```
idrekam_medis (PK, unsigned int, auto-increment)
idreservasi_dokter (bigint)
dokter_pemeriksa (bigint)
anamnesa (longtext)
temuan_klinis (longtext)
diagnosa (longtext)
created_at (timestamp)
deleted_at (timestamp, nullable)
deleted_by (bigint, nullable)
```

### Table: detail_rekam_medis

```
iddetail_rekam_medis (PK, unsigned int, auto-increment)
idrekam_medis (bigint)
idkode_tindakan_terapi (bigint)
detail (longtext, nullable)
deleted_at (timestamp, nullable)
deleted_by (bigint, nullable)
```

---

**Document Generated:** 2026-04-05  
**System:** Laravel Veterinary Clinic Management System
