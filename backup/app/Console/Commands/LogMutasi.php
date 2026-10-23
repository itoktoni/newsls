<?php

namespace App\Console\Commands;

use App\Dao\Models\Mutasi;
use App\Dao\Models\ViewMutasi;
use Illuminate\Console\Command;

class LogMutasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mutasi:log';

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
        $tanggal = now()->addDay(-2)->format('Y-m-d');

        if (Mutasi::where(Mutasi::field_tanggal(), $tanggal)->count() == 0) {

            $data_mutasi = ViewMutasi::get()->map(function ($item) use ($tanggal) {

                $register = $item->total_stock;
                $mutasi = $item->total_bersih + $item->total_kotor;

                $saldo_akhir = $register - $mutasi;
                $saldo_awal = $item->mutasi_saldo_awal;

                $selisih_plus = $selisih_minus = null;
                if ($saldo_akhir < 0) {
                    $selisih_minus = $saldo_akhir;
                } else {
                    $selisih_plus = $saldo_akhir;
                }

                $check = Mutasi::count();
                if ($check > 0) {
                    $saldo_awal = $item->mutasi_saldo_akhir;
                    $saldo_akhir = $saldo_awal - $mutasi;
                }

                return [
                    'mutasi_nama' => $item->rs_nama.' '.$item->jenis_nama.' '.$tanggal,
                    'mutasi_tanggal' => $tanggal,
                    'mutasi_rs_id' => $item->rs_id,
                    'mutasi_rs_nama' => $item->rs_nama,
                    'mutasi_linen_id' => $item->jenis_id,
                    'mutasi_linen_nama' => $item->jenis_nama,
                    'mutasi_register' => $item->total_stock,
                    'mutasi_kotor' => $item->total_kotor,
                    'mutasi_bersih' => $item->total_bersih,
                    'mutasi_minus' => $selisih_minus,
                    'mutasi_plus' => $selisih_plus,
                    'mutasi_saldo_awal' => $saldo_awal,
                    'mutasi_saldo_akhir' => $saldo_akhir,
                    'mutasi_date' => date('Y-m-d h:i:s'),
                ];
            });

            Mutasi::insert($data_mutasi->toArray());

        }

        $this->info('The system has been check successfully!');
    }
}
