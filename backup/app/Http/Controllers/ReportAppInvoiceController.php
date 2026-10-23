<?php

namespace App\Http\Controllers;

use App\Dao\Models\ViewInvoice;
use App\Dao\Models\ViewRs;
use App\Dao\Repositories\ViewInvoiceRepository;

class ReportAppInvoiceController extends MinimalController
{
    public $data;

    public function __construct(ViewInvoiceRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {

        $rs = ViewRs::getOptions();

        self::$share = [
            'rs' => $rs,
        ];
    }

    public function getPrint()
    {
        set_time_limit(0);
        $this->data = self::$repository->getPrint()->get();

        $date = $linen = [];
        if ($this->data) {

            $date = $this->data->unique(ViewInvoice::field_tanggal())->pluck(ViewInvoice::field_tanggal());
            $linen = $this->data->mapToGroups(function ($item, $key) {
                return [$item[ViewInvoice::field_name()] => $item];
            })->sortKeys();

        }

        return moduleView(modulePathPrint(), $this->share([
            'model' => $this->data->first(),
            'data' => $this->data,
            'linen' => $linen,
            'date' => $date,
        ]));
    }
}
