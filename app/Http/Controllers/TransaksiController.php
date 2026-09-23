<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Models\Transaksi;
use App\Models\User;

class TransaksiController extends Controller
{
    use ControllerTrait {
        getData as protected traitGetData;
    }

    public function __construct(Transaksi $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        return array_merge([
            'model' => $this->model,
            'status' => TransactionType::getOptions(),
        ], $data);
    }

    protected function getData()
    {
        return User::scopeRs($this->traitGetData(), ['transaksi.transaksi_rs_ori', 'transaksi.transaksi_rs_scan'])
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs as rs_scan', 'rs_scan.rs_id', '=', 'transaksi.transaksi_rs_scan')
            ->leftJoin('rs as rs_ori', 'rs_ori.rs_id', '=', 'transaksi.transaksi_rs_ori')
            ->leftJoin('users', 'users.id', '=', 'transaksi.transaksi_created_by')
            ->addSelect([
                'transaksi.*',
                'detail_linen.detail_status_linen as linen_status',
                'jenis_linen.jenis_nama as linen_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'rs_scan.rs_nama as rs_scan_nama',
                'rs_ori.rs_nama as rs_ori_nama',
                'users.name as operator_name',
            ]);
    }
}
