# Dashboard Progressive Cache (Approach C)

**Date:** 2026-09-23
**Status:** Approved (user: "oke c dulu")
**Project:** BKA Web — Laravel 13

---

## 1. Problem

Dashboard 3 peran berat: banyak `COUNT`/join per request (KPI, sebaran, antrean, warehouse, System Overview) + chart loop N hari × 2 query.

## 2. Solution (C)

**Short-TTL per-role cache + invalidasi after-commit** — bukan pengganti query (A) atau materialized stats (B).

1. `App\Support\DashboardCache::remember($scope, $segment, fn)` — key `dash:{gen}:{scope}:{segment}`, TTL `config('dashboard.cache_ttl')` (default 60 dtk).
2. **Generation bump** `DashboardCache::flush()` — `dashboard:gen` + 1 → semua key lama basi (tanpa tags; cocok file/database/array store).
3. **Scope** = role + hash allowed RS (`User::allowedRsIds`) — RS user tidak bocor cache milik RS lain; admin/laundry tanpa mapping = `all`.
4. **Flush setelah commit** di write path: transaksi, register, packing, delivery, opname capture/sync, Create/Update/Delete master, DetailLinen custom update.
5. **Chart**: pisah data array (`kotorVsBersihData`, `statusLinenDonutData`) → cache array; bangun `LarapexChart` dari array (object chart tidak di-cache; `serializable_classes => false`).

## 3. Non-goals

- Wire:init / endpoint partial chart (progresif kedua — test masih `assertSee` judul di HTML awal).
- Materialized `dashboard_stats` (B) / UNION ALL collapse (A).
- Ubah kontrak API desktop.

## 4. Risks

| Risk | Mitigation |
|---|---|
| Angka basah setelah scan bulk | flush after-commit + TTL 60s |
| Forget sebelum commit / double | flush hanya setelah `DB::commit()` |
| Scope bocor antar RS | key hash `allowedRsIds` |
| Object chart gagal serialize | cache hanya array |

## 5. Acceptance

- Dashboard render sama; `DashboardRoleTest` hijau.
- Test cache: hit kedua tidak re-query; `flush()` → nilai baru.
- `vendor/bin/pint` file disentuh; `php artisan test`.
