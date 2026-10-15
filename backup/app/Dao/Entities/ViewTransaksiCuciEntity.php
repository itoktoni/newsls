<?php

namespace App\Dao\Entities;

trait ViewTransaksiCuciEntity
{
    public static function field_primary()
    {
        return 'view_transaksi_cuci_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_total()
    {
        return 'view_transaksi_cuci_total';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_total()};
    }
}
