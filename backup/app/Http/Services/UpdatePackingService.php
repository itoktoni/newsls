<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\CetakType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Cetak;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Pending;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;

class UpdatePackingService
{
    public function update($data)
    {
        $passing = $return = [];

        DB::beginTransaction();
        try {
            Bersih::insert($data->bersih);

            Outstanding::whereIn(Outstanding::field_primary(), $data->rfid)
                ->update([
                    Outstanding::field_rs_ori() => $data->rs_id,
                    Outstanding::field_ruangan_id() => $data->ruangan_id,
                    Outstanding::field_status_process() => ProcessType::PACKING,
                    Outstanding::field_updated_at() => date('Y-m-d H:i:s'),
                    Outstanding::field_status_hilang() => HilangType::NORMAL,
                    Outstanding::field_hilang_created_at() => null,
                    Outstanding::field_pending_created_at() => null,
                ]);

            Pending::where('pending_transaksi', '!=', TransactionType::BERSIH)
                ->where('pending_rfid', $data->rfid)->update([
                    'pending_updated_at' => date('Y-m-d H:i:s'),
                    'pending_proses' => ProcessType::PACKING,
                ]);

            History::bulk($data->rfid, LogType::PACKING, 'Assign to Rs '.$data->rs_name, $data->rs_id);

            // CETAK PRINT

            $code = $data->uuid;

             $opname = Opname::where(Opname::field_status(), OpnameType::Proses)
                ->first();

                if($opname)
                {
                    //ketika grouping update at nya di update
                    OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)
                        ->whereIn('opname_detail_rfid', $data->rfid)
                        ->where('opname_detail_ketemu', BooleanType::NO)
                        ->update([
                            OpnameDetail::field_scan_rs() => BooleanType::YES,
                            OpnameDetail::field_ketemu() => BooleanType::YES,
                            OpnameDetail::field_waktu() => date('Y-m-d H:i:s'),
                            OpnameDetail::field_sync() => BooleanType::YES,
                            OpnameDetail::field_reff() => $code,
                            OpnameDetail::field_scan_by() => LogType::PACKING,
                        ]);
                }

            $total = Bersih::where(Bersih::field_barcode(), $code)
                ->addSelect([
                    'bersih_rfid',
                    'bersih_id_rs',
                    'bersih_id_ruangan',
                    'bersih_barcode',
                    'bersih_delivery',
                    'bersih_status',
                    'bersih_created_at',
                    'rs_nama',
                    'ruangan_nama',
                    'jenis_id',
                    'jenis_nama',
                    'name',
                ])
                ->leftJoinRelationship('has_rs')
                ->leftJoinRelationship('has_ruangan')
                ->leftJoinRelationship('has_user')
                ->leftJoinRelationship('has_detail.has_jenis')
                ->get();

            $data = null;

            if ($total->count() > 0) {

                $cetak = Cetak::where(Cetak::field_name(), $code)->first();
                if (! $cetak) {
                    $cetak = Cetak::create([
                        Cetak::field_date() => date('Y-m-d'),
                        Cetak::field_name() => $code,
                        Cetak::field_type() => CetakType::Barcode,
                        Cetak::field_user() => auth()->user()->name ?? null,
                        Cetak::field_rs_id() => $total[0]->bersih_id_rs ?? null,
                    ]);
                }

                $data = $total->mapToGroups(function ($item) {
                    $parse = [
                        'id' => $item->jenis_id,
                        'nama' => $item->jenis_nama,
                        'rs' => $item->rs_nama,
                        'lokasi' => $item->ruangan_nama,
                        'status' => $item->bersih_status,
                        'tgl' => formatDate($item->bersih_created_at, 'd/M/Y'),
                    ];

                    return [$item['jenis_id'].'#'.$item['ruangan_id'] => $parse];
                });

                $no = 1;
                foreach ($data as $item) {
                    $return[] = [
                        'id' => $no,
                        'code' => $code,
                        'tgl' => $item[0]['tgl'] ?? null,
                        'rs' => $item[0]['rs'] ?? null,
                        'nama' => $item[0]['nama'] ?? null,
                        'lokasi' => $item[0]['lokasi'] ?? null,
                        'status' => $item[0]['status'] ?? null,
                        'user' => auth()->user()->name ?? null,
                        'total' => count($item),
                    ];

                    $no++;
                }
            }

            DB::commit();

            return Notes::data($return, $passing);

        } catch (\Throwable $th) {
            DB::rollBack();

            return Notes::error($th->getMessage());
        }
    }
}
