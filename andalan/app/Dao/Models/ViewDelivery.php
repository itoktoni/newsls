<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;

class ViewDelivery extends ViewTransaksi
{
    protected $table = 'view_delivery';

    protected $primaryKey = 'transaksi_id';

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('Kode Delivery')->sort(),
            DataBuilder::build($this->field_status_bersih())->name('Status')->show()->sort(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build($this->field_total())->name('Total')->show()->sort(),
            DataBuilder::build($this->field_delivery_at())->name('Tanggal Delivery')->show()->sort(),
            DataBuilder::build($this->field_created_by())->name('User')->show(false)->sort(),
            DataBuilder::build(User::field_username())->name('User')->show()->sort(),
        ];
    }
}
