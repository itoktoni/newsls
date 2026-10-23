<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Requests\GeneralRequest;
use App\Models\Transaksi;
use App\Models\TransaksiMaster;
use App\Models\User;

class TransaksiController extends Controller
{
    use ControllerTrait {
        getData as protected traitGetData;
        getShow as protected traitGetShow;
    }

    public function __construct(TransaksiMaster $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        return array_merge([
            'model' => $this->model,
            'status' => TransactionType::getOptions(),
            'rs' => User::rsOptions(),
        ], $data);
    }

    /**
     * Master list: 1 baris per kode transaksi + nama RS + operator +
     * hitung baris detail. Filter status (KOTOR/REJECT/REWASH) lewat
     * ?filters[master_status][$eq]=KOTOR atau _q/_field.
     */
    protected function getData()
    {
        return User::scopeRs($this->traitGetData(), ['transaksi_master.master_rs_id'])
            ->leftJoin('rs', 'rs.rs_id', '=', 'transaksi_master.master_rs_id')
            ->leftJoin('users', 'users.id', '=', 'transaksi_master.master_created_by')
            ->withCount('hasDetails as detail_count')
            ->addSelect([
                'transaksi_master.*',
                'rs.rs_nama as rs_nama',
                'users.name as operator_name',
            ])
            // Berat bersih live (Σ jenis_berat detail) — tidak disimpan di master.
            ->selectRaw('(SELECT SUM(jenis_linen.jenis_berat) FROM transaksi JOIN detail_linen ON detail_linen.detail_rfid = transaksi.transaksi_rfid JOIN jenis_linen ON jenis_linen.jenis_id = detail_linen.detail_id_jenis WHERE transaksi.transaksi_key = transaksi_master.master_kode) as bersih_kg');
    }

    /**
     * Master + detail: header master berikut seluruh baris RFID
     * dalam kode yang sama (paginated 50).
     */
    public function getShow(GeneralRequest $request, $id)
    {
        $master = $this->model->with(['hasRs', 'hasOperator'])->findOrFail($id);

        if ($master->master_rs_id) {
            User::ensureRsAccess((int) $master->master_rs_id);
        }

        $details = Transaksi::query()
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs as rs_scan', 'rs_scan.rs_id', '=', 'transaksi.transaksi_rs_scan')
            ->leftJoin('rs as rs_ori', 'rs_ori.rs_id', '=', 'transaksi.transaksi_rs_ori')
            ->where('transaksi.transaksi_key', $master->master_kode)
            ->orderBy('transaksi.transaksi_id')
            ->addSelect([
                'transaksi.*',
                'detail_linen.detail_status_linen as linen_status',
                'jenis_linen.jenis_nama as linen_nama',
                'jenis_linen.jenis_berat as jenis_berat',
                'ruangan.ruangan_nama as ruangan_nama',
                'rs_scan.rs_nama as rs_scan_nama',
                'rs_ori.rs_nama as rs_ori_nama',
            ])
            ->paginate($request->input('per_page', 50))
            ->withQueryString();

        if ($this->isApi()) {
            return $this->views($this->template(), [
                'master' => $master,
                'details' => $details,
            ]);
        }

        $beratBersih = (float) Transaksi::where('transaksi_key', $master->master_kode)
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->sum('jenis_linen.jenis_berat');

        return view($this->template(), $this->share([
            'master' => $master,
            'details' => $details,
            'beratBersih' => $beratBersih,
        ]));
    }
}
