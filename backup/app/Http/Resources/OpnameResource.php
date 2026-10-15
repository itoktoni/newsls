<?php

namespace App\Http\Resources;

use App\Dao\Models\Opname;
use App\Dao\Models\Rs;
use Illuminate\Http\Resources\Json\JsonResource;

class OpnameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $model;

    public function toArray($request)
    {
        return [
            'opname_id' => $this->{Opname::field_primary()},
            'opname_start' => $this->{Opname::field_start()},
            'opname_end' => $this->{Opname::field_end()},
            'rs_id' => $this->has_rs->{Rs::field_primary()} ?? '',
            'rs_nama' => $this->has_rs->{Rs::field_name()} ?? '',
        ];
        // return parent::toArray($request);
    }
}
