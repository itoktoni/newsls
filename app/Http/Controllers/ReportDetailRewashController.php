<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Controllers\Concerns\BuildsDetailList;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;

/**
 * Report Detail Rewash — adopsi andalan ReportDetailRewashController.
 * Logic list + streaming Excel di BuildsDetailList.
 *
 * Tanpa $this->model (seperti BersihController) sehingga lolos
 * GeneralRequest::authorize() dan tidak butuh Policy baru.
 */
class ReportDetailRewashController extends Controller
{
    use BuildsDetailList, ControllerTrait {
        BuildsDetailList::getExportExcel insteadof ControllerTrait;
    }

    public const STATUS = TransactionType::REWASH;

    public const TITLE = 'DETAIL TRANSAKSI REWASH';

    public const FILE_PREFIX = 'detail-rewash';

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
