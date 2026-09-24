<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\GeneralRequest;
use App\Models\JenisLinen;
use App\Models\Outstanding;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

/**
 * Menu Warehouse — stok linen di gudang utama (viewer saja).
 *
 * Alur: grouping/QC lolos → outstanding_status_proses='GUDANG' +
 * outstanding_warehouse_id (env GUDANG_UTAMA_ID) → packing → delivery.
 * Substitusi RFID (kotor A dikirim B sejenis) didukung penuh karena
 * packing/delivery memilih RFID bebas per jenis (berlaku semua status milik).
 *
 * Tanpa $this->model sehingga lolos GeneralRequest::authorize().
 */
class WarehouseController extends Controller
{
    public function getTable(GeneralRequest $request)
    {
        $gudangId = Warehouse::utamaId();
        $perPage = (int) $request->input('per_page', 25);

        $currentSort = $request->input('sort.0', '');
        $sortField = str_replace(':desc', '', str_replace(':asc', '', $currentSort));
        $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';

        $base = $this->stockQuery($gudangId);

        $stats = [
            'gudang' => Warehouse::find($gudangId),
            'total_pcs' => (clone $base)->count(),
            'per_jenis' => User::scopeRs(Outstanding::query(), 'outstanding.outstanding_rs_scan')
                ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
                ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                ->where(fn ($q) => $q->whereNull('outstanding.outstanding_id_warehouse')->orWhere('outstanding.outstanding_id_warehouse', $gudangId))
                ->selectRaw('COALESCE(jenis_linen.jenis_nama, ?) as nama, COUNT(*) as pcs', ['Tanpa Jenis'])
                ->groupBy('jenis_linen.jenis_nama')
                ->orderByDesc('pcs')
                ->limit(12)
                ->get(),
        ];

        $map = [
            'outstanding_rfid' => 'outstanding.outstanding_rfid',
            'outstanding_rs_scan' => 'outstanding.outstanding_rs_scan',
            'outstanding_id_ruangan' => 'outstanding.outstanding_id_ruangan',
            'outstanding_status_transaksi' => 'outstanding.outstanding_status_transaksi',
            'linen_nama' => 'jenis_linen.jenis_nama',
        ];
        $fields = [
            'outstanding_rfid' => 'RFID',
            'outstanding_rs_scan' => 'RS Scan',
            'outstanding_id_ruangan' => 'Ruangan',
            'outstanding_status_transaksi' => 'Status Transaksi',
            'linen_nama' => 'Linen',
        ];
        $sortMap = [
            'outstanding_rfid' => 'outstanding.outstanding_rfid',
            'outstanding_status_transaksi' => 'outstanding.outstanding_status_transaksi',
            'outstanding_updated_at' => 'outstanding.outstanding_updated_at',
        ];
        $data = $this->applySort(
            $this->applyFilters($base, $map, $request),
            $sortMap, 'outstanding.outstanding_updated_at', $sortField, $sortDir
        )->cursorPaginate($perPage)->withQueryString();

        return view('pages.warehouse.table', [
            'statusOptions' => TransactionType::getOptions(),
            'rsOptions' => User::rsOptions(),
            'ruanganOptions' => Ruangan::orderBy('ruangan_nama')->pluck('ruangan_nama', 'ruangan_id')->all(),
            'jenisOptions' => JenisLinen::orderBy('jenis_nama')->pluck('jenis_nama', 'jenis_id')->all(),
            'tab' => 'stok',
            'stats' => $stats,
            'data' => $data,
            'fields' => $fields,
            'sortField' => $sortField,
            'sortDir' => $sortDir,
        ]);
    }

    private function stockQuery(int $gudangId)
    {
        return User::scopeRs(Outstanding::query(), 'outstanding.outstanding_rs_scan')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'outstanding.outstanding_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'outstanding.outstanding_rs_scan')
            ->leftJoin('warehouse', 'warehouse.warehouse_id', '=', 'outstanding.outstanding_id_warehouse')
            ->where(fn ($q) => $q->whereNull('outstanding.outstanding_id_warehouse')->orWhere('outstanding.outstanding_id_warehouse', $gudangId))
            ->addSelect([
                'outstanding.outstanding_rfid',
                'outstanding.outstanding_key',
                'outstanding.outstanding_status_transaksi',
                'outstanding.outstanding_status_proses',
                'outstanding.outstanding_updated_at',
                'jenis_linen.jenis_nama as linen_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'rs.rs_nama as rs_nama',
                'warehouse.warehouse_nama as gudang_nama',
            ]);
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
}
