<?php

namespace App\Dao\Models;

use Illuminate\Database\Eloquent\Model;

class ViewOutstanding extends Outstanding
{
    protected $table = 'view_outstanding';

    protected $primaryKey = 'outstanding_rfid';

    protected $casts = [
        'outstanding_rs_ori' => 'integer',
        'outstanding_rs_scan' => 'integer',
        'outstanding_created_at' => 'datetime',
        'outstanding_updated_at' => 'datetime',
        'outstanding_pending_created_at' => 'datetime',
        'outstanding_pending_updated_at' => 'datetime',
        'outstanding_hilang_created_at' => 'datetime',
        'outstanding_hilang_updated_at' => 'datetime',
    ];

    protected $filters = [
        'filter',
        'view_rs_id'
    ];

    public function has_outstanding()
    {
        return $this->hasOne(Outstanding::class, Outstanding::field_primary(), $this->field_primary());
    }

    public function has_detail()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), $this->field_primary());
    }
}
