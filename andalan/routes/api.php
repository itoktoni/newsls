<?php

use App\Dao\Enums\BedaRsType;
use App\Dao\Enums\BooleanType;
use App\Dao\Enums\CetakType;
use App\Dao\Enums\CuciType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\OwnershipType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Enums\YesNoType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Cetak;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\JenisBahan;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Pending;
use App\Dao\Models\Register;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Supplier;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewTotalJenis;
use App\Http\Controllers\BersihController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Requests\DetailDataRequest;
use App\Http\Requests\OpnameDetailRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\DetailResource;
use App\Http\Resources\DownloadCollection;
use App\Http\Resources\OpnameResource;
use App\Http\Resources\RsResource;
use App\Http\Resources\RsSingleResource;
use App\Http\Services\SaveOpnameService;
use App\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Plugins\Notes;
use Plugins\Query;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::post('push-subscribe', function (Request $request) {
    PushSubscription::create(['data' => $request->getContent()]);
});

Route::post('login', [UserController::class, 'postLoginApi'])->name('postLoginApi');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('download/{rsid}', function ($rsid, Request $request) {
        set_time_limit(0);
        $data = ViewDetailLinen::where(ViewDetailLinen::field_rs_id(), $rsid)->get();
        if (count($data) == 0) {
            return Notes::error('Data Tidak Ditemukan !');
        }
        $request->request->add([
            'rsid' => $rsid,
        ]);
        $resource = new DownloadCollection($data);

        return response()->streamJson($resource);

    });

    Route::get('configuration', function () {
        set_time_limit(0);
        $jenis_linen = JenisLinen::select([JenisLinen::field_primary(), JenisLinen::field_name()])->get();
        $jenis_bahan = JenisBahan::select([JenisBahan::field_primary(), JenisBahan::field_name()])->get();

        $supplier = Supplier::select([Supplier::field_primary(), Supplier::field_name()])->get();

        $getProses = ProcessType::getInstances();
        $status_proses = [];
        foreach($getProses as $key => $status){
            if($status->getValue($key) != null){
                $status_proses[] = [
                    'status_id' => $key,
                    'status_name' => $status->getValue($key)
                ];
            }
        }

        $getTransaction = TransactionType::getInstances();
        $status_transaksi = [];
        foreach($getTransaction as $key => $status){
            if($status->getValue($key) != null){
                $status_transaksi[] = [
                    'status_id' => $key,
                    'status_name' => $status->getValue($key)
                ];
            }
        }
        $getCuci = CuciType::getInstances();
        $status_cuci = [];
        foreach($getCuci as $key => $status){
            if($status->getValue($key) != null){
                $status_cuci[] = [
                    'status_id' => $key,
                    'status_name' => $status->getValue($key)
                ];
            }
        }
        $getRegister = RegisterType::getInstances();
        $status_register = [];
        foreach($getRegister as $key => $status){
            if($status->getValue($key) != null){
                $status_register[] = [
                    'status_id' => $key,
                    'status_name' => $status->getValue($key)
                ];
            }
        }

        $data = [
            'supplier' => $supplier,
            'jenis_bahan' => $jenis_bahan,
            'jenis_linen' => $jenis_linen,
            'status_proses' => $status_proses,
            'status_transaksi' => $status_transaksi,
            'status_cuci' => $status_cuci,
            'status_register' => $status_register,
        ];

        return $data;
    });

    Route::get('rs', function (Request $request) {

        $type = $request->type;

        $status_register = [];
        $status_cuci = [];
        $status_proses = [];
        $data_supplier = [];
        $data_jenis = [];
        $data_bahan = [];
        $data_jenis_rs = [];
        $ruangan = [];

        $ruangan = Ruangan::select([
            'ruangan.ruangan_id',
            'ruangan_nama',
            'rs_id',
        ])->leftJoin('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', '=', 'ruangan.ruangan_id')
        ->orderBy('ruangan_nama', 'ASC')
        ->get();

        $data_jenis_rs = JenisLinen::select([
            'jenis_linen.jenis_id',
            'jenis_linen.jenis_nama',
            'rs_id',
        ])->leftJoin('rs_dan_jenis', 'rs_dan_jenis.jenis_id', '=', 'jenis_linen.jenis_id')
        ->orderBy('jenis_nama', 'ASC')
        ->get() ?? [];

        if(empty($type) || $type == "register")
        {
            foreach (RegisterType::getInstances() as $key => $value) {
                if(!empty($value->value)){
                    $status_register[] = [
                        'status_id' => $key,
                        'status_nama' => formatCapitilizeSentance($value->description),
                    ];
                }
            }

            foreach (CuciType::getInstances() as $key => $value) {
                if(!empty($value->value)){
                    $status_cuci[] = [
                        'status_id' => $key,
                        'status_nama' => formatCapitilizeSentance($value->description),
                    ];
                }
            }

            foreach (ProcessType::getInstances() as $key => $value) {
                if(!empty($value->value)){
                    $status_proses[] = [
                        'status_id' => $key,
                        'status_nama' => formatCapitilizeSentance($value->description),
                    ];
                }
            }
        }

        $status_transaksi = [];
        foreach (TransactionType::getInstances() as $key => $value) {
            if(!empty($value->value)){
                $status_transaksi[] = [
                    'status_id' => $key,
                    'status_nama' => formatCapitilizeSentance($value->description),
                ];
            }
        }

        try {

            if(empty($type))
            {
                $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->get();
                $collection = RsResource::collection($rs);
                $data_supplier = Supplier::select(Supplier::field_primary(), Supplier::field_name())->get() ?? [];
                $data_bahan = JenisBahan::select(JenisBahan::field_primary(), JenisBahan::field_name())->get() ?? [];
                $data_jenis = JenisLinen::select(JenisLinen::field_primary(), JenisLinen::field_name())->orderBy('jenis_nama', 'ASC')->get() ?? [];
            }
            else if($type == "register")
            {
                $rs = Rs::get();
                $collection = RsSingleResource::collection($rs);

                $data_supplier = Supplier::select(Supplier::field_primary(), Supplier::field_name())->get() ?? [];
                $data_bahan = JenisBahan::select(JenisBahan::field_primary(), JenisBahan::field_name())->orderBy(JenisBahan::field_name(), 'ASC')->get() ?? [];
                $data_jenis = JenisLinen::select(JenisLinen::field_primary(), JenisLinen::field_name())->orderBy(JenisLinen::field_name(), 'ASC')->get() ?? [];
                $data_jenis_rs = JenisLinen::select([
                    'jenis_linen.jenis_id',
                    'jenis_linen.jenis_nama',
                    'rs_id',
                ])->leftJoin('rs_dan_jenis', 'rs_dan_jenis.jenis_id', '=', 'jenis_linen.jenis_id')->orderBy('jenis_nama', 'ASC')->get() ?? [];

                $ruangan = Ruangan::select([
                    'ruangan.ruangan_id',
                    'ruangan_nama',
                    'rs_id',
                ])->leftJoin('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', '=', 'ruangan.ruangan_id')
                ->orderBy('ruangan_nama', 'ASC')
                ->get();
            }
            else{

                $rs = Rs::query();

                if($type == "free")
                {
                    $data_jenis_rs = JenisLinen::select([
                        'jenis_linen.jenis_id',
                        'jenis_linen.jenis_nama',
                        'rs_id',
                    ])->leftJoin('rs_dan_jenis', 'rs_dan_jenis.jenis_id', '=', 'jenis_linen.jenis_id')
                    ->orderBy('jenis_nama', 'ASC')
                    ->get() ?? [];

                    $rs = $rs->where(Rs::field_status(), OwnershipType::FREE);
                }
                else {
                    $rs = $rs->where(Rs::field_status(), OwnershipType::DEDICATED);
                }

                $collection = RsSingleResource::collection($rs->get());

                $ruangan = Ruangan::select([
                    'ruangan.ruangan_id',
                    'ruangan_nama',
                    'rs_id',
                ])->leftJoin('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', '=', 'ruangan.ruangan_id')
                ->orderBy('ruangan_nama', 'ASC')
                ->get();
            }

            $add = [
                'status_transaksi' => $status_transaksi,
                'status_proses' => $status_proses,
                'status_cuci' => $status_cuci,
                'status_register' => $status_register,
                'bahan' => $data_bahan,
                'supplier' => $data_supplier,
                'jenis' => $data_jenis,
                'jenis_rs' => $data_jenis_rs,
                'ruangan' => $ruangan,
            ];

            $data = Notes::data($collection, $add);

            return $data;

        } catch (\Throwable $th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::get('rs_lite', function(){
        $rs = Rs::select(Rs::field_primary(), Rs::field_name())->get() ?? [];
        return Notes::data($rs);
    });

    Route::get('rs/{rsid}', function ($rsid) {

        try {

            $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->findOrFail($rsid);
            $collection = new RsResource($rs);

            return Notes::data($collection);

        } catch (\Throwable $th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::post('register', function (RegisterRequest $request) {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {

            $code = env('CODE_REGISTER', 'REG');
            $autoNumber = Query::autoNumber(Outstanding::getTableName(), Outstanding::field_key(), $code . date('ymd'), env('AUTO_NUMBER', 15));
            $rsid = $request->rs_id;
            $userId = auth()->user()->id;
            $date = date('Y-m-d H:i:s');

            if ($request->status_register == RegisterType::GANTI_CHIP) {
                $transaksi_status = TransactionType::KOTOR;
                $proses_status = ProcessType::SCAN;
            } else {
                $transaksi_status = TransactionType::REGISTER;
                $proses_status = ProcessType::REGISTER;
            }

            $config = [];

            DB::beginTransaction();

            foreach ($request->rfid as $item) {
                $merge = [
                    Detail::field_primary() => $item,
                    Detail::field_jenis_id() => $request->jenis_id,
                    Detail::field_bahan_id() => $request->bahan_id,
                    Detail::field_supplier_id() => $request->supplier_id,
                    Detail::field_status_kepemilikan() => OwnershipType::FREE,
                    Detail::field_status_linen() => $transaksi_status,
                    Detail::field_status_cuci() => $request->status_cuci,
                    Detail::field_status_register() => $request->status_register ? $request->status_register : RegisterType::REGISTER,
                    Detail::field_created_at() => $date,
                    Detail::field_updated_at() => $date,
                    Detail::field_created_by() => $userId,
                    Detail::field_updated_by() => $userId,
                ];

                $data_outstanding = [
                    Outstanding::field_key() => $autoNumber,
                    Outstanding::field_primary() => $item,
                    Outstanding::field_status_transaction() => $transaksi_status,
                    Outstanding::field_status_process() => $proses_status,
                    Outstanding::field_created_at() => $date,
                    Outstanding::field_updated_at() => $date,
                    Outstanding::field_created_by() => $userId,
                    Outstanding::field_updated_by() => $userId,
                    Outstanding::field_status_hilang() => HilangType::NORMAL,
                    Outstanding::field_hilang_created_at() => null,
                    Outstanding::field_pending_created_at() => null,
                ];

                $data_transaksi = [
                    Transaksi::field_key() => $autoNumber,
                    Transaksi::field_rfid() => $item,
                    Transaksi::field_rs_ori() => $rsid,
                    Transaksi::field_rs_scan() => $rsid,
                    Transaksi::field_beda_rs() => BedaRsType::NO,
                    Transaksi::field_status_transaction() => $transaksi_status,
                    Transaksi::field_created_at() => $date,
                    Transaksi::field_created_by() => $userId,
                    Transaksi::field_updated_at() => $date,
                    Transaksi::field_updated_by() => $userId,
                ];

                if ($request->has('ruangan_id')) {
                    $merge = array_merge($merge, [
                        Detail::field_status_kepemilikan() => OwnershipType::DEDICATED,
                        Detail::field_ruangan_id() => $request->ruangan_id,
                        Detail::field_rs_id() => $rsid,
                    ]);

                    $data_outstanding = array_merge($data_outstanding, [
                        Outstanding::field_rs_ori() => $rsid,
                        Outstanding::field_rs_scan() => $rsid,
                        Outstanding::field_ruangan_id() => $request->ruangan_id,
                    ]);

                    $data_transaksi = array_merge($data_transaksi, [
                        Transaksi::field_ruangan_id() => $request->ruangan_id,
                    ]);

                    //ConfigLinen::updateOrCreate(
                    ConfigLinen::create(
                        [
                            ConfigLinen::field_primary() => $item,
                            ConfigLinen::field_rs_id() => $rsid,
                        ],
                        [
                            ConfigLinen::field_primary() => $item,
                            ConfigLinen::field_rs_id() => $rsid,
                        ]
                    );

                } else {
                    foreach ($rsid as $id_rs) {
                        //ConfigLinen::updateOrCreate(
                        ConfigLinen::create(
                            [
                                ConfigLinen::field_primary() => $item,
                                ConfigLinen::field_rs_id() => $id_rs,
                            ],
                            [
                                ConfigLinen::field_primary() => $item,
                                ConfigLinen::field_rs_id() => $id_rs,
                            ]
                        );
                    }
                }

                $detail[] = $merge;
                $transaksi[] = $data_transaksi;
                $outstanding[] = $data_outstanding;
            }

            Detail::insert($detail);
            Outstanding::upsert($outstanding, Outstanding::field_primary());

            if($request->status_register == RegisterType::GANTI_CHIP){
                Transaksi::insert($transaksi);

                $history = collect($request->rfid)->map(function ($item, $rsid) {
                    return [
                        ModelsHistory::field_rs_id() => $rsid,
                        ModelsHistory::field_name() => $item,
                        ModelsHistory::field_status() => LogType::REG_TRANSACTION,
                        ModelsHistory::field_created_by() => auth()->user()->name,
                        ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                        ModelsHistory::field_description() => json_encode([ModelsHistory::field_name() => $item]),
                    ];
                });
            }

            else{
                $history = collect($request->rfid)->map(function ($item, $rsid) {
                    return [
                        ModelsHistory::field_rs_id() => $rsid,
                        ModelsHistory::field_name() => $item,
                        ModelsHistory::field_status() => LogType::REGISTER,
                        ModelsHistory::field_created_by() => auth()->user()->name,
                        ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                        ModelsHistory::field_description() => json_encode([ModelsHistory::field_name() => $item]),
                    ];
                });
            }

            ModelsHistory::insert($history->toArray());

            DB::commit();

            $return = ViewDetailLinen::whereIn(ViewDetailLinen::field_primary(), $request->rfid)->get();

            return Notes::data(DetailResource::collection($return));

        } catch (\Illuminate\Database\QueryException $th) {
            DB::rollBack();

            if ($th->getCode() == 23000) {
                $message = explode('for key', $th->getMessage());
                $clean = str_replace('SQLSTATE[23000]: Integrity constraint violation: 1062','RFID', $message[0]);
                return Notes::error($clean);
            }

            return Notes::error($request->all(), $th->getMessage());
        } catch (\Throwable $th) {
            DB::rollBack();

            if($th->getCode() == 23000){
                $message = explode('for key', $th->getMessage());
                $clean = str_replace('SQLSTATE[23000]: Integrity constraint violation: 1062','RFID', $message[0]);
                return Notes::error($clean);
            }

            return Notes::error($request->all(), $th->getMessage());
        }

    });

    Route::post('detail/rfid', function (DetailDataRequest $request) {
        try {

            $item = Query::getDetail()
                ->leftJoinRelationship(HAS_OUTSTANDING)
                ->whereIn(Detail::field_primary(), $request->rfid)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item[Detail::field_primary()] => $item];
                }) ?? [];

                $kosong = [
                    'rfid' => null,
                    'jenis_id' => null,
                    'jenis_nama' => null,
                    'bahan_id' => null,
                    'bahan_nama' => null,
                    'supplier_id' => null,
                    'supplier_nama' => null,
                    'rs_id' => null,
                    'rs_nama' => null,
                    'ruangan_id' => null,
                    'ruangan_nama' => null,
                    'status_register' => null,
                    'status_cuci' => null,
                    'status_transaksi' => null,
                    'status_proses' => null,
                    'tanggal_create' => null,
                    'tanggal_update' => null,
                    'pemakaian' => null,
                    'user_nama' => null
                ];

            foreach($request->rfid as $code){

                if(isset($item[$code])){
                    $data = $item[$code];

                    $collection[] = [
                        'rfid' => $code,
                        'jenis_id' => $data->detail_rfid,
                        'jenis_nama' => $data->jenis_nama ?? '',
                        'bahan_id' => $data->detail_id_bahan,
                        'bahan_nama' => $data->bahan_nama ?? '',
                        'supplier_id' => $data->supplier_id,
                        'supplier_nama' => $data->supplier_nama ?? '',
                        'rs_id' => $data->detail_id_rs ?? '',
                        'rs_nama' => $data->rs_nama ?? '',
                        'ruangan_id' => $data->detail_id_ruangan,
                        'ruangan_nama' => $data->ruangan_nama ?? '',
                        'status_register' => $data->detail_status_register,
                        'status_cuci' => $data->detail_status_cuci,
                        'status_transaksi' => $data->outstanding_status_transaksi ?? TransactionType::BERSIH,
                        'status_proses' => $data->outstanding_status_proses ?? TransactionType::BERSIH,
                        'tanggal_create' => $data->detail_created_at ? Carbon::make($data->detail_created_at)->format('Y-m-d') : null,
                        'tanggal_update' => $data->detail_updated_at ? Carbon::make($data->detail_updated_at)->format('Y-m-d') : null,
                        'pemakaian' => $data->detail_total_bersih ?? 0,
                        'user_nama' => $data->name ?? null,
                    ];
                }
                else{
                    $collection[] = array_merge($kosong, [
                        'rfid' => $code
                    ]);
                }
            }

            return Notes::data($collection);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error($th->getMessage());
        } catch (\Throwable $th) {
            return Notes::error($th->getMessage());
        }
    });

    Route::post('kotor', [TransaksiController::class, 'kotor']);
    Route::post('retur', [TransaksiController::class, 'retur']);
    Route::post('rewash', [TransaksiController::class, 'rewash']);

    Route::get('grouping/{rfid}', function ($rfid) {
        try {

            $flag = 'Normal';

            $date = date('Y-m-d H:i:s');
            $user = auth()->user()->id;

            DB::beginTransaction();

            $detail = Detail::with([HAS_OUTSTANDING, HAS_VIEW])->findOrFail($rfid);
            $view = $detail->has_view;

            ModelsHistory::create([
                ModelsHistory::field_rs_id() => $detail->field_primary,
                ModelsHistory::field_name() => $rfid,
                ModelsHistory::field_status() => LogType::QC,
                ModelsHistory::field_created_by() => auth()->user()->name,
                ModelsHistory::field_created_at() => $date,
                ModelsHistory::field_description() => json_encode([ModelsHistory::field_name() => $rfid]),
            ]);

            $code = env('CODE_KOTOR', 'KTR');
            if($detail->field_status_linen == TransactionType::REGISTER){
                $code = env('CODE_REGISTER', 'REG');
            }
            else if($detail->field_status_linen == TransactionType::REJECT){
                $code = env('CODE_REJECT', 'RJK');
            }
            else if($detail->field_status_linen == TransactionType::REWASH){
                $code = env('CODE_REWASH', 'WSH');
            }
            $autoNumber = Query::autoNumber(Outstanding::getTableName(), Outstanding::field_key(), $code . date('ymd'), env('AUTO_NUMBER', 15));

            // CHECK OUTSTANDING DATA
            $data_outstanding = [
                Outstanding::field_primary() => $rfid,
                Outstanding::field_status_process() => ProcessType::QC,
                Outstanding::field_updated_at() => $date,
                Outstanding::field_updated_by() => $user,
                Outstanding::field_rs_ori() => $detail->field_rs_id,
                Outstanding::field_rs_scan() => $detail->field_rs_id,
                Outstanding::field_ruangan_id() => $detail->field_ruangan_id,
                Outstanding::field_status_hilang() => HilangType::NORMAL,
                Outstanding::field_hilang_created_at() => null,
                Outstanding::field_pending_created_at() => null,
            ];

            if ($detail->field_status_kepemilikan == OwnershipType::FREE)
            {
                $data_outstanding = array_merge($data_outstanding, [
                    Outstanding::field_rs_ori() => null,
                    Outstanding::field_ruangan_id() => null,
                ]);
            }

            $outstanding = $detail->has_outstanding;
            if ($outstanding)
            {
                $outstanding->update($data_outstanding);
            }
            else
            {
                if((!empty($detail->field_report) && $detail->field_report->format('Y-m-d') != date('Y-m-d')))
                {
                    $flag = 'KOTOR';
                    $transaksi_status = TransactionType::KOTOR;

                    if($detail->field_status_linen == TransactionType::REGISTER)
                    {
                        $flag = 'REGISTER';
                        $transaksi_status = TransactionType::REGISTER;

                        Transaksi::create([
                            Transaksi::field_key() => $autoNumber,
                            Transaksi::field_rfid() => $rfid,
                            Transaksi::field_rs_ori() => $detail->detail_id_rs,
                            Transaksi::field_rs_scan() => $detail->detail_id_rs,
                            Transaksi::field_beda_rs() => BedaRsType::NO,
                            Transaksi::field_ruangan_id() => $detail->detail_id_ruangan,
                            Transaksi::field_status_transaction() => $transaksi_status,
                            Transaksi::field_created_by() => $user,
                            Transaksi::field_updated_at() => $date,
                            Transaksi::field_updated_by() => $user,
                        ]);

                    } else {
                        // CHECK TRANSACTION DATA IF NOT PRESENT

                        $transaksi = Transaksi::where(Transaksi::field_rfid(), $rfid)
                            ->whereDate(Transaksi::field_created_at(), date('Y-m-d'))
                            ->count();

                        if($transaksi == 0)
                        {
                            ModelsHistory::create([
                                ModelsHistory::field_rs_id() => $detail->field_primary,
                                ModelsHistory::field_name() => $rfid,
                                ModelsHistory::field_status() => LogType::QC_TRANSACTION,
                                ModelsHistory::field_created_by() => auth()->user()->name,
                                ModelsHistory::field_created_at() => $date,
                                ModelsHistory::field_description() => json_encode([ModelsHistory::field_name() => $rfid]),
                            ]);

                            Transaksi::create([
                                Transaksi::field_key() => $autoNumber,
                                Transaksi::field_rfid() => $rfid,
                                Transaksi::field_rs_ori() => $detail->detail_id_rs,
                                Transaksi::field_rs_scan() => $detail->detail_id_rs,
                                Transaksi::field_beda_rs() => BedaRsType::NO,
                                Transaksi::field_ruangan_id() => $detail->detail_id_ruangan,
                                Transaksi::field_status_transaction() => TransactionType::KOTOR,
                                Transaksi::field_created_at() => $date,
                                Transaksi::field_created_by() => $user,
                                Transaksi::field_updated_at() => $date,
                                Transaksi::field_updated_by() => $user,
                            ]);
                        }

                        $detail->update([
                            Detail::field_status_linen() => $transaksi_status,
                        ]);
                    }

                    $outstanding = Outstanding::create(array_merge($data_outstanding, [
                        Outstanding::field_key() => $autoNumber,
                        Outstanding::field_status_transaction() => $transaksi_status,
                        Outstanding::field_created_at() => $date,
                        Outstanding::field_created_by() => $user,
                    ]));

                    Pending::where('pending_transaksi', '!=', TransactionType::BERSIH)
                    ->where('pending_rfid', $rfid)->update([
                        'pending_updated_at' => date('Y-m-d H:i:s'),
                        'pending_transaksi' => $transaksi_status,
                        'pending_proses' => ProcessType::QC,
                    ]);

                }
                else
                {
                    if($detail->field_status_linen == TransactionType::REGISTER)
                    {
                        $outstanding = Outstanding::create(array_merge($data_outstanding, [
                            Outstanding::field_key() => $autoNumber,
                            Outstanding::field_status_transaction() => TransactionType::REGISTER,
                            Outstanding::field_created_at() => $detail->detail_created_at->format('Y-m-d H:i:s') ?? $date,
                            Outstanding::field_created_by() => $detail->detail_updated_at->format('Y-m-d H:i:s') ?? $user,
                        ]));
                    }

                }
            }

            $collection = [
                'rfid' => $rfid ?? '',
                'linen_id' => $view->view_linen_id ?? '',
                'linen_nama' => $view->view_linen_nama ?? '',
                'rs_id' => $view->view_rs_id ?? '',
                'rs_nama' => $view->view_rs_nama ?? '',
                'ruangan_id' => $view->view_ruangan_id ?? '',
                'ruangan_nama' => $view->view_ruangan_nama ?? '',
                'status_transaksi' => $outstanding->outstanding_status_transaksi ?? '',
                'status_proses' => $outstanding->outstanding_status_proses ?? '',
                'status_kepemilikan' => $detail->detail_status_kepemilikan ?? null,
                'tanggal_create' => !empty($outstanding->outstanding_created_at) ? Carbon::make($outstanding->outstanding_created_at)->format('Y-m-d') : null,
                'tanggal_update' => !empty($outstanding->outstanding_updated_at) ? Carbon::make($outstanding->outstanding_updated_at)->format('Y-m-d') : null,
                'user_nama' => $view->view_created_name ?? null,
                'status_linen' => $flag ?? null,
            ];

            $detail->update([
                Detail::field_status_linen() => $detail->field_status_linen,
            ]);

            $opname = Opname::where(Opname::field_status(), OpnameType::Proses)
                ->first();

            if($opname)
            {
                //ketika grouping update at nya di update
                OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)
                    ->where('opname_detail_rfid', $rfid)
                    ->where('opname_detail_ketemu', BooleanType::NO)
                    ->update([
                        OpnameDetail::field_scan_rs() => BooleanType::YES,
                        OpnameDetail::field_ketemu() => BooleanType::YES,
                        OpnameDetail::field_waktu() => date('Y-m-d H:i:s'),
                        OpnameDetail::field_sync() => BooleanType::YES,
                        OpnameDetail::field_reff() => $outstanding->outstanding_key,
                        OpnameDetail::field_scan_by() => LogType::QC,
                    ]);
            }

            DB::commit();

            return $collection;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            DB::rollBack();

            return Notes::error($rfid, 'RFID ' . $rfid . ' tidak ditemukan');
        } catch (\Throwable $th) {

            DB::rollBack();

            if($th->getCode() == 23000){
                $message = explode('for key', $th->getMessage());
                $clean = str_replace('SQLSTATE[23000]: Integrity constraint violation: 1062','RFID', $message[0]);
                return Notes::error($clean);
            }

            return Notes::error($rfid, $th->getMessage());
        }
    });

    Route::post('packing', [BersihController::class, 'packing']);
    Route::get('packing/{code}', [DeliveryController::class, 'printPacking']);

    Route::get('list/packing/{rsid}', function ($rsid) {
        $data = Cetak::select([Cetak::field_name()])
            ->where(Cetak::field_rs_id(), $rsid)
            ->where(Cetak::field_type(), CetakType::Barcode)
            ->where(Cetak::field_date(), '>=', now()->addDay(-30))
            ->orderBy(Cetak::field_primary(), 'DESC')
            ->get();

        return Notes::data($data);
    });

    Route::post('delivery', [BersihController::class, 'delivery']);
    Route::get('delivery/{code}', [DeliveryController::class, 'printDelivery']);

    Route::get('list/delivery/{rsid}', function ($rsid) {
        $data = Cetak::select([Cetak::field_name()])
            ->where(Cetak::field_rs_id(), $rsid)
            ->where(Cetak::field_type(), CetakType::Delivery)
            ->where(Cetak::field_date(), '>=', now()->addDay(-30))
            ->orderBy(Cetak::field_primary(), 'DESC')
            ;

        if (request()->get('tgl')) {
            $data->where(Cetak::field_date(), '=', request()->get('tgl'));
        }

        return Notes::data($data->get());
    });




    Route::get('total/delivery/{rsid}/{status}', function ($rsid, $status) {
        $data = DB::table('view_total_bersih_dedicated')
            ->where('view_rs_id', $rsid)
            ->where('view_status', $status)
            ->first();

        return Notes::data(['total' => $data->view_total ?? 0]);
    });

    Route::get('total/outstanding/{rsid}/{ruangan}/{jenis}/{transaksi}', function ($rsid, $ruangan, $jenis, $transaksi) {

        $kosong = [
            'view_jenis_id' => 0,
            'view_ruangan_id' => 0,
            'view_rs_id' => 0,
            'view_total' => 0,
            'view_status' => "",
        ];

        if($transaksi == TransactionType::BERSIH)
        {
            $transaksi = TransactionType::KOTOR;
        }

        $data = DB::table('view_total_transaksi')
            ->where('view_rs_id', $rsid)
            ->where('view_jenis_id', $jenis)
            ->where('view_ruangan_id', $ruangan)
            ->where('view_status', $transaksi)
            ->first() ?? $kosong;

        return Notes::data($data);
    });


    Route::get('total/bersih/{rsid}/{ruangan}/{jenis}/{transaksi}', function ($rsid, $ruangan, $jenis, $transaksi) {

        $kosong = [
            'view_jenis_id' => 0,
            'view_ruangan_id' => 0,
            'view_rs_id' => 0,
            'view_total' => 0,
            'view_status' => "",
        ];

        $data = DB::table('view_total_bersih_free')
            ->where('view_rs_id', $rsid)
            ->where('view_jenis_id', $jenis)
            ->where('view_ruangan_id', $ruangan)
            ->where('view_status', $transaksi)
            ->first() ?? $kosong;

        return Notes::data($data);
    });



    Route::get('opname', function (Request $request) {
        try {
            $today = today()->format('Y-m-d');
            $data = Opname::with([HAS_RS])
                ->where(Opname::field_status(), OpnameType::Proses)
                ->where(Opname::field_start(), '<=', $today)
                ->where(Opname::field_end(), '>=', $today)
                ->get();

            $collection = OpnameResource::collection($data);

            return Notes::data($collection);

        } catch (\Throwable $th) {
            return Notes::error($th->getCode(), $th->getMessage());
        }
    })->name('opname_data');

    Route::get('opname/{id}', function ($id, Request $request) {
        try {
            $data = Opname::with([HAS_RS])->find($id);

            $collection = new OpnameResource($data);

            return Notes::data($collection);

        } catch (\Throwable $th) {
            return Notes::error($th->getCode(), $th->getMessage());
        }
    })->name('opname_detail');

    Route::post('/opname', function (OpnameDetailRequest $request, SaveOpnameService $service) {
        $data = $service->save($request->{Opname::field_primary()}, $request->data);

        return $data;
    });
});