<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewTransaksiBersihEntity;
use Illuminate\Database\Eloquent\Model;

class ViewTransaksiBersih extends Model
{
    use ViewTransaksiBersihEntity;

    protected $table = 'view_transaksi_bersih';

    protected $primaryKey = 'view_transaksi_bersih_id';

    protected $casts = [
        'view_transaksi_bersih_id' => 'string',
        'view_transaksi_bersih_total' => 'integer',
    ];
}
