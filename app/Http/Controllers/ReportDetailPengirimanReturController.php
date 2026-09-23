<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDeliveryReport;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;

/**
 * Detail pengiriman linen retur - adopsi andalan ReportDetailPengirimanReturController.
 * Logic di BuildsDeliveryReport. Tanpa model sehingga lolos authorize.
 */
class ReportDetailPengirimanReturController extends Controller
{
    use BuildsDeliveryReport, ControllerTrait {
        BuildsDeliveryReport::getExportExcel insteadof ControllerTrait;
    }

    public const KIND = 'detail';

    public const DELIVERY_TYPE = 'RETUR';

    public const TITLE = 'DETAIL PENGIRIMAN LINEN RETUR';

    public const FILE_PREFIX = 'detail-pengiriman-retur';

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
