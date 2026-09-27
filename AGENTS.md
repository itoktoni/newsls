# AGENTS.md — BKA Web (Laravel)

> **Project:** `BKA Web` — Laravel 13 + Livewire 4 + Flux UI — sistem linen rumah sakit
> **Peran:** Satu-satunya **source of truth**. Mengelola **master data**, menampilkan **transaksi**, melayani **API Sanctum** untuk desktop RFID, dan menghasilkan **report**.
> **Stack:** PHP ^8.3 | Laravel 13.x | Livewire 4.x + Flux 2.x | Tailwind + Vite | Sanctum 4.x | bensampo/laravel-enum | lorisleiva/laravel-actions | izniburak/laravel-auto-routes | MySQL | spatie/activitylog

---

## 1. Tanggung Jawab Utama (4 Pilar)

| Pilar | Apa yang dikelola | Route / Controller | Catatan |
|---|---|---|---|
| **Master Data** | `rs`, `group_rs`, `ruangan`, `jenis_linen`, `jenis_bahan`, `supplier`, `kategori`, `config_linen`, `detail_linen` | `routes/web.php` `Route::auto()` + `*Controller.php` | CRUD via `ControllerTrait` — lihat §4 |
| **Bersih (web, viewer)** | Packing + Delivery + Riwayat Cetak — tab `packing` (antrean SCAN/QC), `delivery` (PACKING), `riwayat` (`cetak`) | `BersihController::getTable` saja + `pages/bersih/table.blade.php` | Web read-only; semua PROSES hanya via desktop → API `PackingDeliveryController`; menu `bersih.getTable` |
| **Transaksi Viewer** | `transaksi` (history) + `outstanding` (stok di laundry) + `detail_linen` status `KOTOR` | `TransaksiController` | Read-only viewer; data ditulis oleh API desktop |
| **API (untuk desktop)** | `POST /api/login`, `/register`, `/kotor`, `/retur`, `/rewash`, `GET /api/rs`, `/configuration` | `routes/api.php` + `app/Http/Controllers/Api/` + `AuthController` | Sanctum Bearer, kontrak field = kolom DB |
| **Report** | Laporan per RS / per jenis / outstanding / stok — PDF via `barryvdh/laravel-dompdf`, grafik via `larapex-charts` | `resources/views/pdf/` + `app/Charts/` | Barcode `milon/barcode` untuk label RFID |

> **Jangan menambah sumber data lain.** Semua validasi status (`CuciEnum`, `LinenStatusEnum`, `RegisterEnum`, `TransactionType`, `RsStatusEnum`) hidup di `app/Enums/` — desktop hanya push, validasi final di sini.

---

## 2. Tech Stack Ringkas

```
Laravel 13 (PHP 8.3) | Fortify (web auth) + Sanctum (API) | Livewire 4 + Flux 2 | Tailwind + Vite
lorisleiva/laravel-actions (business logic) | laravel-auto-routes (Route::auto) | laravel-purity (filter/sort)
bensampo/laravel-enum + EnumTrait | kirschbaum/power-joins | spatie/activitylog | dompdf + larapex-charts
MySQL (bka) + SQLite (Orbit flat-file untuk CMS bila dipakai)
```

---

## 3. Struktur Direktori Relevan

```
app/
├── Actions/               # Business logic — RegisterLinenAction, Create/Update/DeleteAction
├── Enums/                 # CuciEnum, LinenStatusEnum, RegisterEnum, TransactionType, RsStatusEnum, RoleEnum
├── Http/Controllers/
│   ├── Api/TransaksiApiController.php   # kotor/retur/rewash -> transaction()
│   ├── AuthController.php               # login -> createToken('api_token')
│   ├── RegisterLinenController.php      # POST /api/register (massal)
│   └── *Controller.php                 # Master data + Transaksi viewer (pakai ControllerTrait)
├── Models/                # Rs, DetailLinen, Transaksi, Outstanding, ConfigLinen, JenisLinen, Ruangan, ...
├── Policies/              # WAJIB per model — tanpa ini 403 (BasePolicy -> config/permision.php)
├── Properties/            # *Entity trait — abstraksi nama kolom (field_rs_nama())
├── Concerns/              # ControllerTrait, DefaultEntity, EnumTrait, OptionTrait, ...
function/Global.php        # helper: fileUrl(), uploadFile(), formatDate(), moduleRoute()
routes/
├── web.php                # CRUD master data (auth+verified+access) — Route::auto()
└── api.php               # KONTRAK untuk desktop — jangan ubah tanpa sync desktop/DAO
resources/views/
├── pages/{module}/table.blade.php + form.blade.php  # template CRUD (copy dari pages/users/)
└── pdf/                   # template laporan
database/migrations/       # rs, detail_linen (PK detail_rfid string), config_linen, rs_dan_*, transaksi
config/permision.php       # mapping role -> allowed actions
config/menu.php            # navigasi sidebar
```

---

## 4. Pola CRUD Standar (Wajib Diikuti)

### 4.1 Urutan pembuatan modul baru

1. **Enum** (jika perlu) `app/Enums/NamaEnum.php` — extend `BenSampo\Enum\Enum`, pakai `EnumTrait`, `getOptions()` untuk dropdown
2. **Model** `app/Models/Nama.php` — extend `BaseModel`, `$table` singular, `$primaryKey = '{module}_id'`, `$fillable`, `casts(): array`, `$filterColumns`/`$sortColumns`, `field_name()`, `rules()`, relation `hasXxx()` (prefix `has` wajib)
3. **Migration** `database/migrations/*_create_{table}.php` — kolom prefix `{module}_{field}` (mis. `rs_nama`, `detail_rfid`)
4. **Policy** `app/Policies/NamaPolicy.php extends BasePolicy` — **tanpa ini semua request 403**
5. **Controller** `app/Http/Controllers/NamaController.php` — `use ControllerTrait`, `__construct(Model $m){ $this->model = $m::getModel(); }`, override `share()`/`getData()` bila perlu
6. **Route** `routes/web.php` → `Route::auto('/nama', 'NamaController', ['name'=>'nama'])`
7. **Views** `resources/views/pages/nama/table.blade.php` + `form.blade.php` — copy dari `pages/users/` jangan karang struktur baru
8. **Menu + Permission** `config/menu.php` + `config/permision.php`

### 4.2 ControllerTrait — method yang tersedia

`index` → redirect ke `getTable`, `getTable` (paginated table), `getCreate`/`postCreate`, `getUpdate`/`postUpdate`, `getDelete`/`postDelete` (bulk), `getShow`. Custom action = `getXxx`/`postXxx` + route manual.

> File upload: alias trait `postCreate as traitPostCreate`, handle `uploadFile()` sebelum panggil trait, merge path string ke request.

### 4.3 View template — `table` + `form` (copy users)

- `table`: `<x-filter>`, `<x-table>` dengan `<x-table-sort>` loop `$model::$sortColumns`, `<x-table-action>` per row, `<x-slot:mobile>` wajib, `<x-pagination>`, `<x-action :action="['create','delete']">`, hidden `.module` + `/js/table.js` + `initTable()`
- `form`: satu file untuk create & update, `<x-form :model>`, `<x-card>` + `@bind($model)`, `col="6"` grid, `<x-select :options="Enum::getOptions()">`, `enctype="multipart/form-data"` bila ada file

---

## 5. API Contract untuk Desktop (Jangan Ubah Sembarang)

### 5.1 Auth

```
POST /api/login  { email, password } -> { status, data: { api_token, id, name, email, role } }
POST /api/logout (Bearer) -> 200
GET  /api/me     (Bearer)
PUT  /api/me     (Bearer) { name?, email?, phone? }
```

`AuthController@login` = `User::where(email)->first()` + `Hash::check` + `$user->createToken('api_token')->plainTextToken`. Token Sanctum disimpan desktop di `Properties.Settings.Default["Token"]`.

### 5.2 Register Linen (massal)

```
POST /api/register (Bearer)
Body: rfid[] (required, array string), jenis_id, bahan_id, supplier_id, status_cuci,
      rs_id (int atau array untuk GROUP), ruangan_id, status_register, status_kepemilikan, deskripsi, tgl_cek
-> 201 { total, rfid[], data[] }

GET /api/register/config?rs_id=75 (Bearer) -> { rs[], jenis[], ruangan[], bahan[], supplier[], status_*[] }
```

Logic di `RegisterLinenAction`, controller hanya `Notes::create()` envelope `{status,code,name,message,data}`.

### 5.3 Transaksi RFID — Kotor / Retur / Rewash

```
POST /api/{kotor|retur|rewash} (Bearer)
Body: rfid[] (required array), rs_id (required int exists:rs), key (required string, transaksi_key)

-> 201 { message, data: { key, status, rfid_count, inserted } }
```

`TransaksiApiController@transaction` normalisasi `type` upper + map `REJECT` alias, validasi, lalu `handle()`:
- deduplicate `rfid` unique,
- load `DetailLinen` + `Outstanding` existing,
- insert `transaksi` (chunk 500, skip jika sudah ada today),
- insert/update `outstanding` (stok laundry) — bila belum ada insert `NORMAL`/`SCAN`, bila sudah ada update ke status terbaru,
- bila `KOTOR`, update `detail_linen.detail_status_linen='KOTOR'`,
- audit `activity('transaksi')`.

### 5.4 Packing & Delivery (adopsi andalan BersihController)

```
POST /api/packing (Bearer)
Body: rfid[] (required array), rs_id, ruangan_id, status_transaksi
-> Notes::data([{id,code,tgl,rs,nama,lokasi,status,user,total}]) grouped by jenis+ruangan
   + cetak row type=1 (Barcode) + rfids JSON untuk reprint

POST /api/delivery (Bearer)
Body: rs_id, status_transaksi (KOTOR/BERSIH->KOTOR)
-> Notes::data([{id,code,tgl,rs,nama,lokasi,status,user,total}])
   + DetailLinen BERSIH + report_date, Outstanding dihapus, cetak row type=2 (Delivery)

GET /api/packing/{code} | /api/delivery/{code} -> Notes::data([DetailLinen...]) untuk reprint
GET /api/list/packing/{rsid} | /api/list/delivery/{rsid}[?tgl=] -> Notes::data([{cetak_code}])
GET /api/total/delivery/{rsid}/{status} -> Notes::data({total,view_total}) dari Outstanding PACKING
GET /api/total/outstanding/{rsid}/{ruangan}/{jenis}/{transaksi} -> Notes::data({view_total,...})
GET /api/total/bersih/{rsid}/{ruangan}/{jenis}/{transaksi} -> Notes::data({view_total}) dari DetailLinen
```

Controller: `app/Http/Controllers/Api/PackingDeliveryController.php`. Tabel `cetak` legacy
(`cetak_code,cetak_id_rs,cetak_type 1/2,cetak_barcode/cetak_delivery,cetak_rfids` JSON BKA).
Catatan: `transaksi_status` enum DB tidak ada `BERSIH` — delivery TIDAK insert Transaksi
(meniru andalan yang catat ke Bersih/Cetak, bukan Transaksi).

### 5.5 Master data untuk dropdown / sync

```
GET /api/download/{rsid} (Bearer)   -> STREAMED JSON (lihat catatan di bawah)
GET /api/configuration   (Bearer) -> { supplier[], jenis_bahan[], jenis_linen[], status_*[], kepemilikan[] }
GET /api/rs?type=free|dedicated (Bearer) -> { data, ruangan[], jenis_linen[], jenis_rs[], ruangan_rs[] }
GET /api/rs_lite         (Bearer) -> { data: [rs_id, rs_nama] }
GET /api/rs/{rsid}       (Bearer) -> { data: Rs with hasRuangan,hasJenis }
```

**`GET /api/download/{rsid}`** — sync master RFID 1 RS ke desktop (bisa 12rb+ baris).
Controller `app/Http/Controllers/Api/DownloadApiController.php` (invokable, `->whereNumber('rsid')`).

```
{ status, code, name:"List", message, total, data[], rs{rs_id,rs_nama}, ruangan[{ruangan_id,ruangan_nama}], opname[rfid] }
data[] = { rfid, rs_id, rs_nama, ruangan_id, ruangan_nama, jenis_id, jenis_nama, status_transaksi, status_proses, tanggal }
```

Aturan (tiruan `andalan/app/Http/Resources/DownloadCollection`):
- `status_transaksi`/`status_proses` = baris `outstanding` bila ada, selain itu `BERSIH`; dipaksa
  `BERSIH` bila RFID belum pernah punya baris `transaksi`. `tanggal` = `detail_updated_at`
  (format `Y-m-d H:i:s`) atau waktu generate bila ada `outstanding`.
- `ruangan` = pivot `rs_dan_ruangan` milik RS; `opname` = RFID dengan `opname_detail_ketemu=1`
  pada opname aktif (`opname_status=1`). RS kosong -> envelope `code 404` `Data Tidak Ditemukan !`.
- `total` = jumlah baris yang diharapkan (key TAMBAHAN, bukan legacy) — desktop wajib cek
  `data.length === total` supaya download terpotong ketahuan, bukan gagal deserialize.

Tuning anti-putus: `response()->streamJson()` (generator, chunk 2.000 baris via `DB::table` tanpa
hidrasi model + `flush()` per chunk + `X-Accel-Buffering: no`), `set_time_limit(0)`, query log off,
`JSON_INVALID_UTF8_SUBSTITUTE`. Jangan kembalikan ke `DetailLinen::with(...)->get()` — 12rb model =
memory_limit habis = JSON terpotong. Bila kontrak field berubah, sync `desktop/DAO/*`.

### 5.6 Konvensi response

- Web CRUD: `PayloadTrait` -> flash `TOAST_SUCCESS` + redirect (web) atau JSON (API).
- API register/transaksi: `Plugins\Notes` envelope + HTTP 201/422/500. Jangan ubah key tanpa update `desktop/DAO/*`.

---

## 6. Model & Domain Penting

| Model | Table | PK | Catatan domain |
|---|---|---|---|
| `DetailLinen` | `detail_linen` | `detail_rfid` (string, non-increment) | Inti sistem — PK = EPC RFID; `detail_id_rs`, `detail_id_ruangan`, `detail_id_jenis`, `detail_id_bahan`, `detail_id_supplier`; `detail_status_*` (cuci/register/kepemilikan/linen); `booted()` default `LinenStatusEnum::REGISTER` + `RegisterEnum::REGISTER` |
| `Transaksi` | `transaksi` | `transaksi_id` | History — `transaksi_key` (batch key), `transaksi_rfid`, `transaksi_rs_ori/scan`, `transaksi_beda_rs`, `transaksi_status` (`KOTOR`/`REJECT`/`REWASH`) |
| `Outstanding` | `outstanding` | `outstanding_rfid` | Stok di laundry — mirror transaksi terakhir; `outstanding_status_process='SCAN'`, `outstanding_status_hilang='NORMAL'` |
| `Rs` | `rs` | `rs_id` | Rumah sakit — `rs_status` (`FREE`/`DEDICATED`/`GROUP`), `rs_id_group`, pivot `rs_dan_ruangan`, `rs_dan_jenis` (parstock) |
| `Ruangan` | `ruangan` | `ruangan_id` | — |
| `JenisLinen` / `JenisBahan` | `jenis_linen`/`jenis_bahan` | `jenis_id`/`bahan_id` | — |
| `ConfigLinen` | `config_linen` | — | Pivot RFID → multi-RS pemilik (`GROUP_CONCAT` di response) |

Kolom: selalu `{module}_{field}` (mis. `rs_nama`, `detail_id_jenis`). FK: `{module}_id_{related}`.

---

## 7. Enums (bensampo)

`CuciEnum`, `LinenStatusEnum`, `RegisterEnum`, `RsStatusEnum`, `TransactionType` (+ `RoleEnum`, `StatusEnum`). Pakai `EnumTrait::getOptions()` untuk `<x-select>`, `getApi()` untuk JSON API. `TransactionType::KOTOR/REJECT/REWASH`.

---

## 8. Keamanan & Config

- Auth web: `Fortify` (registrasi publik MATI) + `auth` + `verified` + `access` middleware (lihat `routes/web.php`).
- Policy wajib — `GeneralRequest::authorize()` cek `$user->can($action, $model)` -> `BasePolicy::before()` -> `config/permision.php` (allowlist `modul => [role]`; modul tak terdaftar = perilaku lama). Modul `user`/`users` hanya `admin`/`developer` — role lain 403 di web; endpoint API users tidak didaftarkan sama sekali (404 envelope).
- Jangan tambah route user (web/API, eksplisit/auto) tanpa mendaftarkan modulnya di `config/permision.php` bila harus terkunci role.
- `APP_URL` di `.env` harus sesuai domain; `SANCTUM_STATEFUL_DOMAINS` untuk SPA bila ada.
- Jangan commit `.env`, `storage/`, `vendor/`. Upload via `uploadFile()` -> `storage/app/public/{folder}/` + `fileUrl()`.

---

## 9. Perintah Penting

```bash
composer install --no-dev          # prod
composer dev                        # serve + queue:listen + vite (concurrently)
composer lint:check && composer lint # pint
php artisan test                    # pest
php artisan boost:update            # setelah composer install
php artisan migrate --force
npm run dev | npm run build
```

---

## 10. Aturan untuk AI Agent

1. Ubah API (`routes/api.php`, `AuthController`, `TransaksiApiController`, `RegisterLinenController`) -> update `desktop/DAO/` + `LoginForm.cs` agar tidak 401/422.
2. Tambah modul master data -> ikuti checklist §4 (Model->Policy->ControllerTrait->Route::auto->Views).
3. Validasi bisnis tetap di web (`Enums` + `Model::rules()` + `Action`); desktop hanya collect & push batch.
4. Selalu pakai `Route::auto()` untuk CRUD standar; manual route hanya untuk custom (qrcode/pdf).
5. View wajib copy `pages/users/table.blade.php` & `form.blade.php` — jangan invent struktur baru.
