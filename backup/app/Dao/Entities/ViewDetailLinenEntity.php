<?php

namespace App\Dao\Entities;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;

trait ViewDetailLinenEntity
{
    public static function field_primary()
    {
        return 'view_linen_rfid';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_id()
    {
        return 'view_linen_id';
    }

    public function getFieldIdAttribute()
    {
        return $this->{$this->field_id()};
    }

    public static function field_name()
    {
        return 'view_linen_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_rs_id()
    {
        return 'view_rs_id';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_rs_name()
    {
        return 'view_rs_nama';
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{$this->field_rs_name()};
    }

    public static function field_ruangan_id()
    {
        return 'view_ruangan_id';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{$this->field_ruangan_id()};
    }

    public static function field_ruangan_name()
    {
        return 'view_ruangan_nama';
    }

    public function getFieldRuanganNameAttribute()
    {
        return $this->{$this->field_ruangan_name()};
    }

    public static function field_status_register()
    {
        return 'view_status_register';
    }

    public function getFieldStatusRegisterAttribute()
    {
        return $this->{$this->field_status_register()};
    }

    public static function field_status_kepemilikan()
    {
        return 'view_status_kepemilikan';
    }

    public function getFieldStatusKepemilikanAttribute()
    {
        return $this->{$this->field_status_kepemilikan()};
    }

    public function getFieldStatusRegisterNameAttribute()
    {
        return RegisterType::getDescription($this->getFieldStatusRegisterAttribute());
    }

    public static function field_status_cuci()
    {
        return 'view_status_cuci';
    }

    public function getFieldStatusCuciAttribute()
    {
        return $this->{$this->field_status_cuci()};
    }

    public function getFieldStatusCuciNameAttribute()
    {
        return CuciType::getDescription($this->getFieldStatusCuciAttribute());
    }

    public static function field_status_trasaction()
    {
        return 'view_status_transaksi';
    }

    public function getFieldStatusTransactionAttribute()
    {
        return $this->{$this->field_status_trasaction()};
    }

    public function getFieldStatusTransactionNameAttribute()
    {
        return TransactionType::getDescription($this->getFieldStatusTransactionAttribute());
    }

    public static function field_status_process()
    {
        return 'view_status_proses';
    }

    public function getFieldStatusProcessAttribute()
    {
        return $this->{$this->field_status_process()};
    }

    public function getFieldStatusProcessNameAttribute()
    {
        return ProcessType::getDescription($this->getFieldStatusProcessAttribute());
    }

    public static function field_tanggal_update()
    {
        return 'view_tanggal_update';
    }

    public function getFieldTanggalUpdateAttribute()
    {
        return $this->{$this->field_tanggal_update()};
    }

    public static function field_tanggal_create()
    {
        return 'view_tanggal_create';
    }

    public function getFieldTanggalCreateAttribute()
    {
        return $this->{$this->field_tanggal_create()};
    }

    public static function field_tanggal_delete()
    {
        return 'view_tanggal_delete';
    }

    public function getFieldTanggalDeleteAttribute()
    {
        return $this->{$this->field_tanggal_delete()};
    }

    public static function field_pemakaian()
    {
        return 'view_pemakaian';
    }

    public function getFieldPemakaianAttribute()
    {
        return $this->{$this->field_pemakaian()};
    }

    public static function field_created_name()
    {
        return 'view_created_name';
    }

    public function getFieldCreatedNameAttribute()
    {
        return $this->{$this->field_created_name()};
    }

    public static function field_updated_name()
    {
        return 'view_updated_name';
    }

    public function getFieldUpdatedNameAttribute()
    {
        return $this->{$this->field_updated_name()};
    }

    public static function field_weight()
    {
        return 'view_linen_berat';
    }

    public function getFieldWeightAttribute()
    {
        return $this->{$this->field_weight()};
    }

    public static function field_category_id()
    {
        return 'view_kategori_id';
    }

    public function getFieldCategoryIdAttribute()
    {
        return $this->{$this->field_category_id()};
    }

    public static function field_category_name()
    {
        return 'view_kategori_name';
    }

    public function getFieldCategoryNameAttribute()
    {
        return $this->{$this->field_category_name()};
    }

    public static function field_reported_at()
    {
        return 'view_tanggal_create';
    }

    public static function field_created_at()
    {
        return 'view_tanggal_create';
    }

    public static function field_pending_create()
    {
        return 'view_pending_create';
    }

    public function getFieldPendingCreatedAtAttribute()
    {
        return $this->{$this->field_pending_create()};
    }

    public static function field_pending_update()
    {
        return 'view_pending_update';
    }

    public function getFieldPendingUpdateAtAttribute()
    {
        return $this->{$this->field_pending_update()};
    }

    public static function field_hilang_create()
    {
        return 'view_hilang_create';
    }

    public function getFieldHilangCreatedAtAttribute()
    {
        return $this->{$this->field_hilang_create()};
    }

    public static function field_hilang_update()
    {
        return 'view_hilang_update';
    }

    public function getFieldHilangUpdateAtAttribute()
    {
        return $this->{$this->field_hilang_update()};
    }

    public static function field_cuci()
    {
        return 'view_transaksi_cuci_total';
    }

    public function getFieldCuciAttribute()
    {
        return $this->{$this->field_cuci()};
    }

    public static function field_bersih()
    {
        return 'view_transaksi_bersih_total';
    }

    public function getFieldBersihAttribute()
    {
        return $this->{$this->field_bersih()};
    }

    public static function field_retur()
    {
        return 'view_transaksi_retur_total';
    }

    public function getFieldReturAttribute()
    {
        return $this->{$this->field_retur()};
    }

    public static function field_rewash()
    {
        return 'view_transaksi_rewash_total';
    }

    public function getFieldRewashAttribute()
    {
        return $this->{$this->field_rewash()};
    }
}
