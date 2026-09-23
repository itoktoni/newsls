<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\ViewInvoiceEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class ViewInvoice extends Model
{
    use ActiveTrait, DataTableTrait, FilterQueryString, OptionTrait, Sanitizable, Sortable, ViewInvoiceEntity;

    protected $table = 'view_invoice';

    protected $primaryKey = 'view_key';

    public $sortable = [
        'view_nama_rs',
        'view_linen_nama',
        'view_tanggal',
    ];

    protected $filters = [
        'filter',
        'start_date',
        'end_date',
        'view_nama_rs',
        'view_linen_nama',
        'view_tanggal',
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
            DataBuilder::build($this->field_primary())->name('Tag Code'),
            DataBuilder::build($this->field_name())->name('Tag Name')->sort(),
        ];
    }
}
