<?php

namespace App\Console\Commands;

use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use Illuminate\Console\Command;
use Plugins\History;

class StopOpname extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opname:stop';

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
        $data = Opname::where(Opname::field_end(), '<', date('y-m-d'))->get();
        if ($data->count() > 0) {
            $pluck = $data->pluck(Opname::field_primary(), Opname::field_primary());

            Opname::whereIn(Opname::field_primary(), $pluck)
                ->update([
                    Opname::field_status() => OpnameType::Selesai,
                ]);

            $rfid = OpnameDetail::select(OpnameDetail::field_rfid())
                ->whereIn(OpnameDetail::field_opname(), $pluck)
                ->get()
                ->pluck(OpnameDetail::field_rfid(), OpnameDetail::field_rfid());

            History::bulk($rfid, ProcessType::OpnameSelesai, 'Opname selesai');
        }

        $this->info('The system has been check successfully!');
    }
}
