<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RsSingleResource extends JsonResource
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
            'rs_id' => $this->field_primary,
            'rs_nama' => $this->field_name,
            'rs_status' => $this->field_status,
            'rs_ruangan' => [],
            'rs_jenis' => [],
        ];
        // return parent::toArray($request);
    }
}
