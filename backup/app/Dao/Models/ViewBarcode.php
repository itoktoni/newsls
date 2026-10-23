<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;

class ViewBarcode extends ViewTransaksi
{
    protected $table = 'view_barcode';

    protected $primaryKey = 'transaksi_id';

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('Kode Barcode')->sort(),
            DataBuilder::build($this->field_delivery())->name('Kode Delivery')->show()->sort(),
            DataBuilder::build($this->field_status_bersih())->name('Status')->show()->sort(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build($this->field_total())->name('Total')->show()->sort(),
            DataBuilder::build($this->field_barcode_at())->name('Tanggal Barcode')->show()->sort(),
            DataBuilder::build($this->field_created_by())->name('User')->show(false)->sort(),
            DataBuilder::build(User::field_username())->name('User')->show()->sort(),
        ];
    }
}
