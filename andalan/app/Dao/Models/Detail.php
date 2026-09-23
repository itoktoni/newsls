<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\DetailEntity;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Models\History as HistoryModel;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class Detail extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, DetailEntity, FilterQueryString, OptionTrait, PowerJoins, Sanitizable, Sortable, Userstamps;

    protected $table = 'detail_linen';

    protected $primaryKey = 'detail_rfid';

    protected $fillable = [
        'detail_rfid',
        'detail_id_rs',
        'detail_id_ruangan',
        'detail_id_jenis',
        'detail_id_bahan',
        'detail_id_supplier',
        'detail_deskripsi',
        'detail_created_by',
        'detail_updated_by',
        'detail_deleted_by',
        'detail_created_at',
        'detail_updated_at',
        'detail_deleted_at',
        'detail_status_cuci',
        'detail_status_register',
        'detail_status_linen',
        'detail_status_transaksi',
        'detail_status_edar',
        'detail_total_rewash',
        'detail_total_retur',
        'detail_total_bersih',
        'detail_tgl_cek',
        'detail_report',
        'detail_lama',
        'detail_pengantian_user',
        'detail_pengantian_waktu',
    ];

    public $sortable = [
        'detail_nama',
        'detail_deskripsi',
    ];

    protected $casts = [
        'detail_rfid' => 'string',
        'detail_report' => 'date',
        'detail_status_linen' => 'string',
    ];

    protected $filters = [
        'filter',
        'detail_status_cuci',
        'detail_status_register',
        'view_rs_id',
        'view_ruangan_id',
        'view_linen_id',
        'detail_id_rs',
        'detail_rfid',
        'detail_id_jenis',
        'ruangan_nama',
    ];

    protected $dates = [
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    const CREATED_AT = 'detail_created_at';

    const UPDATED_AT = 'detail_updated_at';

    const DELETED_AT = 'detail_deleted_at';

    const CREATED_BY = 'detail_created_by';

    const UPDATED_BY = 'detail_updated_by';

    const DELETED_BY = 'detail_deleted_by';

    public $timestamps = true;

    public $incrementing = false;
    protected $keyType = 'string';

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldStatus(): array
    {
        return [
            $this->field_status_process() => ProcessType::class,
            $this->field_status_register() => RegisterType::class,
        ];
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('No. RFID')->sort(),
            DataBuilder::build(ViewDetailLinen::field_rs_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build(ViewDetailLinen::field_ruangan_name())->name('Ruangan')->show()->sort(),
            DataBuilder::build(ViewDetailLinen::field_category_name())->name('Kategori')->show()->sort(),
            DataBuilder::build(ViewDetailLinen::field_name())->name('Nama Linen')->show()->sort(),
            DataBuilder::build($this->field_status_register())->name('Register')->show()->sort(),
            DataBuilder::build($this->field_status_cuci())->name('Cuci')->show()->sort(),
            DataBuilder::build($this->field_status_transaction())->name('Transaksi')->show()->sort(),
            DataBuilder::build($this->field_status_process())->name('Posisi Terakhir')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_jenis()
    {
        return $this->hasOne(JenisLinen::class, JenisLinen::field_primary(), self::field_jenis_id());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_ruangan_id());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_created_by());
    }

    public function has_bahan()
    {
        return $this->hasOne(JenisBahan::class, JenisBahan::field_primary(), self::field_bahan_id());
    }

    public function has_supplier()
    {
        return $this->hasOne(Supplier::class, Supplier::field_primary(), self::field_supplier_id());
    }

    public function has_cuci()
    {
        return $this->hasOne(ViewTransaksiCuci::class, ViewTransaksiCuci::field_primary(), self::field_primary());
    }

    public function has_return()
    {
        return $this->hasOne(ViewTransaksiRetur::class, ViewTransaksiRetur::field_primary(), self::field_primary());
    }

    public function has_rewash()
    {
        return $this->hasOne(ViewTransaksiRewash::class, ViewTransaksiRewash::field_primary(), self::field_primary());
    }

    public function has_view()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), self::field_primary());
    }

    public function has_outstanding()
    {
        return $this->hasOne(Outstanding::class, Outstanding::field_primary(), self::field_primary());
    }

    public function has_history()
    {
        return $this->hasMany(HistoryModel::class, HistoryModel::field_name(), self::field_primary());
    }
}
