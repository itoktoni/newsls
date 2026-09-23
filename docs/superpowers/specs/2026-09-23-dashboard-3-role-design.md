# Dashboard Redesign — 3 Peran (RS / Admin / Laundry)

**Date:** 2026-09-23  
**Status:** Approved (user)  
**Project:** BKA Web — Laravel 13, sistem linen rumah sakit

---

## 1. Problem

`/dashboard` saat ini starter admin (users + notifikasi + chart registrasi) — **tanpa data linen**. Operasional (kotor, register, pending, bersih, outstanding, sebaran per ruangan) tersembunyi di menu Bersih/Warehouse/Report. Butuh dashboard terpisah per peran bisnis.

## 2. Goals

1. Tiga dashboard terpisah: **rumah sakit**, **admin**, **petugas laundry**.
2. Route terpisah + menu Dashboard mengikuti role; akses route yang tidak cocok → **redirect ke default** (bukan 403).
3. Role tak dikenal / legacy (`user`, `editor`, data asing) → **default = dashboard laundry**.
4. Konten linen-first: KPI status, sebaran bersih per ruangan, antrean/warehouse, chart — bukan user/notif sebagai hero.

## 3. Non-goals (fase ini)

- Redesign massal sidebar per role (kecuali label Dashboard).
- Auto-refresh realtime / WebSocket.
- Parstock multi-RS yang sempurna bila pivot null (fallback: stok saja, tanpa %).
- Ubah kontrak API desktop.

## 4. Roles & routing

### 4.1 RoleEnum

Tambah konstanta (bensampo + `EnumTrait` → otomatis di form user `RoleEnum::getOptions()`):

| Const | Value | Dashboard |
|---|---|---|
| `ADMIN` | `admin` | Dashboard admin |
| `DEVELOPER` | `developer` | Dashboard admin |
| `RS` | `rs` | Dashboard rumah sakit |
| `LAUNDRY` | `laundry` | Dashboard petugas laundry |
| `USER` (legacy) | `user` | **Default → laundry** |
| `EDITOR` (legacy) | `editor` | **Default → laundry** |

Migrasi data role **tidak wajib** di fase ini — legacy tetap jatuh ke default.

### 4.2 Routes (`routes/web.php`, middleware `auth` + `verified` + `access`)

| Route | Name | Behavior |
|---|---|---|
| `GET /dashboard` | `dashboard` | **Router:** map role → route target; tak dikenal → `dashboard.laundry` |
| `GET /dashboard/admin` | `dashboard.admin` | Role selain `admin`/`developer` → redirect `dashboard` (default) + flash |
| `GET /dashboard/rs` | `dashboard.rs` | Role selain `rs` → redirect default + flash |
| `GET /dashboard/laundry` | `dashboard.laundry` | Role selain `laundry` + bukan fallback legacy → redirect default + flash |

**Default home** (Fortify / post-login): tetap `GET /dashboard` (router), bukan hardcode satu view.

**Aturan redirect “tidak sesuai role → buka default”:**

```
defaultFor(user):
  admin | developer → dashboard.admin
  rs                → dashboard.rs
  laundry           → dashboard.laundry
  lainnya           → dashboard.laundry   // user, editor, unknown
```

- Buka `/dashboard/rs` sebagai `laundry` → `redirect()->route('dashboard')` (router ulang ke default) + flash info.
- `developer` diperlakukan sama dengan `admin` untuk dashboard.

### 4.3 Implementasi controller

Pendekatan **A (disetujui):** route + invokable terpisah.

```
app/Http/Controllers/
  DashboardRouterController.php   // GET /dashboard
  Dashboard/AdminDashboardController.php
  Dashboard/RsDashboardController.php
  Dashboard/LaundryDashboardController.php
```

Helper trait/shared (opsional): `Concerns/ResolvesDashboardRole.php` → `defaultDashboardRoute(User): string`.

`DashboardController` lama diganti/dialihkan (jangan duplikasi logika users/notif — pindah ke Admin view).

## 5. Content per dashboard

### 5.1 Dashboard Rumah Sakit (`dashboard.rs`)

Scope data: RS yang diakses user (`rs.access` / pivot `rs_dan_user` — konsisten dengan middleware report bila memakai group; atau filter `detail_id_rs` in user’s RS list).

| Widget | Sumber data | Catatan |
|---|---|---|
| **Sebaran linen bersih per ruangan** | `DetailLinen` where `detail_status_linen='BERSIH'` group `detail_id_ruangan` | Gauge/fill: stok vs par bila par tersedia; else tampilkan stok. Urut under-stock dulu. |
| **Pending unit** | `pending` (guard `Schema::hasTable`) where `pending_bersih_at IS NULL` + join ruangan — **atau** outstanding status hilang PENDING untuk RS scope | Pilih query paling murah yang selesai di implement; fallback outstanding. |
| **Alert ruangan kurang** | Turunan sebaran: fill &lt; threshold | Threshold default **60%** (const di config/controller); tanpa par → alert hanya bila stok = 0 (opsional). |
| Ringkas outstanding laundry (opsional ringan) | `Outstanding` count where status proses laundry + RS scope | Bila join RS mahal, skip dulu. |

Drill: klik widget → table filter yang sudah ada (detail-linen / report pending).

### 5.2 Dashboard Petugas Laundry (`dashboard.laundry`) — DEFAULT

| Widget | Sumber |
|---|---|
| **Pipeline KPI** | Register: `detail_status_linen=REGISTER` · Kotor/in laundry: count `outstanding` · Pending: outstanding/pending pending · Bersih: `detail_status_linen=BERSIH` / antrean · Delivered: `bersih` hari ini atau `detail_report` |
| **Antrean packing / siap delivery** | Pola `BersihController`: outstanding `status_proses IN (SCAN,QC,REGISTER,GUDANG)` vs `PACKING` |
| **Warehouse** | Pola `WarehouseController`: `status_proses=GUDANG` total + per jenis top N |
| **Chart kotor vs bersih 7 hari** | Larapex area/bar — adopsi semangat `BuildsDateSeries` / `DashboardChart` (transaksi KOTOR vs bersih per hari) |

### 5.3 Dashboard Admin (`dashboard.admin`)

| Widget | Sumber |
|---|---|
| **KPI global** | Register, Kotor, Pending, Bersih, Outstanding, Gudang — agregat sama seperti laundry + register/gudang |
| **Sebaran global** | Sebaran bersih per ruangan; filter RS (dropdown opsional, default semua) |
| **Data health** | Count: `rs`, `jenis_linen`, `config_linen`, outstanding hilang, pending |
| **Opname** | Opname status proses/selesai; hilang warehouse count |
| **System overview kecil** | Users + unread notif (data lama, accordion/blok bawah) |

## 6. UI / components

- Layout: `x-layouts::app`, breadcrumb, `div.content.space-y-4` — pola `pages/opname/detail.blade.php`.
- KPI: **`x-stat-widget`** / **`x-stat**` (sudah ada, belum dipakai) atau kartu `x-card :noGrid` big-number (pola bersih/warehouse).
- Panel: `x-card` (+ `label`/`icon`).
- Chart: `x-chart-widget` + `@push('scripts')` script larapex.
- Gauge sebaran: CSS `conic-gradient` / ring sederhana + badge hijau/amber/merah (`>=100%` / `>=60%` / else) — tanpa library chart baru.
- Mobile: grid `grid-cols-2 lg:grid-cols-4` (stat-widget) + stack; alert strip full-width.
- Bahasa UI: **Indonesia** untuk label linen (Register, Kotor, Pending, Bersih, Outstanding, Sebaran, dst.).
- Freshness dual-clock: **opsional** — tidak wajib v1; bila murah, 1 baris “diperbarui jam …” di footer kartu.

## 7. Menu

- `config/menu.php` tetap `route => 'dashboard'` (router).
- Bottom nav Home → `dashboard`.
- Label cukup “Dashboard”. Filter menu per role di luar scope kecuali follow-up.

## 8. Config / constants

- `config/dashboard.php` (atau const di controller):  
  - `understock_threshold` = `0.6`  
  - `top_ruangan` = `12`  
  - `chart_days` = `7`

## 9. Error handling & edge cases

| Case | Behavior |
|---|---|
| Belum login | middleware auth → login |
| Role null/kosong | default laundry |
| Tabel `pending` tidak ada | widget pending → 0 / “n/a”, jangan 500 |
| Parstock null / 0 | gauge tanpa % — tampilkan stok + “par —” |
| User multi-RS / tanpa RS | RS dashboard: semua RS yang boleh; kosong → empty state |
| Query berat | satu aggregate per widget; hindari N+1 eager load berlebih |

## 10. Testing

- Feature `tests/Feature/DashboardRoleTest.php` (atau serupa):  
  - `admin`/`developer` → `GET /dashboard` land di admin content.  
  - `rs` → content RS.  
  - `laundry` → content laundry.  
  - `user` / `editor` / role `gak-ada` → **laundry**.  
  - Role salah buka `/dashboard/admin` → redirect ke default (assert redirect + tujuan).  
  - String KPI muncul (mis. “Sebaran”, “Pipeline”, “Warehouse”) per view.  
- Jangan `RefreshDatabase` global di test persist lama (test baru boleh isolasi sendiri / pola project).  
- `vendor/bin/pint` file yang disentuh + `php artisan test` full.

## 11. Implementation order (untuk plan)

1. `RoleEnum` + helper `defaultDashboardRoute`  
2. Routes + router + 3 controller (query dulu, view polos KPI)  
3. View RS (sebaran + alert + pending)  
4. View Laundry (pipeline + bersih + warehouse + chart)  
5. View Admin (KPI global + sebaran + health + opname + system kecil)  
6. Ganti data lama dashboard → pindah ke admin  
7. Menu/breadcrumb bila perlu  
8. Tests + pint + full suite  

## 12. Open questions (ditutup saat approve)

- ~~Mapping role~~ → opsi 1: tambah `rs` + `laundry`.  
- ~~Default mismatch~~ → laundry.  
- ~~Isi tiap dashboard~~ → RS: 1+3+6; Laundry: 1+2+3+4; Admin: 1+2+4+5 + system kecil.  
- Pendekatan → **A** route terpisah.  

## 13. Success criteria

- Login tiap role → dashboard relevan tanpa klik.  
- Buka URL dashboard role lain → jatuh ke default, tidak 403.  
- Manager RS lihat sebaran + alert tanpa buka report.  
- Petugas laundry lihat pipeline + packing + warehouse + chart dalam satu layar.  
- Admin lihat KPI global + health + opname.  
- Seluruh suite test hijau.
