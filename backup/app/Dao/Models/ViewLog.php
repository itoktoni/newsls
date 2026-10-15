<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewLogEntity;
use Illuminate\Database\Eloquent\Model;

class ViewLog extends Model
{
    use ViewLogEntity;

    protected $table = 'view_log';

    protected $primaryKey = 'view_log_rfid';

    protected $casts = [
        'view_log_status' => 'integer',
    ];
}
