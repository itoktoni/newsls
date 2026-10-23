<?php

namespace App\Console\Commands;

use App\Dao\Models\Logs;
use App\Dao\Models\Mutasi;
use App\Dao\Models\ViewMutasi;
use Illuminate\Console\Command;

class LogSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mutasi:summary';

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
        $tanggal = now()->format('Y-m-d');
        $backdate = now()->addDay(-1)->format('Y-m-d');

        $data = Logs::where(function($query) use($tanggal, $backdate) {
            $query->where(Logs::field_check(), '!=', $tanggal)
                ->where(Logs::field_tanggal(), $backdate)
                ->whereNull(Logs::field_out());
        })->orWhere(function($query) use($backdate) {
            $query->whereNull(Logs::field_check())
                ->where(Logs::field_tanggal(), $backdate)
                ->whereNull(Logs::field_out());
        })->get();

        if(!empty($data))
        {
            $insert = [];
            foreach($data as $log)
            {
                $insert[] = [
                    Logs::field_rfid() => $log->field_rfid,
                    Logs::field_rs() => $log->field_rs,
                    Logs::field_jenis() => $log->field_jenis,
                    Logs::field_ruangan() => $log->field_ruangan,
                    Logs::field_in() => $log->field_in,
                    Logs::field_user() => USER_SYSTEM,
                    Logs::field_tanggal() => $tanggal,
                ];
            }

            Logs::insert($insert);
        }

        $this->info('The system has been check successfully!');
    }
}
