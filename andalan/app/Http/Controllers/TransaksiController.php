<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BedaRsType;
use App\Dao\Enums\BooleanType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\SyncType;
use App\Dao\Enums\TransactionType;
use App\Dao\Enums\YesNoType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewTransaksi;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Plugins\Alert;
use Plugins\History as PluginsHistory;
use Plugins\Notes;
use Plugins\Response;

class TransaksiController extends MasterController
{
    public function __construct(TransaksiRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postCreate(GeneralRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    private function getTransaksi($code)
    {
        $view = ViewTransaksi::find($code);

        if ($view) {
            $transaksi = Transaksi::with([HAS_DETAIL, HAS_RS])
                ->where(Transaksi::field_key(), $view->field_key)
                ->where(Transaksi::field_status_transaction(), $view->field_status_transaction);

            return $transaksi;
        }

        return $view;
    }

    public function getUpdate($code)
    {
        $transaksi = $this->getTransaksi($code);
        if (!$transaksi) {
            return Response::redirectTo(moduleRoute('getTable'));
        }

        return moduleView(modulePathForm(), $this->share([
            'model' => $this->get($code),
            'data' => $transaksi->get(),
        ]));
    }

    public function getDeleteTransaksi($code)
    {
        $transaksi = Transaksi::with([HAS_DETAIL])->findOrFail($code);
        if ($transaksi) {

            PluginsHistory::log($transaksi->field_rfid, LogType::DELETE_TRANSAKSI, 'Data di delete dari transaksi ' . $transaksi->field_primary);
            Notes::delete($transaksi->get()->toArray());
            Alert::delete();

            $transaksi->delete();
        }

        return Response::redirectBack();
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $transaksi = $this->getTransaksi($code);

        if ($transaksi) {
            $rfid = $transaksi->pluck(Transaksi::field_rfid());

            $bulk = $transaksi->get()->toArray();
            PluginsHistory::bulk($rfid, LogType::DELETE_TRANSAKSI, $bulk);
            Notes::delete($bulk);
            Alert::delete();
            $transaksi->delete();
        }

        return Response::redirectBack($transaksi);
    }

    public function kotor(TransactionRequest $request)
    {
        $request[STATUS_TRANSAKSI] = TransactionType::KOTOR;
        $request[STATUS_PROCESS] = ProcessType::SCAN;

        return $this->transaction($request);
    }

    public function retur(TransactionRequest $request)
    {
        $request[STATUS_TRANSAKSI] = TransactionType::REJECT;
        $request[STATUS_PROCESS] = ProcessType::SCAN;

        return $this->transaction($request);
    }

    public function rewash(TransactionRequest $request)
    {
        $request[STATUS_TRANSAKSI] = TransactionType::REWASH;
        $request[STATUS_PROCESS] = ProcessType::SCAN;

        return $this->transaction($request);
    }

    private function rfidCanSyncToServer($form_transaksi, $status_transaksi, $date)
    {
        if ($status_transaksi != TransactionType::BERSIH) {
            return false;
        }

        if (($form_transaksi == TransactionType::KOTOR) && !(now()->diffInHours($date) >= env('TRANSACTION_HOURS_ALLOWED', 15))) {
            return false;
        }

        return true;
    }

    private function transaction($request)
    {
        try {

            DB::beginTransaction();

            $rfid = collect($request->rfid)->filter()->toArray();

            $data = Detail::leftJoinRelationship(HAS_OUTSTANDING)
                ->addSelect([
                    Outstanding::field_status_transaction(),
                    Outstanding::field_status_process(),
                ])
                ->whereIn(Detail::field_primary(), $rfid)
                ->get()->mapWithKeys(function ($item) {
                return [$item[Detail::field_primary()] => $item];
            });

            $outstanding_data = Outstanding::select([
                Outstanding::field_primary(),
                Outstanding::field_status_transaction(),
                Outstanding::field_status_process(),
                Outstanding::field_created_at(),
            ])
                ->whereIn(Outstanding::field_primary(), $request->rfid)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item[Outstanding::field_primary()] => $item];
                });

            $status_transaksi = $request->{STATUS_TRANSAKSI};
            $status_process = $request->{STATUS_PROCESS};
            $status_sync = SyncType::No;

            $return = $transaksi = $linen = $log = $outstanding = [];

            foreach ($rfid as $item) {
                $date = date('Y-m-d H:i:s');
                $user = auth()->user()->id;

                if (isset($data[$item])) {
                    $detail = $data[$item];

                    if (empty($detail->outstanding_status_transaksi) and $this->rfidCanSyncToServer($status_transaksi, $detail->field_status_linen, $detail->detail_report)) {
                        $status_sync = SyncType::Yes;

                        $beda_rs = $request->rs_id == $detail->field_rs_id ? YesNoType::NO : BooleanType::YES;

                        $data_transaksi = [
                            Transaksi::field_key() => $request->key,
                            Transaksi::field_rfid() => $item,
                            Transaksi::field_rs_ori() => $detail->field_rs_id,
                            Transaksi::field_rs_scan() => $request->rs_id,
                            Transaksi::field_beda_rs() => $beda_rs,
                            Transaksi::field_ruangan_id() => $detail->field_ruangan_id,
                            Transaksi::field_status_transaction() => $status_transaksi,
                            Transaksi::field_created_at() => $date,
                            Transaksi::field_created_by() => $user,
                            Transaksi::field_updated_at() => $date,
                            Transaksi::field_updated_by() => $user,
                        ];

                        $outstanding[] = [
                            Outstanding::field_key() => $request->key,
                            Outstanding::field_primary() => $item,
                            Outstanding::field_rs_ori() => $detail->field_rs_id,
                            Outstanding::field_rs_scan() => $request->rs_id,
                            Outstanding::field_status_beda_rs() => $beda_rs,
                            Outstanding::field_ruangan_id() => $detail->field_ruangan_id,
                            Outstanding::field_status_transaction() => $status_transaksi,
                            Outstanding::field_status_process() => $status_process,
                            Outstanding::field_created_at() => date('Y-m-d H:i:s'),
                            Outstanding::field_updated_at() => date('Y-m-d H:i:s'),
                            Outstanding::field_created_by() => auth()->user()->id,
                            Outstanding::field_updated_by() => auth()->user()->id,
                            Outstanding::field_status_hilang() => HilangType::NORMAL,
                            Outstanding::field_hilang_created_at() => null,
                            Outstanding::field_pending_created_at() => null,
                        ];

                        $transaksi[] = $data_transaksi;
                        $linen[] = (string) $item;

                        $log[] = [
                            History::field_rs_id() => $request->rs_id,
                            History::field_name() => $item,
                            History::field_status() => LogType::SCAN,
                            History::field_created_by() => auth()->user()->name,
                            History::field_created_at() => $date,
                            History::field_description() => json_encode($data_transaksi),
                        ];

                        $return[] = [
                            KEY => $request->key,
                            STATUS_SYNC => $status_sync,
                            STATUS_TRANSAKSI => $status_transaksi,
                            STATUS_PROCESS => $status_process,
                            RFID => $item,
                            TANGGAL_UPDATE => $date,
                        ];

                    } else {
                        $date = $detail->field_updated_at->format('Y-m-d H:i:s');
                        $status_sync = SyncType::No;

                        $return[] = [
                            KEY => $request->key,
                            STATUS_SYNC => $status_sync,
                            STATUS_TRANSAKSI => $detail->outstanding_status_transaksi,
                            STATUS_PROCESS => $detail->outstanding_status_proses,
                            RFID => $item,
                            TANGGAL_UPDATE => $date,
                        ];
                    }
                } else {

                    $transaction_outstanding = $outstanding_data[$item] ?? false;
                    $transaction_exists = Transaksi::select(Transaksi::field_rfid())
                        ->where(Transaksi::field_rfid(), $item)
                        ->whereDate(Transaksi::field_created_at(), date('Y-m-d'))
                        ->count();

                    // transaksi nya gak boleh dibikin di hari yang sama
                    if ($transaction_exists == 0) {

                        $transaksi[] = [

                            Transaksi::field_key() => $request->key,
                            Transaksi::field_rfid() => $item,
                            Transaksi::field_rs_ori() => null,
                            Transaksi::field_rs_scan() => $request->rs_id,
                            Transaksi::field_beda_rs() => BedaRsType::NOT_REGISTERED,
                            Transaksi::field_ruangan_id() => null,
                            Transaksi::field_status_transaction() => $status_transaksi,
                            Transaksi::field_created_at() => $date,
                            Transaksi::field_created_by() => $user,
                            Transaksi::field_updated_at() => $date,
                            Transaksi::field_updated_by() => $user,
                        ];

                    }

                    if (($transaction_outstanding == false)) { //if outstanding null

                        $outstanding[] = [

                            Outstanding::field_key() => $request->key,
                            Outstanding::field_primary() => $item,
                            Outstanding::field_rs_ori() => null,
                            Outstanding::field_rs_scan() => $request->rs_id,
                            Outstanding::field_status_beda_rs() => BedaRsType::NOT_REGISTERED,
                            Outstanding::field_ruangan_id() => null,
                            Outstanding::field_status_transaction() => $status_transaksi,
                            Outstanding::field_status_process() => $status_process,
                            Outstanding::field_created_at() => date('Y-m-d H:i:s'),
                            Outstanding::field_updated_at() => date('Y-m-d H:i:s'),
                            Outstanding::field_created_by() => auth()->user()->id,
                            Outstanding::field_updated_by() => auth()->user()->id,
                            Outstanding::field_status_hilang() => HilangType::NORMAL,
                            Outstanding::field_hilang_created_at() => null,
                            Outstanding::field_pending_created_at() => null,
                        ];
                    }

                    if (isset($outstanding_data[$item]) && ($transaction_outstanding->field_created_at == date('Y-m-d'))) {
                        $transaksi_status = $outstanding_data[$item];
                        $return[] = [
                            KEY => $request->key,
                            STATUS_SYNC => SyncType::No,
                            STATUS_TRANSAKSI => $transaksi_status->outstanding_status_transaksi,
                            STATUS_PROCESS => $transaksi_status->outstanding_status_proses,
                            RFID => $item,
                            TANGGAL_UPDATE => $date,
                        ];
                    } else {
                        $return[] = [
                            KEY => $request->key,
                            STATUS_SYNC => SyncType::Unknown,
                            STATUS_TRANSAKSI => TransactionType::UNKNOWN,
                            STATUS_PROCESS => ProcessType::UNKNOWN,
                            RFID => $item,
                            TANGGAL_UPDATE => $date,
                        ];
                    }

                }
            }

            /*
            cleansing duplicate rfid
            ketika transaksi dikirim 2x rfid
             */
            $transaksi = collect($transaksi)->unique('transaksi_rfid')->values()->all();

            if (!empty($transaksi)) {
                foreach (array_chunk($transaksi, env('TRANSACTION_CHUNK')) as $save_transaksi) {
                    Transaksi::insert($save_transaksi);
                }
            }

            if (!empty($outstanding)) {
                foreach (array_chunk($outstanding, env('TRANSACTION_CHUNK')) as $save_transaksi) {
                    Outstanding::insert($save_transaksi);
                }
            }

            if (!empty($log)) {
                foreach (array_chunk($log, env('TRANSACTION_CHUNK')) as $save_log) {
                    History::insert($save_log);
                }
            }

            Detail::whereIn(Detail::field_primary(), $linen)->update([
                Detail::field_updated_at() => date('Y-m-d H:i:s'),
                Detail::field_status_linen() => TransactionType::KOTOR,
            ]);

            $opname = Opname::where(Opname::field_status(), OpnameType::Proses)
                ->whereDate(Opname::field_start(), '>=', date('Y-m-d'))
                ->whereDate(Opname::field_end(), '<=', date('Y-m-d'))
                ->first();

            if($opname)
            {
                OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)
                    ->whereIn('opname_detail_rfid', $rfid)
                    ->where('opname_detail_ketemu', BooleanType::NO)
                    ->update([
                        OpnameDetail::field_scan_rs() => BooleanType::YES,
                        OpnameDetail::field_ketemu() => BooleanType::YES,
                        OpnameDetail::field_waktu() => date('Y-m-d H:i:s'),
                        OpnameDetail::field_sync() => BooleanType::YES,
                        OpnameDetail::field_reff() => $request->key,
                        OpnameDetail::field_scan_by() => LogType::SCAN,
                    ]);
            }

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();

            if ($th->getCode() == 23000) {
                $message = explode('for key', $th->getMessage());
                $clean = str_replace('SQLSTATE[23000]: Integrity constraint violation: 1062', 'RFID', $message[0]);
                return Notes::error($clean);
            }

            return Notes::error($th->getMessage());
        }

        $preventif = collect($return);
        if ($preventif->where('status_sync', '!=', 0)->count() == 0) {
            return Notes::error('Tidak Ada RFID yang di Sync !');
        }

        $return = $preventif->unique(RFID)->values()->all();

        return Notes::create($return);
    }
}
