<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDeliveryReport;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;

/**
 * Report Detail Pengiriman Linen Baru - adopsi andalan
 * ReportDetailPengirimanLinenBaruController (bersih_status REGISTER).
 * Sumber tabel `bersih` (kolom bersih_report). Logic di BuildsDeliveryReport.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportDetailPengirimanLinenBaruController extends Controller
{
    use BuildsDeliveryReport, ControllerTrait {
        BuildsDeliveryReport::getExportExcel insteadof ControllerTrait;
    }

    public const KIND = 'detail';

    public const DELIVERY_TYPE = 'REGISTER';

    public const TITLE = 'DETAIL PENGIRIMAN LINEN BARU';

    public const FILE_PREFIX = 'detail-pengiriman-linen-baru';

    public function getTable(GeneralRequest $request)
    {
        // Hanya form filter - hasil dibuka di getPrint (tab baru).
        return $this->views($this->template(), $this->share());
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => \App\Models\User::rsOptions(),
        ];

        return array_merge($default, $data);
    }
}
