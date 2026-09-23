<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewTransaksiReturEntity;
use Illuminate\Database\Eloquent\Model;

class ViewTransaksiRetur extends Model
{
    use ViewTransaksiReturEntity;

    protected $table = 'view_transaksi_retur';

    protected $primaryKey = 'view_transaksi_retur_id';

    protected $casts = [
        'view_transaksi_retur_id' => 'string',
        'view_transaksi_retur_total' => 'integer',
    ];
}
