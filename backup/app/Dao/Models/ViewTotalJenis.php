<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewTotalJenisEntity;
use Illuminate\Database\Eloquent\Model;

class ViewTotalJenis extends Model
{
    use ViewTotalJenisEntity;

    protected $table = 'view_total_jenis';

    protected $primaryKey = 'view_total_jenis_id';

    protected $casts = [
        'view_total_jenis_id' => 'integer',
        'view_total_jenis_total' => 'integer',
    ];
}
