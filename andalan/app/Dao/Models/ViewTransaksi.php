<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\TransaksiEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class ViewTransaksi extends Transaksi
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OptionTrait, PowerJoins, Sanitizable, Sortable, TransaksiEntity, Userstamps;

    protected $table = 'view_transaksi';

    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
        'transaksi_id',
        'transaksi_key',
        'transaksi_status',
        'transaksi_id_rs',
        'username',
    ];

    public $sortable = [
        'transaksi_key',
        'rs_nama',
        'transaksi_delivery',
        'transaksi_barcode',
        'transaksi_status',
        'transaksi_bersih',
        'transaksi_created_at',
        'transaksi_barcode_at',
        'transaksi_delivery_at',
    ];

    protected $casts = [
        'transaksi_key' => 'string',
        'transaksi_status' => 'string',
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = false;

    public $incrementing = false;

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID Unik')->show(false)->sort(),
            DataBuilder::build($this->field_status_transaction())->name('Status')->show()->sort(),
            DataBuilder::build($this->field_key())->name('Nomer Transaksi')->show()->sort(),
            DataBuilder::build($this->field_total())->name('Total')->show(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build($this->field_created_at())->name('Tanggal')->show()->sort(),
            DataBuilder::build($this->field_created_by())->name('User')->show(false)->sort(),
            DataBuilder::build(User::field_username())->name('User')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_ruangan_id());
    }
}
