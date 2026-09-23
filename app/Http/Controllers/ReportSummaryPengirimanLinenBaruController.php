<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDeliveryReport;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;

/**
 * Report Summary Pengiriman Linen Baru - adopsi andalan
 * ReportSummaryPengirimanLinenBaruController (bersih_status REGISTER).
 * Sumber tabel `bersih` (kolom bersih_report). Logic di BuildsDeliveryReport.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportSummaryPengirimanLinenBaruController extends Controller
{
    use BuildsDeliveryReport, ControllerTrait {
        BuildsDeliveryReport::getExportExcel insteadof ControllerTrait;
    }

    public const KIND = 'summary';

    public const DELIVERY_TYPE = 'REGISTER';

    public const TITLE = 'SUMMARY PENGIRIMAN LINEN BARU';

    public const FILE_PREFIX = 'summary-pengiriman-linen-baru';

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
