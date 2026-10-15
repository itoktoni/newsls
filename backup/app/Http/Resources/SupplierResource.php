<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'supplier_id' => $this->field_primary,
            'supplier_nama' => $this->field_name,
        ];
        // return parent::toArray($request);
    }
}
