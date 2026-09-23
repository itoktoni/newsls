<?php

namespace App\Console\Commands;

use App\Dao\Enums\HilangType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Pending;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Plugins\History as PluginsHistory;

class CheckPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Commands check is there any pending rfid';

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
        $outstanding = Outstanding::query()
            ->select(Outstanding::getTableName().'.*', 'detail_id_jenis')
            ->leftJoinRelationship('has_rfid')
            ->whereNotNull('outstanding_rs_ori')
            ->whereDate(Outstanding::field_updated_at(), '<=', Carbon::now()->subMinutes(1440)->toDateString())
            // ->whereDate(Outstanding::field_updated_at(), '<', Carbon::now()->toDateString())
            ->where(Outstanding::field_status_hilang(), HilangType::NORMAL)
            ->limit(2)
            ->get();

        if ($outstanding) {

            $rfid = $outstanding->pluck(Outstanding::field_primary());

            PluginsHistory::bulk($rfid, LogType::PENDING, 'RFID Pending');

            $insert = [];

            $now = now()->format('Y-m-d H:i:s');

            foreach($outstanding as $pending)
            {
                $user_id = $pending->outstanding_created_by;
                $insert[] = [
                    'pending_rfid' => $pending->outstanding_rfid,
                    'pending_key' => $pending->outstanding_key,
                    'pending_id_rs' => $pending->outstanding_rs_ori,
                    'pending_id_ruangan' => $pending->outstanding_id_ruangan,
                    'pending_id_jenis' => $pending->detail_id_jenis,
                    'pending_created_at' => $now,
                    'pending_updated_at' => $now,
                    'pending_kotor_at' => $pending->outstanding_created_at,
                    'pending_transaksi' => $pending->outstanding_status_transaksi,
                    'pending_proses' => $pending->outstanding_status_proses,
                    'pending_created_by' => $user_id,
                    'pending_updated_by' => $user_id,
                    'pending_kotor_by' => $user_id,
                    'pending_status' => LogType::PENDING,
                ];
            }

            $cek_ada = Pending::where('pending_rfid', $pending->outstanding_rfid)->whereNull('pending_bersih_at')->count();
            if($cek_ada == 0)
            {
                Pending::insert($insert);
            }

            Outstanding::whereIn(Outstanding::field_primary(), $rfid)->update([
                Outstanding::field_status_hilang() => HilangType::PENDING,
                Outstanding::field_pending_created_at() => date('Y-m-d H:i:s'),
                Outstanding::field_pending_updated_at() => date('Y-m-d H:i:s'),
            ]);
        }

        $this->info('The system has been check successfully!');
    }
}
