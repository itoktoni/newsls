<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\SystemMenuEntity;
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

class SystemMenu extends Model
{
    use ActiveTrait, ActiveTrait, DataTableTrait, FilterQueryString, OptionTrait, Sanitizable, Sortable, SystemMenuEntity;

    protected $table = 'system_menu';

    protected $primaryKey = 'system_menu_code';

    protected $with = ['has_link'];

    protected $keyType = 'string';

    protected $fillable = [
        'system_menu_code',
        'system_menu_name',
        'system_menu_url',
        'system_menu_controller',
        'system_menu_action',
        'system_menu_type',
        'system_menu_sort',
        'system_menu_description',
        'system_menu_enable',
        'system_menu_can_delete',
    ];

    public $sortable = [
        'system_menu_code',
        'system_menu_name',
        'system_menu_action',
        'system_menu_controller',
        'system_menu_sort',
    ];

    protected $casts = [
        'system_menu_enable' => 'integer',
        'system_menu_can_delete' => 'integer',
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

    public function has_link()
    {
        return $this->belongsToMany(SystemLink::class, 'system_menu_connection_link', 'system_menu_code', 'system_link_code');
    }

    public static function boot()
    {
        parent::creating(function ($model) {
            if (empty($model->{SystemMenu::field_action()}) && ($model->{SystemMenu::field_type()} == MenuType::Menu)) {
                $act = '.getTable';
                if (str_contains($model->{SystemMenu::field_name()}, 'report')) {
                    $act = '.getCreate';
                }
                $model->{SystemMenu::field_action()} = Core::getControllerName($model->{SystemMenu::field_controller()}).$act;
            }
        });

        parent::saving(function ($model) {
            if ($model->{SystemMenu::field_type()} == MenuType::Menu) {

                $model->{SystemMenu::field_primary()} = Core::getControllerName($model->{SystemMenu::field_controller()});
                if (empty($model->{SystemMenu::field_url()})) {
                    $model->{SystemMenu::field_url()} = Core::getControllerName($model->{SystemMenu::field_controller()});
                }
            } else {
                $model->{SystemMenu::field_primary()} = Str::snake($model->{SystemMenu::field_name()});
            }
        });
        parent::boot();
    }
}
