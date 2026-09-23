<?php

namespace App\Console\Commands;

use App\Dao\Enums\HilangType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Plugins\History;

class CheckHilang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:hilang';

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

        $outstanding = Outstanding::query()
            ->select(Outstanding::field_primary())
            ->whereDate(Outstanding::field_updated_at(), '<=', Carbon::now()->subMinutes(4320)->toDateString())
            ->where(Outstanding::field_status_hilang(), '!=', HilangType::HILANG)
            ->get();

        if ($outstanding) {

            $rfid = $outstanding->pluck(Outstanding::field_primary());

            History::bulk($rfid, LogType::HILANG, 'RFID HILANG');

            Outstanding::whereIn(Outstanding::field_primary(), $rfid)->update([
                Outstanding::field_pending_created_at() => null,
                Outstanding::field_pending_updated_at() => null,
                Outstanding::field_status_hilang() => HilangType::HILANG,
                Outstanding::field_hilang_updated_at() => date('Y-m-d H:i:s'),
                Outstanding::field_hilang_created_at() => date('Y-m-d H:i:s'),
            ]);
        }

        $this->info('The system has been check successfully!');
    }
}
