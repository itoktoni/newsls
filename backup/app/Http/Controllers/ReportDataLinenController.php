<?php

namespace App\Http\Controllers;

use App\Dao\Models\JenisLinen;
use App\Dao\Models\Kategori;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ReportDataLinenController extends MinimalController
{
    public $data;

    public function __construct(DetailRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {

        $rs = Rs::getOptions();
        $ruangan = Ruangan::getOptions();
        $kategori = Kategori::getOptions();
        $jenis = JenisLinen::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'kategori' => $kategori,
            'ruangan' => $ruangan,
            'rs' => $rs,
        ];
    }

    private function getQuery($request)
    {
        return self::$repository->getPrintDataMaster()->get();
    }

    private function exportExcel($request)
    {
        $writer = SimpleExcelWriter::streamDownload(moduleName().'.xlsx');
        self::$repository->getPrintDataMaster()->chunk(1000, function ($item) use ($writer) {
            foreach ($item as $key => $table) {
                $writer->addRow([
                    'No.' => $key + 1,
                    'NO. RFID' => $table->field_primary,
                    'KATEGORI LINEN' => $table->view_kategori_nama,
                    'LINEN' => $table->field_name,
                    'BERAT' => $table->field_weight,
                    'RUMAH SAKIT' => $table->field_rs_name,
                    'RUANGAN' => $table->field_ruangan_name,
                    'CUCI/RENTAL' => $table->field_status_cuci_name,
                    'JUMLAH PEMAKAIAN LINEN' => $table->field_cuci,
                    'JUMLAH BERSIH' => $table->field_bersih,
                    'JUMLAH RETUR' => $table->field_retur,
                    'JUMLAH REWASH' => $table->field_rewash,
                    'POSISI TERAKHIR' => $table->field_status_process_name,
                    'TGL POSISI TERAKHIR' => formatDate($table->field_tanggal_update),
                    'OPERATOR UPDATE TERAKHIR' => $table->field_updated_name,
                    'STATUS REGISTER' => $table->field_status_register_name,
                    'TGL REGISTRASI' => formatDate($table->field_tanggal_create),
                    'OPERATOR REGISTRASI' => $table->field_created_name,
                ]);

                if ($key % 1000 === 0) {
                    flush(); // Flush the buffer every 1000 rows
                }
            }
        });

        return $writer->toBrowser();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));

        $this->data = [];
        if ($request->action == 'export' || $this->getQuery($request)->count() > env('APP_CHUNK', 10000)) {
            return $this->exportExcel($request);
        }

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
