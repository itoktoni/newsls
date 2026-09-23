# BKA Linen RFID Rebuild — Design Doc

Tanggal: 2026-09-20
Status: approved approach B (event-hardened rebuild)
Keputusan: full rebuild + reuse DB `bka` 100% + scan via API

## 1. Latar & temuan

Sistem lama `andalan/`: 67 controller (30+ report), pola `Dao/Models + Entities + Traits`,
`Buki\AutoRoute`, `DataBuilder` datatable. DB `bka` (MariaDB): `rs`, `ruangan`,
`kategori`, `jenis_linen`, `jenis_bahan`, `supplier`, `detail_linen` (PK `detail_rfid`
string), `transaksi` (kotor), `bersih`, `outstanding`, `pending`, `opname` +
`opname_detail`, `mutasi`, `cetak`, `history`, plus tabel auth `users`, `system_*`.

Stack baru (AGENTS.md): Laravel 13, Livewire 4 + Flux, `ControllerTrait`,
`Route::auto()`, Policy per model, bensampo Enum + `EnumTrait`, PowerJoins
`leftJoinRelationship`, `CreateAction/UpdateAction/DeleteAction`,
views `pages/{module}/table.blade.php + form.blade.php` mengikuti kanon `pages/users/`.

## 2. Arsitektur

- `App\Models/*` mapping 1:1 ke tabel `bka` apa adanya: custom `$table`,
  `$primaryKey`, `CREATED_AT/UPDATED_AT/DELETED_AT` kustom, `$incrementing=false`
  + `$keyType='string'` untuk PK RFID. Kolom lain tanpa migrasi.
- `App\Properties/*Entity` disalin dari `andalan/app/Dao/Entities` (sumber tunggal
  nama kolom `field_*()` + accessor `getField*Attribute`).
- `App\Enums/*` migrasi dari 23 enum lama ke bensampo (`TransactionType`,
  `ProcessType`, `RegisterType`, `BedaRsType`, `CuciType`, dst) + 2 enum baru
  `ScanArahEnum`, `ScanStatusEnum`, plus `LinenStatusEnum`.
- Controller tipis per modul (`ControllerTrait`), `share()` untuk dropdown
  `Enum::getOptions()`, `getData()` via `leftJoinRelationship` (tanpa `with()`),
  `Route::auto()` di `routes/web.php`, satu `*Policy extends BasePolicy` per model.
- Dua tabel BARU non-intrusif (satu-satunya migrasi): `scan_outbox`,
  `linen_daily_summary`. Tabel `bka` lain nol perubahan.

## 3. Komponen tahap 1

1. Master: Rs, Ruangan, Kategori, JenisLinen, JenisBahan, Supplier.
2. Sirkulasi: DetailLinen (RFID), Transaksi (kotor), Bersih, Outstanding,
   Pending, Mutasi, History.
3. Scan API + outbox worker + state machine guard.
4. Opname + OpnameDetail (snapshot + merge).
5. ReportEngine + summary (tahap 1: rekap bersih golden-master; 29 report lain tahap 2).

Setiap modul: Model + Entity + Policy + Controller + `Route::auto` +
`pages/{module}/table + form` + `config/permision.php` + `config/menu.php`.

## 4. Data flow — scan via API

1. Handheld/reader `POST /api/scan` (Sanctum): `{rfid, arah, device_id, waktu,
   idempotency_key}`.
2. `ScanController` validasi via model `rules()`, tulis SATU baris ke
   `scan_outbox` (`status=pending`, unique `idempotency_key`), balas `202 queued`.
3. Queue worker FIFO proses outbox: debounce 10 detik per `(rfid, device)`,
   panggil `DetailLinen::transitionTo($rfid, $target)` → guard transisi →
   insert `transaksi/history` (tidak pernah UPDATE in-place) → tandai outbox done.
4. Koreksi = event kompensasi `SCAN_VOID` (butuh `can(void)` + supervisor),
   bukan edit/hapus.
5. Dashboard Livewire baca proyeksi outstanding/stok via PowerJoins; opsional
   broadcast Centrifugo per batch.

State machine `LinenStatusEnum`: `kotor→cuci→bersih→distribusi→kotor`,
`distribusi→rusak_hilang` (karantina, hanya opname-re entry yang membuka).
Guard di `booted()->updating`, tolak dengan `ValidationException` → `TOAST_FAILED`.
Audit `linen_journey` ditulis atomik di `transitionTo()`.

## 5. ReportEngine

- `linen_daily_summary(tanggal, rs_id, jenis_id, bersih, kotor, retur, rewash,
  hilang, pending, parstok_snapshot)` diisi job harian + listener scan.
- Satu `ReportController` (thin `getInvoice/getMutasi/...`) delegasi ke
  `ReportEngine` service / Laravel Action; query hanya ke summary +
  `Filterable/Sortable`.
- `report:rebuild --from --to` replay `detail_linen` → summary; checksum harian
  + tombol repair per tanggal untuk self-healing drift.
- Tahap 1 cukup 1 report cocok 100% vs query lama sebelum 29 sisanya dipindah.

## 6. Error handling

- Double-scan dalam jendela: `200 deduplicated` (tanpa duplikat transaksi).
- Transisi ilegal: `422` + pesan enum; konkurensi: `where(status, expected)->update()`,
  pihak kalah retry dengan pesan jelas.
- Worker crash: replay dari `last_processed_id` (idempoten).
- Freeze-mode: semua `Route::auto` read-only kecuali opname darurat (tahap 2).

## 7. Testing

- Pest: state machine tolak `kotor→bersih`; idempotensi double-POST satu transaksi;
  `SCAN_VOID` butuh otorisasi; golden-master 1 report vs query lama.
- `composer lint` (Pint) + `php artisan test` hijau sebelum tiap tahap.

## 8. Non-goals tahap 1

SQLite sync per-RS, PWA offline-first, 29 report lanjutan, freeze-mode UI,
printer label — masuk tahap 2.

## 9. Self-review

- Placeholder: tidak ada TBD; dimensi summary dikunci di seksi 5.
- Konsistensi: arsitektur reuse-100% cocok dengan 2 tabel baru non-intrusif;
  tidak ada join live di report; tidak ada UPDATE in-place di sirkulasi.
- Scope: satu plan implementasi (master + sirkulasi + scan + 1 report).
- Ambiguitas: jendela debounce ditetapkan 10 detik; idempotency_key wajib dari
  client (fallback `rfid+arah+menit` di server).
