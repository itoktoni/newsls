<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Pending;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Http\Request;

class ReportPendingLinenController extends MinimalController
{
    public $data;

    public function __construct(Pending $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $jenis = JenisLinen::getOptions();
        $ruangan = Ruangan::getOptions();
        $cuci = CuciType::getOptions();
        $register = RegisterType::getOptions();
        $transaction = TransactionType::getOptions([
            TransactionType::KOTOR,
            TransactionType::BERSIH,
            TransactionType::REJECT,
            TransactionType::REWASH,
        ]);

        self::$share = [
            'rs' => $rs,
            'ruangan' => $ruangan,
            'jenis' => $jenis,
            'register' => $register,
            'cuci' => $cuci,
            'transaction' => array_merge($transaction, [
                'Pending' => 'Pending',
            ])
        ];
    }

    private function getQuery($request)
    {
        $query = Pending::query()
            ->leftJoin('rs', 'rs.rs_id', '=', 'pending.pending_id_rs')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'pending.pending_id_ruangan')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'pending.pending_id_jenis')
            ->leftJoin('view_detail_linen', 'view_detail_linen.view_linen_rfid', '=', 'pending.pending_rfid')
            ->join('config_linen', function ($join) {
                $join->on('config_linen.detail_rfid', '=', 'pending.pending_rfid') // Perbaikan penulisan detail_rfid / details_rfid
                    ->on('config_linen.rs_id', '=', 'rs.rs_id');
            })
            ->select([
                'pending.*',
                'rs.rs_nama',
                'ruangan.ruangan_nama',
                'jenis_linen.jenis_nama',
                'view_detail_linen.view_transaksi_bersih_total',
                'view_detail_linen.view_status_proses',
            ]);

        if ($rs_id = $request->get(ViewDetailLinen::field_rs_id())) {
            $query = $query->where('pending.pending_id_rs', $rs_id);
        }

        if ($start_date = $request->start_pending) {
            $query = $query->whereDate('pending.pending_kotor_at', '>=', $start_date);
        }

        if ($end_date = $request->end_pending) {
            $query = $query->whereDate('pending.pending_kotor_at', '<=', $end_date);
        }

         if ($start_bersih = $request->start_bersih) {
            $query = $query->whereDate('pending.pending_bersih_at', '>=', $start_bersih);
        }

        if ($end_bersih = $request->end_bersih) {
            $query = $query->whereDate('pending.pending_bersih_at', '<=', $end_bersih);
        }

        if ($status = $request->status) {

            if($request->get('status') == 'Pending')
            {
               $query = $query->whereNull('pending.pending_bersih_at');
            }
            else if($request->get('status') == 'BERSIH')
            {
               $query = $query->whereNotNull('pending.pending_bersih_at');
            }
            else
            {
                $query = $query->where('pending.pending_transaksi', $status);
            }

        }

        return $query->get();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
