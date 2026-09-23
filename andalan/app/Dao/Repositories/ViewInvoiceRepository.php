<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\ViewInvoice;

class ViewInvoiceRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new ViewInvoice() : $this->model;
    }

    public function getPrint()
    {
        return $this->model->query()->filter()->orderBy(ViewInvoice::field_tanggal());
    }
}
