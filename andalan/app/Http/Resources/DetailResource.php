<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
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
        $data = [
            'linen_id' => $this->field_primary,
            'linen_nama' => $this->field_name ?? '',
            'rs_id' => $this->field_rs_id ?? '',
            'rs_nama' => $this->field_rs_name ?? '',
            'ruangan_id' => $this->field_ruangan_id,
            'ruangan_nama' => $this->field_ruangan_name ?? '',
            // 'status_register' => $this->view_status_register,
            // 'status_cuci' => $this->view_status_cuci,
            // 'status_transaksi' => $this->field_status_transaction_name,
            // 'status_proses' => $this->field_status_process_name,
            // 'tanggal_create' => $this->field_tanggal_create ? $this->field_tanggal_create->format('Y-m-d') : null,
            // 'tanggal_update' => $this->field_tanggal_update ? $this->field_tanggal_update->format('Y-m-d') : null,
            // 'tanggal_delete' => $this->field_tanggal_delete ? $this->field_tanggal_delete->format('Y-m-d') : null,
            'pemakaian' => $this->field_cuci ?? 0,
            'user_nama' => $this->field_created_name ? $this->field_created_name : null,
        ];

        return $data;
        // return parent::toArray($request);
    }
}
