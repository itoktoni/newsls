<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewTransaksiRewashEntity;
use Illuminate\Database\Eloquent\Model;

class ViewTransaksiRewash extends Model
{
    use ViewTransaksiRewashEntity;

    protected $table = 'view_transaksi_rewash';

    protected $primaryKey = 'view_transaksi_rewash_id';

    protected $casts = [
        'view_transaksi_rewash_id' => 'string',
        'view_transaksi_rewash_total' => 'integer',
    ];
}
