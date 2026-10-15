<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewDetailLinenEntity;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class ViewConfigLinen extends Model
{
    use FilterQueryString, PowerJoins, ViewDetailLinenEntity;

    protected $table = 'view_config_linen';

    protected $primaryKey = 'view_linen_rfid';

    protected $casts = [
        'view_linen_rfid' => 'string',
        'view_linen_id' => 'integer',
        'view_pemakaian' => 'integer',
        'view_status_cuci' => 'string',
        'view_status_register' => 'string',
    ];

    protected $filters = [
        'filter',
        'start_date',
        'end_date',
        'view_rs_id',
        'view_ruangan_id',
        'view_linen_id',
        'view_created_id',
        'view_status_cuci',
        'view_status_register',
    ];

    protected $keyType = 'string';

    protected $dates = [
        'view_tanggal_create',
        'view_tanggal_update',
        'view_tanggal_delete',
    ];

    public function start_date($query)
    {
        $date = request()->get('start_date');
        if ($date) {
            $query = $query->whereDate($this->field_reported_at(), '>=', $date);
        }

        return $query;
    }

    public function end_date($query)
    {
        $date = request()->get('end_date');

        if ($date) {
            $query = $query->whereDate($this->field_reported_at(), '<=', $date);
        }

        return $query;
    }

    public function has_category()
    {
        return $this->hasOne(Kategori::class, Kategori::field_primary(), self::field_category_id());
    }

    public function has_bersih()
    {
        return $this->hasOne(ViewTransaksiBersih::class, ViewTransaksiBersih::field_primary(), self::field_primary());
    }

    public function has_retur()
    {
        return $this->hasOne(ViewTransaksiRetur::class, ViewTransaksiRetur::field_primary(), self::field_primary());
    }

    public function has_rewash()
    {
        return $this->hasOne(ViewTransaksiRewash::class, ViewTransaksiRewash::field_primary(), self::field_primary());
    }

    public function has_cuci()
    {
        return $this->hasOne(ViewTransaksiCuci::class, ViewTransaksiCuci::field_primary(), self::field_primary());
    }

    public function has_log()
    {
        return $this->hasOne(ViewLog::class, ViewLog::field_primary(), self::field_primary());
    }
}
