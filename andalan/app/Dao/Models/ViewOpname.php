<?php

namespace App\Dao\Models;

use Illuminate\Database\Eloquent\Model;

class ViewOpname extends Model
{
    protected $table = 'view_opname';

    protected $primaryKey = 'so_id';

    public function has_jenis()
    {
        return $this->hasOne(JenisLinen::class, JenisLinen::field_primary(), JenisLinen::field_primary());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), Ruangan::field_primary());
    }
}
