<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\GeneralRequest;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Menu Bersih — gabungan Packing & Delivery (web, VIEWER SAJA).
 *
 * Struktur tabel mengikuti pola standar Data Linen (ControllerTrait):
 * - x-filter (quick search + Advanced drawer) → filters[field][op], _q/_field
 * - x-table-sort → sort.0=field:dir, cursorPaginate + withQueryString
 * - x-table + x-slot:mobile, x-pagination, table.js + initTable
 *
 * - Tab Packing  : antrean Outstanding (SCAN/QC/REGISTER).
 * - Tab Delivery : Outstanding PACKING.
 * - Tab Riwayat  : history cetak packing/delivery (tabel cetak legacy).
 *
 * Semua PROSES (packing/delivery) hanya lewat aplikasi desktop via API.
 */
class BersihController extends Controller
{
    public function getTable(GeneralRequest $request)
    {
        $tab = $request->input('tab', 'packing');
        if (! in_array($tab, ['packing', 'delivery'])) {
            $tab = 'packing';
        }

        $perPage = (int) $request->input('per_page', 25);

        $currentSort = $request->input('sort.0', '');
        $sortField = str_replace(':desc', '', str_replace(':asc', '', $currentSort));
        $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';

        $stats = [
            'antrean_packing' => User::scopeRs(Outstanding::whereIn('outstanding_status_proses', ['SCAN', 'QC', 'REGISTER', 'GUDANG']), 'outstanding.outstanding_rs_scan')->count(),
            'siap_delivery' => User::scopeRs(Outstanding::where('outstanding_status_proses', 'PACKING'), 'outstanding.outstanding_rs_scan')->count(),
            // Event "menjadi BERSIH hari ini" = baris tabel bersih yang dibuat hari ini
            // (riwayat per-RFID). Jangan pakai status DetailLinen saat ini — linen yang
            // sudah BERSIH lalu di-scan KOTOR lagi akan hilang dari hitungan padahal
            // riwayat cetak tetap menampilkan barisnya.
            'bersih_hari_ini' => User::scopeRs(
                DB::table('bersih')->whereDate('bersih_created_at', today()),
                'bersih.bersih_id_rs'
            )->count(),
        ];

        $share = [
            'rsOptions' => User::rsOptions(),
            'statusOptions' => TransactionType::getOptions(),
        ];

        // Packing = baris bersih yang dibuat hari ini (berapa banyak yang di-packing
        // hari itu); Delivery = baris bersih yang sudah terkirim (bersih_delivery terisi).
        $map = [
            'bersih_rfid' => 'bersih.bersih_rfid',
            'bersih_id_rs' => 'bersih.bersih_id_rs',
            'bersih_status' => 'bersih.bersih_status',
            'bersih_barcode' => 'bersih.bersih_barcode',
            'bersih_delivery' => 'bersih.bersih_delivery',
            'bersih_report' => 'bersih.bersih_report',
            'linen_nama' => 'jenis_linen.jenis_nama',
            'rs_nama' => 'rs.rs_nama',
        ];
        $fields = [
            'bersih_rfid' => 'RFID',
            'bersih_id_rs' => 'Rumah Sakit',
            'bersih_status' => 'Status',
            'linen_nama' => 'Linen',
            'rs_nama' => 'RS',
        ];
        $sortMap = [
            'bersih_rfid' => 'bersih.bersih_rfid',
            'bersih_report' => 'bersih.bersih_report',
            'bersih_delivery' => 'bersih.bersih_delivery',
            'bersih_barcode' => 'bersih.bersih_barcode',
        ];
        $q = $this->bersihListQuery();

        if ($tab === 'packing') {
            // Packing = berapa banyak yang di-packing hari itu (baris bersih hari ini)
            $q->whereDate('bersih.bersih_created_at', today());
        } else {
            // Delivery = baris bersih yang sudah terkirim
            $q->whereNotNull('bersih.bersih_delivery');
        }

        $data = $this->applySort(
            $this->applyFilters($q, $map, $request),
            $sortMap, 'bersih.bersih_id', $sortField, $sortDir
        )->cursorPaginate($perPage)->withQueryString();

        return view('pages.bersih.table', array_merge($share, [
            'tab' => $tab, 'stats' => $stats, 'data' => $data, 'fields' => $fields,
            'sortField' => $sortField, 'sortDir' => $sortDir,
        ]));
    }

    /**
     * Terapkan filters[field][op] + _q/_field ala ControllerTrait::getData.
     * $map = [requestField => qualifiedColumn] sebagai whitelist.
     */
    private function applyFilters($query, array $map, Request $request)
    {
        foreach ((array) $request->input('filters', []) as $field => $conditions) {
            if (! isset($map[$field])) {
                continue;
            }
            $col = $map[$field];

            if (is_array($conditions)) {
                foreach ($conditions as $operator => $value) {
                    if ($value === '' || $value === null) {
                        continue;
                    }
                    match ($operator) {
                        '$contains' => $query->whereRaw('LOWER('.$col.') LIKE ?', ['%'.strtolower($value).'%']),
                        '$eq' => $query->whereRaw('LOWER('.$col.') = ?', [strtolower($value)]),
                        '$gt' => $query->where($col, '>', $value),
                        '$gte' => $query->where($col, '>=', $value),
                        '$lt' => $query->where($col, '<', $value),
                        '$lte' => $query->where($col, '<=', $value),
                        '$ne' => $query->whereRaw('LOWER('.$col.') != ?', [strtolower($value)]),
                        '$in' => $query->whereIn($col, (array) $value),
                        default => $query->whereRaw('LOWER('.$col.') LIKE ?', ['%'.strtolower($value).'%']),
                    };
                }
            } elseif (is_string($conditions) && $conditions !== '') {
                $query->whereRaw('LOWER('.$col.') LIKE ?', ['%'.strtolower($conditions).'%']);
            }
        }

        $q = $request->input('_q');
        $searchField = $request->input('_field');
        if ($q && $searchField && isset($map[$searchField])) {
            $query->whereRaw('LOWER('.$map[$searchField].') LIKE ?', ['%'.strtolower($q).'%']);
        }

        return $query;
    }

    private function applySort($query, array $sortMap, string $default, string $field, string $dir)
    {
        if (isset($sortMap[$field])) {
            return $query->orderBy($sortMap[$field], $dir === 'desc' ? 'desc' : 'asc');
        }

        return $query->orderBy($default, 'desc');
    }

    private function bersihListQuery()
    {
        return User::scopeRs(DB::table('bersih'), 'bersih.bersih_id_rs')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'bersih.bersih_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'bersih.bersih_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'bersih.bersih_id_rs')
            ->leftJoin('users', 'users.id', '=', 'bersih.bersih_created_by')
            ->select([
                'bersih.*',
                'jenis_linen.jenis_nama as linen_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'rs.rs_nama as rs_nama',
                'users.name as user_nama',
            ]);
    }
}
