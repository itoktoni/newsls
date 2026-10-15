<?php

namespace App\Console\Commands;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\Mutasi;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewMutasi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DummyOpname extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:opname';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Commands To copy web frontend to vendor console';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last = Detail::where(Detail::field_rs_id(), 75)->orderByRaw('cast(detail_rfid as unsigned) desc')->first()->field_primary ?? 0;
        $date = now()->format('Y-m-d H:i:s');

        if(Opname::where(Opname::field_rs_id(), 75)->where(Opname::field_primary(), 100)->count() == 0){
            $opname = Opname::create([
                Opname::field_primary() => 100,
                Opname::field_rs_id() => 75,
                Opname::field_name() => 'Dummy Opname',
                Opname::field_start() => $date,
                Opname::field_end() => now()->addDay(5)->format('Y-m-d H:i:s'),
                Opname::field_status() => OpnameType::Proses,
            ]);
        }

        if($last < 100000)
        {
            $counter = intval($last) + 1;

            foreach (range($counter, $counter + 1000) as $item) {
                $config[] = [
                    Rs::field_primary() => 75,
                    Detail::field_primary() => $item,
                ];

                $data[] = [
                    Detail::field_primary() => $item,
                    Detail::field_rs_id() => 75,
                    Detail::field_ruangan_id() => 250,
                    Detail::field_jenis_id() => 333,
                    Detail::field_bahan_id() => 1,
                    Detail::field_supplier_id() => 1,
                    Detail::field_created_by() => 101,
                    Detail::field_updated_by() => 101,
                    Detail::field_created_at() => $date,
                    Detail::field_updated_at() => $date,
                    Detail::field_status_cuci() => 'RENTAL',
                    Detail::field_status_register() => 'REGISTER',
                    Detail::field_status_kepemilikan() => 'DEDICATED',
                    Detail::field_status_linen() => 'BERSIH',
                ];

                $opname[] = [
                    OpnameDetail::field_rfid() => $item,
                    OpnameDetail::field_opname() => 100,
                    OpnameDetail::field_code() => 'DUMMY',
                    OpnameDetail::field_waktu() => $date,
                    OpnameDetail::field_register() => BooleanType::NO,
                    OpnameDetail::field_transaksi() => TransactionType::BERSIH,
                    OpnameDetail::field_proses() => ProcessType::UNKNOWN,
                    OpnameDetail::field_status_hilang() => HilangType::NORMAL,
                    OpnameDetail::field_ketemu() => BooleanType::YES,
                    OpnameDetail::field_scan_rs() => 75,
                    OpnameDetail::field_created_at() => $date,
                    OpnameDetail::field_updated_at() => $date,
                    OpnameDetail::field_created_by() => 101,
                    OpnameDetail::field_updated_by() => 101,
                ];
            }

            DB::transaction(function () use ($config, $data, $opname) {
                ConfigLinen::insert($config);
                Detail::insert($data);
                OpnameDetail::insert($opname);
            });
        }

        //INSERT INTO `andalan`.`detail_linen` (`detail_rfid`, `detail_id_rs`, `detail_id_ruangan`, `detail_id_jenis`, `detail_id_bahan`, `detail_id_supplier`, `detail_created_by`, `detail_updated_by`, `detail_created_at`, `detail_updated_at`, `detail_status_cuci`, `detail_status_register`, `detail_status_kepemilikan`, `detail_status_linen`) VALUES ('1', 75, 250, 333, 1, 1, 101, 101, '2025-03-22 22:44:19', '2025-03-22 22:44:19', 'RENTAL', 'REGISTER', 'DEDICATED', 'BERSIH')

        $this->info('The system has been check successfully!');
    }
}