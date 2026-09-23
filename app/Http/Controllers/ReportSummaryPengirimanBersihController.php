<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDeliveryReport;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;

/**
 * Summary pengiriman linen bersih - adopsi andalan ReportSummaryPengirimanBersihController.
 * Logic di BuildsDeliveryReport. Tanpa model sehingga lolos authorize.
 */
class ReportSummaryPengirimanBersihController extends Controller
{
    use BuildsDeliveryReport, ControllerTrait {
        BuildsDeliveryReport::getExportExcel insteadof ControllerTrait;
    }

    public const KIND = 'summary';

    public const DELIVERY_TYPE = 'BERSIH';

    public const TITLE = 'SUMMARY PENGIRIMAN LINEN BERSIH';

    public const FILE_PREFIX = 'summary-pengiriman-bersih';

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
