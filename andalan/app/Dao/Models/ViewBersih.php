<?php

namespace App\Dao\Models;

use App\Dao\Entities\BersihEntity;
use Illuminate\Database\Eloquent\Model;

class ViewBersih extends Bersih
{
    protected $table = 'view_bersih';

    protected $primaryKey = 'bersih_id';

    protected $casts = [
        'bersih_rfid' => 'string',
        'bersih_rs_id' => 'integer',
        'bersih_created_at' => 'datetime',
        'bersih_updated_at' => 'datetime',
    ];

    protected $filters = [
        'view_rs_id',
        'view_ruangan_id',
        'view_linen_id',
    ];

    public function view_rs_id($query, $value) {
        return $query->where('rs_id', $value);
    }

    public function view_ruangan_id($query, $value) {

        if(!empty($value)){
            return $query->where('ruangan_id', $value);
        }

        return $query;
    }

    public function view_linen_id($query, $value) {
        if(!empty($value)){
            return $query->where('jenis_id', $value);
        }

        return $query;
    }
}
