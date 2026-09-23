<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\CetakType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Pending;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;

class UpdateDeliveryService
{
    public function update($data)
    {
        DB::beginTransaction();

        try {

            $passing = $return = [];
            $startDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d').' 13:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d').' 23:59:59');

            $check_date = Carbon::now()->between($startDate, $endDate);
            $report_date = Carbon::now();

            if ($check_date) {
                $report_date = Carbon::now()->addDay(1);
            }

            $transaksi = $data->status_transaksi;
            if ($transaksi == TransactionType::KOTOR) {
                $transaksi = TransactionType::BERSIH;
            }

            $check = Bersih::query()
                ->whereNull(Bersih::field_delivery())
                ->where(Bersih::field_rs_id(), $data->rs_id)
                ->where(Bersih::field_status(), $transaksi)
                ->whereNotNull(Bersih::field_barcode())
                ->update([
                    Bersih::field_delivery() => $data->code,
                    Bersih::field_delivery_by() => auth()->user()->id,
                    Bersih::field_delivery_at() => date('Y-m-d H:i:s'),
                    Bersih::field_report() => $report_date->format('Y-m-d'),
                ]);

            $rfid = Bersih::select(Bersih::field_rfid(), Bersih::field_ruangan_id())
                ->where(Bersih::field_delivery(), $data->code)
                ->get();

            $return = [];

            if ($rfid && $check) {


                $update_by_location = $rfid->mapToGroups(function ($item) {
                    return [
                        $item->bersih_id_ruangan => $item->field_rfid
                    ];
                });

                foreach($update_by_location as $loc_id => $loc){
                    Detail::whereIn(Detail::field_primary(), $loc)
                    ->update([
                        Detail::field_ruangan_id() => $loc_id
                    ]);
                }

                $data_rfid = $rfid->pluck(Bersih::field_rfid());

                $detail = [
                    Detail::field_rs_id() => $data->rs_id,
                    Detail::field_status_linen() => TransactionType::BERSIH,
                    Detail::field_updated_at() => date('Y-m-d H:i:s'),
                    Detail::field_report() => $report_date->format('Y-m-d'),
                    Detail::field_updated_by() => auth()->user()->id,
                ];

                if ($data->status_transaksi == TransactionType::REWASH) {
                    $detail = array_merge($detail, [
                        Detail::field_total_rewash() => DB::raw('detail_total_rewash + 1')
                    ]);
                } else if($data->status_transaksi == TransactionType::REJECT){
                    $detail = array_merge($detail, [
                        Detail::field_total_reject() => DB::raw('detail_total_reject + 1')
                    ]);
                } else {
                    $detail = array_merge($detail, [
                        Detail::field_total_bersih() => DB::raw('detail_total_bersih + 1')
                    ]);
                }

                Detail::whereIn(Detail::field_primary(), $data_rfid)
                ->update($detail);

                Outstanding::whereIn(Outstanding::field_primary(), $data_rfid)->delete();
                Pending::where('pending_transaksi', '!=', TransactionType::BERSIH)
                ->whereIn('pending_rfid', $data_rfid)->update([
                    'pending_bersih_by' => auth()->user()->id,
                    'pending_bersih_at' => $report_date->format('Y-m-d H:i:s'),
                    'pending_updated_at' => $report_date->format('Y-m-d H:i:s'),
                    'pending_delivery' => $data->code,
                ]);

                History::bulk($data_rfid, LogType::BERSIH, 'assign rs ', $data->rs_id);

                // PRINT

                $code = $data->code;

                // $opname = Opname::where(Opname::field_status(), OpnameType::Proses)
                // ->first();

                // if($opname)
                // {
                //     //ketika grouping update at nya di update
                //     OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)
                //         ->whereIn('opname_detail_rfid', $data->rfid)
                //         ->where('opname_detail_ketemu', BooleanType::NO)
                //         ->update([
                //             OpnameDetail::field_scan_rs() => BooleanType::YES,
                //             OpnameDetail::field_ketemu() => BooleanType::YES,
                //             OpnameDetail::field_waktu() => date('Y-m-d H:i:s'),
                //             OpnameDetail::field_sync() => BooleanType::YES,
                //             OpnameDetail::field_reff() => $code,
                //             OpnameDetail::field_scan_by() => LogType::BERSIH,
                //         ]);
                // }

                $total = Bersih::where(Bersih::field_delivery(), $code)
                    ->addSelect([
                        'bersih_rfid',
                        'bersih_id_rs',
                        'bersih_id_ruangan',
                        'bersih_barcode',
                        'bersih_delivery',
                        'bersih_status',
                        'bersih_report',
                        'rs_nama',
                        'ruangan_id',
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
                            Cetak::field_type() => CetakType::Delivery,
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
                            'tgl' => formatDate($item->bersih_report, 'd/M/Y'),
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
                            'user' => $item[0]['nama'] ?? null,
                            'status' => $item[0]['status'] ?? null,
                            'total' => count($item),
                        ];

                        $no++;
                    }
                }

            } else {
                DB::rollBack();

                return Notes::error('RFID tidak ditemukan!');
            }

            DB::commit();

            return Notes::data($return, $passing);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }
    }
}
