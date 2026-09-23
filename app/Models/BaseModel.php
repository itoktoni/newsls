<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBaseModel
 */
class BaseModel extends Model
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    // Remove hardcoded products table reference since it's not part of our application
    // Each model should define its own table name
    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [];

    /**
     * Accessor: $table->field_primary in blade templates → model ID.
     */
    public function getFieldPrimaryAttribute(): mixed
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Validation rules. Models override this.
     *
     * ponytail: returns [] on purpose — a guessed default (field_name + 'name')
     * produced errors keyed to columns absent from the form, so nothing rendered
     * and the submit looked like a silent no-op.
     */
    public function rules(): array
    {
        return [];
    }
}
