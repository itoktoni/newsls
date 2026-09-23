<?php

namespace App\Dao\Traits;

use App\Dao\Models\Filters;
use Illuminate\Support\Facades\Cache;
use Plugins\SharedData;

trait ActiveTrait
{
    // abstract public function field_active();
    public function scopeActive($query)
    {
        if (Cache::has('filter')) {
            foreach (Cache::get('filter') as $filter) {
                if (SharedData::get('action_code') == $filter->{Filters::field_name()}) {
                    $value = $filter->{Filters::field_from_user()} ? auth()->user()->{$filter->{Filters::field_value()}} : $filter->{Filters::field_value()};
                    $query = $query->where($filter->{Filters::field_field()}, $filter->{Filters::field_operator()}, $value);
                }
            }
        }

        return $query;
    }
}
