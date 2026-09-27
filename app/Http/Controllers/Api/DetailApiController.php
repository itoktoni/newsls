<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class DetailApiController extends Controller
{
    /**
     * POST /api/detail/rfid — detail linen per RFID.
     *
     * Kontrak legacy andalan: satu item per RFID yang diminta, urutannya mengikuti
     * input, dan RFID yang tidak dikenal tetap dikembalikan dengan nilai null.
     */
    public function rfid(Request $request)
    {
        $request->validate([
            'rfid' => 'required|array|min:1',
            'rfid.*' => 'required|string',
        ]);

        $rfids = array_values(array_unique($request->input('rfid')));

        $rows = DB::table('detail_linen')
            ->leftJoin('outstanding', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('jenis_bahan', 'jenis_bahan.bahan_id', '=', 'detail_linen.detail_id_bahan')
            ->leftJoin('supplier', 'supplier.supplier_id', '=', 'detail_linen.detail_id_supplier')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
            ->leftJoin('users', 'users.id', '=', 'detail_linen.detail_updated_by')
            ->whereIn('detail_linen.detail_rfid', $rfids)
            ->select([
                'detail_linen.*',
                'outstanding.outstanding_status_transaksi',
                'outstanding.outstanding_status_proses',
                'jenis_linen.jenis_nama',
                'jenis_bahan.bahan_nama',
                'supplier.supplier_nama',
                'ruangan.ruangan_nama',
                'rs.rs_nama',
                'users.name as user_nama',
            ])
            ->get()
            ->keyBy('detail_rfid');

        $kosong = array_fill_keys([
            'jenis_id', 'jenis_nama', 'bahan_id', 'bahan_nama', 'supplier_id', 'supplier_nama',
            'rs_id', 'rs_nama', 'ruangan_id', 'ruangan_nama', 'status_register', 'status_cuci',
            'status_transaksi', 'status_proses', 'tanggal_create', 'tanggal_update', 'pemakaian',
            'user_nama',
        ], null);

        $data = [];
        foreach ($rfids as $rfid) {
            $row = $rows->get($rfid);

            if ($row === null) {
                $data[] = ['rfid' => $rfid] + $kosong;

                continue;
            }

            $data[] = [
                'rfid' => $rfid,
                // Legacy mengisi jenis_id dari detail_rfid (bug); di sini dipakai
                // kolom yang benar supaya klien dapat jenis linen yang sesungguhnya.
                'jenis_id' => $row->detail_id_jenis,
                'jenis_nama' => $row->jenis_nama ?? '',
                'bahan_id' => $row->detail_id_bahan,
                'bahan_nama' => $row->bahan_nama ?? '',
                'supplier_id' => $row->detail_id_supplier,
                'supplier_nama' => $row->supplier_nama ?? '',
                'rs_id' => $row->detail_id_rs ?? '',
                'rs_nama' => $row->rs_nama ?? '',
                'ruangan_id' => $row->detail_id_ruangan,
                'ruangan_nama' => $row->ruangan_nama ?? '',
                'status_register' => $row->detail_status_register,
                'status_cuci' => $row->detail_status_cuci,
                'status_transaksi' => $row->outstanding_status_transaksi ?? TransactionType::BERSIH,
                'status_proses' => $row->outstanding_status_proses ?? TransactionType::BERSIH,
                'tanggal_create' => $row->detail_created_at ? Carbon::parse($row->detail_created_at)->format('Y-m-d') : null,
                'tanggal_update' => $row->detail_updated_at ? Carbon::parse($row->detail_updated_at)->format('Y-m-d') : null,
                'pemakaian' => $row->detail_total_bersih ?? 0,
                'user_nama' => $row->user_nama,
            ];
        }

        return Notes::data($data);
    }
}
