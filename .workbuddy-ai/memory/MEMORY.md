# MEMORY.md — catatan jangka panjang project `bka`

## Konvensi yang wajib diikuti

- Kolom tidak pernah di-hardcode: pakai Property trait `app/Properties/{Model}Entity.php`
  → `Model::field_x()` + accessor `getFieldXAttribute()`.
- Semua relasi diawali `has` (`hasRs`, `hasRuangan`, `hasJenis`, `hasBahan`, `hasSupplier`).
- Controller pakai `App\Concerns\ControllerTrait` (+ `Route::auto` dari `izniburak/laravel-auto-routes`).
  Method `getX`/`postX` → nama route otomatis (`postCreate` → `POST /x/create`).
- **Response API wajib pakai `Plugins\Notes`** (`plugins/Notes.php`) — envelope tunggal:
  `{status, code, name, message, data}`. HTTP status **selalu 200** (kode asli di `body.code`),
  ini sengaja demi kompatibilitas klien mobile lama.
  `name` = `List|Data|Create|Update|Delete|Token|Validation|Error`.
  Dipakai juga oleh handler global di `bootstrap/app.php` untuk 401/404/405/422/429/500.
  Saat `code >= 400`, `data` dikosongkan jadi `[]`; detail validasi per field ada di key `errors`.
- `PayloadTrait` (`{code, status, message, data}`) masih dipakai sebagai kontrak **internal**
  Actions; dipetakan ke `Notes` di boundary controller (`ControllerTrait::apiResponse()`).
  `TOAST_SUCCESS`/`TOAST_FAILED` didefinisikan di `function/Global.php`.
- Business logic reusable ditaruh di **`app/Actions/`** (Laravel Actions, `lorisleiva`).
  `app/Services/` hanya untuk cross-cutting concern (mis. `CentrifugoService`).
  - Gaya nyata di repo: `use AsAction, PayloadTrait;` — **tanpa** extend
    `Lorisleiva\Actions\Action` (base class-nya isinya cuma `use AsAction`).
  - Action mengembalikan payload `{code,status,message,data}`; `PayloadTrait` otomatis
    set `code=500` + `status=false` kalau message-nya `TOAST_FAILED`.
  - `ValidationException` di-rethrow dari Action → diformat handler global jadi 422.
- Komentar bahasa Indonesia menjelaskan **kenapa**, dan ditandai `ponytail:` kalau itu tradeoff sadar.

## Model kepemilikan linen (penting, sering tertukar)

- `config_linen` = **MASTER**: daftar RS pemilik sah sebuah RFID. Satu RFID boleh punya banyak baris.
- `detail_linen.detail_id_rs` = **CURRENT holder**: RS yang sedang memegang linen.
- `FREE` → nol baris `config_linen`; `DEDICATED` → min 1 RS; `GROUP` → min 2 RS.
- Logikanya di `App\Models\ConfigLinen::syncRs()/isAllowed()` dan `DetailLinenController::resolveOwnership()`.

## ⚠️ Database nyata ≠ migration di repo

- Koneksi: MariaDB `bka` @127.0.0.1 (root, tanpa password). Isinya **skema legacy hasil import**:
  840 tabel, `detail_linen` 25 kolom, `transaksi` 440k baris, `bersih` 652k baris.
- Tabel `migrations` hanya mencatat batch 1 + `create_activity_log_table`.
  Migrasi `create_rs_table` / `create_detail_linen_table` / `create_config_linen_table`
  **tidak tercatat** karena tabelnya sudah ada → `php artisan migrate` biasa akan gagal
  `table already exists`. Jalankan migrasi baru dengan `--path=...`.
- `detail_linen` di DB punya kolom yang belum ada di migration repo:
  `detail_status_register`, `detail_lama`, `detail_pengantian_user/waktu`,
  `detail_deleted_by/at`, `detail_total_rewash/reject/bersih`.
- Tabel legacy yang sudah ada tapi **belum punya model** di `app/`: `outstanding` (0 baris),
  `transaksi`, `bersih`, `pending`, `opname`, `opname_detail`, `cetak`, `history`.
- Pivot `rs_dan_ruangan` / `rs_dan_jenis` dipakai model `Rs` tapi belum ada migration-nya.

### Bug schema yang sudah diperbaiki

1. `activity_log.subject_id` tadinya `bigint unsigned` (dari `nullableMorphs()`) padahal
   `DetailLinen` PK-nya string RFID → **setiap** `DetailLinen::create()` gagal
   `SQLSTATE[22007] Incorrect integer value`, jadi fitur register selalu gagal.
   Fix: migrasi `2026_09_21_101500_fix_activity_log_subject_id_for_string_keys.php`.
2. `detail_linen.detail_status_kepemilikan` tadinya `ENUM('FREE','DEDICATED')` padahal
   kode sudah pakai `RsStatusEnum::GROUP` → `Data truncated`. Fix: migrasi
   `2026_09_21_101600_add_group_to_detail_linen_kepemilikan.php`.

## API register linen (dibuat 2026-09-21)

- `POST /api/register` — register massal, 1 transaksi. Field ikut andalan:
  `rfid[]`, `rs_id` (skalar **atau** array), `ruangan_id`, `jenis_id`, `bahan_id`,
  `supplier_id`, `status_cuci`; opsional `status_register`, `status_kepemilikan`,
  `deskripsi`, `tgl_cek`.
- `GET /api/register/config` — opsi dropdown, filter `?rs_id=` lewat pivot.
- Keduanya di dalam `auth:sanctum`.
- Implementasi: `RegisterLinenController` → `App\Actions\RegisterLinenAction` → `RegisterLinenRequest`
  (Action dipanggil `RegisterLinenAction::run($request->validated())`; `RegisterLinenService` sudah dihapus).

Type kepemilikan diturunkan dari jumlah `rs_id` (bisa ditimpa `status_kepemilikan`):

| `rs_id` dikirim | type | `config_linen` | `detail_id_rs` |
|---|---|---|---|
| tidak ada | FREE | nol baris | null |
| skalar / 1 elemen | DEDICATED | 1 baris | `rs_id` |
| array ≥2 | GROUP | N baris | elemen pertama |

`status_register = GANTI_CHIP` → `detail_status_linen = KOTOR`, selain itu `REGISTER`.
Belum menulis `outstanding`/`transaksi` (model belum ada).

Catatan: `jenis_id` / `bahan_id` / `supplier_id` / `ruangan_id` masih pakai rule `exists`,
jadi ID dari DB lain (mis. DB dev berbeda) akan ditolak 422.

## API download linen (dibuat 2026-09-24)

- `GET /api/download/{rsid}` (Bearer, middleware `rs.access`) — sync master RFID 1 RS ke desktop.
  Route → `App\Http\Controllers\Api\DownloadApiController` (invokable, `->whereNumber('rsid')`).
- Kontrak mengikuti `andalan/app/Http/Resources/DownloadCollection`:
  `{status,code,name,message,total,data[],rs,ruangan,opname}`; item `data[]` =
  `rfid, rs_id, rs_nama, ruangan_id, ruangan_nama, jenis_id, jenis_nama, status_transaksi, status_proses, tanggal`.
  Sumbernya tabel BKA langsung (tidak ada `view_detail_linen` di project ini).
- **Kenapa bukan `DetailLinen::with(...)->get()`**: 12rb RFID → 12rb model + relasi → memory_limit
  habis di tengah response → JSON terpotong. Sekarang: generator + `DB::table` per chunk 2.000
  baris, status di-preload per chunk (`outstanding` + `SELECT DISTINCT transaksi_rfid`),
  `flush()` tiap chunk, `set_time_limit(0)`, query log off, `total` dikirim di depan `data`
  supaya desktop bisa mendeteksi download terpotong.
- Referensi angka: test `tests/Feature/Api/DownloadApiTest.php` — 12.000 baris ≈ 0,5 dtk,
  delta memory sisi app < 32 MB (tanpa `json_decode` di test).

## Kebiasaan kerja

- Folder `andalan/` = project lama, hanya untuk referensi. Jangan diedit.
- Setelah mengubah schema: uji ke DB nyata, lalu bersihkan data uji.
- `php artisan serve` di environment ini lambat untuk route `/up` (Livewire Blaze timeout) —
  bukan indikasi aplikasi rusak.
