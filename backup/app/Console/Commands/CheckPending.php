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
use Illuminate\Support\Facades\DB;
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
        // Kunci antar-proses via MySQL GET_LOCK (tanpa migration / tanpa ubah tabel).
        // Alasan: CACHE_DRIVER=array membuat Cache::lock() tidak berlaku antar cron,
        // sedangkan GET_LOCK hidup di koneksi MySQL sehingga 2 cron tidak bisa masuk bersamaan.
        $gotLock = (int) (DB::selectOne("SELECT GET_LOCK('check:pending', 5) AS l")->l ?? 0);
        if ($gotLock !== 1) {
            $this->info('Another check:pending process is still running. Skipping.');

            return;
        }

        try {
            DB::transaction(function () {
                $outstanding = Outstanding::query()
                    ->select(Outstanding::getTableName().'.*', 'detail_id_jenis')
                    ->leftJoinRelationship('has_rfid')
                    ->whereNotNull('outstanding_rs_ori')
                    ->whereDate(Outstanding::field_updated_at(), '<=', Carbon::now()->subMinutes(1440)->toDateString())
                    ->where(Outstanding::field_status_hilang(), HilangType::NORMAL)
                    ->orderBy(Outstanding::field_updated_at())
                    ->limit(2)
                    ->lockForUpdate()
                    ->get();

                if ($outstanding->isEmpty()) {
                    return;
                }

                $rfid = $outstanding->pluck(Outstanding::field_primary())->values();

                // Pengecekan murni PHP: cek SEMUA rfid dalam batch terhadap pending aktif.
                // Di dalam GET_LOCK + transaksi, hasil cek ini valid (tidak ada cron lain yang menyela).
                $existing = Pending::whereIn('pending_rfid', $rfid)
                    ->whereNull('pending_bersih_at')
                    ->pluck('pending_rfid')
                    ->toArray();

                $toProcess = $outstanding->whereNotIn(Outstanding::field_primary(), $existing);

                // RFID yang sudah punya pending aktif: tetap tandai PENDING di outstanding
                // supaya tidak diambil lagi menit berikutnya, tapi jangan insert/history ulang
                if ($toProcess->isEmpty()) {
                    Outstanding::whereIn(Outstanding::field_primary(), $rfid)->update([
                        Outstanding::field_status_hilang() => HilangType::PENDING,
                        Outstanding::field_pending_created_at() => date('Y-m-d H:i:s'),
                        Outstanding::field_pending_updated_at() => date('Y-m-d H:i:s'),
                    ]);

                    return;
                }

                $newRfid = $toProcess->pluck(Outstanding::field_primary())->values();

                $now = now()->format('Y-m-d H:i:s');

                $insert = [];
                foreach ($toProcess as $pending) {
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

                if (! empty($insert)) {
                    Pending::insert($insert);
                }

                PluginsHistory::bulk($newRfid, LogType::PENDING, 'RFID Pending');

                // Update semua yang diambil batch ini supaya tidak diproses ulang cron berikutnya
                Outstanding::whereIn(Outstanding::field_primary(), $rfid)->update([
                    Outstanding::field_status_hilang() => HilangType::PENDING,
                    Outstanding::field_pending_created_at() => date('Y-m-d H:i:s'),
                    Outstanding::field_pending_updated_at() => date('Y-m-d H:i:s'),
                ]);
            });
        } finally {
            DB::selectOne("SELECT RELEASE_LOCK('check:pending')");
        }

        $this->info('The system has been check successfully!');
    }
}
