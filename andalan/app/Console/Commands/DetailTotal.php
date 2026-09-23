<?php

namespace App\Console\Commands;

use App\Dao\Models\Detail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetailTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detail:total';

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
        $data = DB::table('view_job_counting')
            ->where('detail_tgl_cek', '!=', date('Y-m-d'))
            ->orWhereNull('detail_tgl_cek')
            ->limit(ENV('TRANSACTION_DETAIL_CEK', 50))
            ->get();

        Log::info($data);

        if ($data) {
            foreach ($data as $item) {
                Detail::where(Detail::field_primary(), $item->detail_rfid)->update([
                    Detail::field_total_bersih() => $item->qty_bersih_kotor,
                    Detail::field_total_reject() => $item->qty_bersih_retur,
                    Detail::field_total_rewash() => $item->qty_bersih_rewash,
                    Detail::field_cek() => date('Y-m-d'),
                ]);
            }
        }

        $this->info('The system has been check successfully!');
    }
}
