<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DownloadLinenResource extends JsonResource
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
            'id' => $this->field_primary,
            'rs' => $this->field_rs_id,
            'loc' => $this->field_ruangan_id,
            'jns' => $this->field_id,
            'sts' => $this->field_status_process,
            'tgl' => $this->field_tanggal_update->format('Y-m-d H:i:s') ?? date('Y-m-d H:i:s'),
        ];
        // return parent::toArray($request);
    }
}
