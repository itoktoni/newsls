<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Controllers\Concerns\BuildsDetailList;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;

/**
 * Report Detail Retur — adopsi andalan ReportDetailReturController.
 * Status transaksi REJECT. Logic list + streaming Excel di BuildsDetailList.
 *
 * Tanpa $this->model (seperti BersihController) sehingga lolos
 * GeneralRequest::authorize() dan tidak butuh Policy baru.
 */
class ReportDetailReturController extends Controller
{
    use BuildsDetailList, ControllerTrait {
        BuildsDetailList::getExportExcel insteadof ControllerTrait;
    }

    public const STATUS = TransactionType::REJECT;

    public const TITLE = 'DETAIL TRANSAKSI RETUR';

    public const FILE_PREFIX = 'detail-retur';

    public function getTable(GeneralRequest $request)
    {
        // Hanya form filter — hasil dibuka di getPrint (tab baru).
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
