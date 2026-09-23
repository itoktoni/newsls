<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewTransaksiCuciEntity;
use Illuminate\Database\Eloquent\Model;

class ViewTransaksiCuci extends Model
{
    use ViewTransaksiCuciEntity;

    protected $table = 'view_transaksi_cuci';

    protected $primaryKey = 'view_transaksi_cuci_id';

    protected $casts = [
        'view_transaksi_cuci_id' => 'string',
        'view_transaksi_cuci_total' => 'integer',
    ];
}
