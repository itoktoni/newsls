<?php

namespace App\Http\Controllers;

use Alkhachatryan\LaravelWebConsole\LaravelWebConsole;
use App\Charts\DashboardBersihHarian;
use App\Charts\DashboardKotorHarian;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Pending;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewOutstandingHilang;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (auth()->check()) {
            return redirect()->route('login');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(DashboardKotorHarian $sebaran, DashboardBersihHarian $perbandingan)
    {
        if (auth()->check() && auth()->user()->active == false) {
            return redirect()->route('login');
        }

        if (auth()->check() && auth()->user()->active == false) {
            return redirect()->route('login');
        }

        $bersih = 0;
        $kotor = 0;
        $reject = 0;
        $rewash = 0;

        $date = date('Y-m-d');

        $rs_id = auth()->user()->rs_id;

        $bersih = Bersih::select(Bersih::field_rfid())
            ->where(Bersih::field_report(), $date)
            ->where(Bersih::field_status(), TransactionType::BERSIH);

        $transaksi = Transaksi::select(Transaksi::field_rfid())
            ->whereDate(Transaksi::field_created_at(), $date);

            $reject = clone $transaksi;
            $rewash = clone $transaksi;

        if (!empty($rs_id))
        {
            $bersih = $bersih->where(Bersih::field_rs_id(), $rs_id);
            $transaksi = $transaksi->where(Transaksi::field_rs_ori(), $rs_id);
        }

        $kotor = $transaksi->where(Transaksi::field_status_transaction(), TransactionType::KOTOR)
            ->whereNotNull(Transaksi::field_rs_ori());
        $reject = $reject->where(Transaksi::field_status_transaction(), TransactionType::REJECT)
            ->whereNotNull(Transaksi::field_rs_ori());
        $rewash = $rewash->where(Transaksi::field_status_transaction(), TransactionType::REWASH)
            ->whereNotNull(Transaksi::field_rs_ori());

        $pending = Pending::query()
             ->select(['pending_rfid'])
            ->leftJoin('rs', 'rs.rs_id', '=', 'pending.pending_id_rs')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'pending.pending_id_ruangan')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'pending.pending_id_jenis')
            ->leftJoin('view_detail_linen', 'view_detail_linen.view_linen_rfid', '=', 'pending.pending_rfid')
            ->join('config_linen', function ($join) {
                $join->on('config_linen.detail_rfid', '=', 'pending.pending_rfid') // Perbaikan penulisan detail_rfid / details_rfid
                    ->on('config_linen.rs_id', '=', 'rs.rs_id');
            })->whereNull('pending_bersih_at');

        // $hilang = ViewOutstandingHilang::where(Outstanding::field_status_hilang(), HilangType::HILANG);

        return view('pages.home.home', [
            'sebaran' => $sebaran->build(),
            'perbandingan' => $perbandingan->build(),
            'kotor' => $kotor->count(),
            'bersih' => $bersih->count(),
            'reject' => $reject->count(),
            'rewash' => $rewash->count(),
            // 'hilang' => 0,
            'pending' => $pending->count() ,
        ]);
    }

    public function console()
    {
        return LaravelWebConsole::show();
    }

    public function doc()
    {
        return view('doc');
    }

    public function error402()
    {
        return view('errors.402');
    }
}
