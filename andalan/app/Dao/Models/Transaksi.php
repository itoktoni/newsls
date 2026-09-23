<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\TransaksiEntity;
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

class Transaksi extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OptionTrait, PowerJoins, Sanitizable, Sortable, TransaksiEntity, Userstamps;

    protected $table = 'transaksi';

    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
        'transaksi_id',
        'transaksi_key',
        'transaksi_status',
        'transaksi_rfid',
        'transaksi_report',
        'transaksi_barcode',
        'transaksi_delivery',
        'transaksi_beda_rs',
        'transaksi_rs_ori',
        'transaksi_rs_scan',
        'transaksi_id_ruangan',
        'transaksi_created_at',
        'transaksi_updated_at',
        'transaksi_created_by',
        'transaksi_updated_by',
        'transaksi_deleted_at',
        'transaksi_deleted_by',
        'transaksi_barcode_at',
        'transaksi_barcode_by',
        'transaksi_delivery_at',
        'transaksi_delivery_by',
        'transaksi_bersih',
        'transaksi_uuid_barcode',
        'transaksi_uuid_delivery',
        'transaksi_grouping',
    ];

    public $sortable = [
        'transaksi_key',
    ];

    protected $casts = [
        'transaksi_rfid' => 'string',
        'transaksi_status' => 'string',
    ];

    protected $filters = [
        'filter',
        'transaksi_rfid',
        'transaksi_id_rs',
        'transaksi_status',
        'transaksi_key',
        'transaksi_barcode',
        'transaksi_delivery',
        'transaksi_bersih',
        'transaksi_created_by',
        'start_date',
        'end_date',
        'rs_id',
        'view_rs_id',
        'view_ruangan_id',
        'view_linen_id',
        'view_status_process',
        'transaksi_rs_ori',
    ];

    const CREATED_AT = 'transaksi_created_at';

    const UPDATED_AT = 'transaksi_updated_at';

    const DELETED_AT = 'transaksi_deleted_at';

    const CREATED_BY = 'transaksi_created_by';

    const UPDATED_BY = 'transaksi_updated_by';

    const DELETED_BY = 'transaksi_deleted_by';

    public $timestamps = true;

    public $incrementing = false;

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build(Transaksi::field_key())->name('NO. TRANSAKSI')->sort()->sort(),
            DataBuilder::build(Transaksi::field_created_at())->name('TANGGAL KOTOR')->sort(),
            DataBuilder::build(Transaksi::field_rfid())->name('NO. RFID')->sort(),
            DataBuilder::build(ViewDetailLinen::field_name())->name('LINEN ')->sort(),
            DataBuilder::build(ViewDetailLinen::field_rs_name())->name('RUMAH SAKIT')->sort(),
            DataBuilder::build(ViewDetailLinen::field_ruangan_name())->name('RUANGAN')->sort(),
            DataBuilder::build(Rs::field_name())->name('LOKASI SCAN RUMAH SAKIT')->sort(),
            DataBuilder::build(Transaksi::field_status_transaction())->name('STATUS KOTOR')->sort(),
            DataBuilder::build(ViewDetailLinen::field_pending_create())->name('PENDING')->sort(),
            DataBuilder::build(ViewDetailLinen::field_hilang_create())->name('HILANG')->sort(),
            DataBuilder::build(User::field_name())->name('OPERATOR')->sort(),
            DataBuilder::build(Transaksi::field_barcode())->name('No. BARCODE')->sort(),
            DataBuilder::build(Transaksi::field_delivery())->name('No. DELIVERY')->sort(),
            DataBuilder::build(Transaksi::field_status_bersih())->name('STATUS BERSIH')->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_rfid()
    {
        return $this->hasOne(Detail::class, Detail::field_primary(), self::field_rfid());
    }

    public function has_detail()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), self::field_rfid());
    }

    public function has_cuci()
    {
        return $this->hasOne(ViewTransaksiCuci::class, ViewTransaksiCuci::field_primary(), self::field_rfid());
    }

    public function has_retur()
    {
        return $this->hasOne(ViewTransaksiRetur::class, ViewTransaksiRetur::field_primary(), self::field_rfid());
    }

    public function has_rewash()
    {
        return $this->hasOne(ViewTransaksiRewash::class, ViewTransaksiRewash::field_primary(), self::field_rfid());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_ori());
    }

    public function has_rs_delivery()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_ori());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_ruangan_id());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_created_by());
    }

    public function has_history()
    {
        return $this->hasMany(HistoryModel::class, HistoryModel::field_name(), self::field_primary());
    }

    public function has_created_barcode()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_barcode_by());
    }

    public function has_created_delivery()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_delivery_by());
    }
}
