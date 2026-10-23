<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\SystemLinkEntity;
use App\Dao\Enums\MenuType;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Plugins\Core;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class SystemLink extends Model
{
    use ActiveTrait, DataTableTrait, FilterQueryString, OptionTrait, Sanitizable, Sortable, SystemLinkEntity;

    protected $table = 'system_link';

    protected $primaryKey = 'system_link_code';

    protected $keyType = 'string';

    protected $fillable = [
        'system_link_code',
        'system_link_name',
        'system_link_action',
        'system_link_controller',
        'system_link_sort',
        'system_link_description',
        'system_link_enable',
        'system_link_url',
        'system_link_type',
    ];

    public $sortable = [
        'system_link_code',
        'system_link_name',
        'system_link_controller',
        'system_link_sort',
    ];

    protected $casts = [
        'system_link_sort' => 'integer',
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
            DataBuilder::build($this->field_primary())->name('Code')->sort(),
            DataBuilder::build($this->field_name())->name('Name')->show()->sort(),
            DataBuilder::build($this->field_controller())->name('Controller')->sort(),
            DataBuilder::build($this->field_description())->name('Description')->show(false),
            DataBuilder::build($this->field_url())->name('Url')->sort(),
            DataBuilder::build($this->field_sort())->name('Sort')->sort()->class('column-active'),
        ];
    }

    public static function boot()
    {
        parent::creating(function ($model) {
            if (empty($model->{SystemLink::field_action()}) && ($model->{SystemLink::field_type()} == MenuType::Menu)) {
                $act = '.getCreate';
                $model->{SystemLink::field_action()} = Core::getControllerName($model->{SystemLink::field_controller()}).$act;
            }
        });

        parent::saving(function ($model) {
            if ($model->{SystemLink::field_type()} == MenuType::Menu) {

                $model->{SystemLink::field_primary()} = Core::getControllerName($model->{SystemLink::field_controller()});
                if (empty($model->{SystemLink::field_url()})) {
                    $model->{SystemLink::field_url()} = Core::getControllerName($model->{SystemLink::field_controller()});
                }
            } else {
                $model->{SystemLink::field_primary()} = Str::snake($model->{SystemLink::field_name()});
            }
        });
        parent::boot();
    }
}
