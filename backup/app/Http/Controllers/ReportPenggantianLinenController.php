<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\GantiChip;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\User;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use Illuminate\Http\Request;

class ReportPenggantianLinenController extends MinimalController
{
    public $data;

    public function __construct(DetailRepository $repository)
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
        $keterangan = TransactionType::getOptions();

        self::$share = [
            'rs' => $rs,
            'ruangan' => $ruangan,
            'jenis' => $jenis,
            'register' => $register,
            'cuci' => $cuci,
            'keterangan' => $keterangan,
        ];
    }

    private function getQuery($request)
    {
        $ganti = GantiChip::getTableName();
        $detail = Detail::getTableName();
        $jenis = JenisLinen::getTableName();
        $rs = Rs::getTableName();
        $ruangan = Ruangan::getTableName();
        $users = with(new User)->getTable();

        $query = GantiChip::query()
            ->select([
                $ganti.'.'.GantiChip::field_lama().' as rfid_lama',
                $ganti.'.'.GantiChip::field_baru().' as rfid_baru',
                $ganti.'.'.GantiChip::field_keterangan().' as keterangan',
                $ganti.'.'.GantiChip::field_tanggal().' as detail_pengantian_waktu',
                $jenis.'.'.JenisLinen::field_name().' as jenis_nama',
                $rs.'.'.Rs::field_name().' as rs_nama',
                $ruangan.'.'.Ruangan::field_name().' as ruangan_nama',
                $users.'.'.User::field_name().' as operator_name',
            ])
            ->leftJoin($detail, $detail.'.'.Detail::field_primary(), '=', $ganti.'.'.GantiChip::field_baru())
            ->leftJoin($jenis, $jenis.'.'.JenisLinen::field_primary(), '=', $detail.'.'.Detail::field_jenis_id())
            ->leftJoin($rs, $rs.'.'.Rs::field_primary(), '=', $detail.'.'.Detail::field_rs_id())
            ->leftJoin($ruangan, $ruangan.'.'.Ruangan::field_primary(), '=', $detail.'.'.Detail::field_ruangan_id())
            ->leftJoin($users, $users.'.'.User::field_primary(), '=', $ganti.'.'.GantiChip::field_by());

            if($request['filter_start'])
            {
                $query = $query->whereDate($ganti.'.'.GantiChip::field_tanggal(), '>=', $request['filter_start']);
            }

            if($request['filter_end'])
            {
                $query = $query->whereDate($ganti.'.'.GantiChip::field_tanggal(), '<=', $request['filter_end']);
            }

            if($request['view_rs_id'])
            {
                $query = $query->where($detail.'.'.Detail::field_rs_id(), $request['view_rs_id']);
            }

            if($request['view_ruangan_id'])
            {
                $query = $query->where($detail.'.'.Detail::field_ruangan_id(), $request['view_ruangan_id']);
            }

            if($request['view_linen_id'])
            {
                $query = $query->where($detail.'.'.Detail::field_jenis_id(), $request['view_linen_id']);
            }

            if($request['filter_keterangan'])
            {
                $query = $query->where($ganti.'.'.GantiChip::field_keterangan(), $request['filter_keterangan']);
            }

            return $query->orderBy($ganti.'.'.GantiChip::field_tanggal(), 'DESC')->get();
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
