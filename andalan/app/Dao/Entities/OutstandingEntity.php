<?php

namespace App\Dao\Entities;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;

trait OutstandingEntity
{
    public static function field_primary()
    {
        return 'outstanding_rfid';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_jenis_id()
    {
        return 'outstanding_id_jenis';
    }

    public static function field_key()
    {
        return 'outstanding_key';
    }

    public function getFieldKeyAttribute()
    {
        return $this->{$this->field_key()};
    }

    public static function field_name()
    {
        return self::field_primary();
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function getFieldWeightAttribute()
    {
        return $this->{JenisLinen::field_weight()};
    }

    public static function field_description()
    {
        return 'outstanding_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_ruangan_id()
    {
        return 'outstanding_id_ruangan';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{$this->field_ruangan_id()};
    }

    public function getFieldRuanganNameAttribute()
    {
        return $this->{Ruangan::field_name()};
    }

    public static function field_rs_ori()
    {
        return 'outstanding_rs_ori';
    }

    public function getFieldRsOriAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_rs_scan()
    {
        return 'outstanding_rs_scan';
    }

    public function getFieldRsScanAttribute()
    {
        return $this->{$this->field_rs_scan()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

    public static function field_status_beda_rs()
    {
        return 'outstanding_status_beda_rs';
    }

    public function getFieldStatusBedaRsAttribute()
    {
        return $this->{$this->field_status_beda_rs()};
    }

    public function getFieldStatusCuciNameAttribute()
    {
        return CuciType::getDescription($this->getFieldStatusCuciAttribute());
    }

    public static function field_status_transaction()
    {
        return 'outstanding_status_transaksi';
    }

    public function getFieldStatusTransactionAttribute()
    {
        return $this->{$this->field_status_transaction()};
    }

    public function getFieldStatusTransactionNameAttribute()
    {
        return TransactionType::getDescription($this->getFieldStatusTransactionAttribute());
    }

    public static function field_status_register()
    {
        return 'outstanding_status_register';
    }

    public function getFieldStatusRegisterAttribute()
    {
        return $this->{$this->field_status_register()};
    }

    public function getFieldStatusRegisterNameAttribute()
    {
        return RegisterType::getDescription($this->getFieldStatusRegisterAttribute());
    }

    public static function field_status_process()
    {
        return 'outstanding_status_proses';
    }

    public function getFieldStatusProcessAttribute()
    {
        return $this->{$this->field_status_process()};
    }

    public function getFieldStatusProcessNameAttribute()
    {
        return ProcessType::getDescription($this->getFieldStatusProcessAttribute());
    }

    public static function field_status_hilang()
    {
        return 'outstanding_status_hilang';
    }

    public function getFieldStatusHilangAttribute()
    {
        return $this->{$this->field_status_hilang()};
    }

    public function getFieldStatusHilangNameAttribute()
    {
        return HilangType::getDescription($this->getFieldStatusProcessAttribute());
    }

    public static function field_created_at()
    {
        return 'outstanding_created_at';
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{self::field_created_at()};
    }

    public static function field_created_by()
    {
        return 'outstanding_created_by';
    }

    public static function field_updated_at()
    {
        return 'outstanding_updated_at';
    }

    public static function field_updated_by()
    {
        return 'outstanding_updated_by';
    }

    public function getFieldUpdatedAtAttribute()
    {
        return $this->{$this->field_updated_at()};
    }

    public static function field_pending_created_at()
    {
        return 'outstanding_pending_created_at';
    }

    public function getFieldPendingCreatedAtAttribute()
    {
        return $this->{$this->field_pending_created_at()};
    }

    public static function field_pending_updated_at()
    {
        return 'outstanding_pending_updated_at';
    }

    public function getFieldPendingAtAttribute()
    {
        return $this->{$this->field_pending_updated_at()};
    }

    public static function field_hilang_created_at()
    {
        return 'outstanding_hilang_created_at';
    }

    public function getFieldHilangCreatedAtAttribute()
    {
        return $this->{$this->field_hilang_created_at()};
    }

    public static function field_hilang_updated_at()
    {
        return 'outstanding_hilang_updated_at';
    }

    public function getFieldHilangAtAttribute()
    {
        return $this->{$this->field_hilang_updated_at()};
    }

    public static function field_total_cuci()
    {
        return 'outstanding_total_cuci';
    }

    public function getFieldTotalCuciAttribute()
    {
        return $this->{$this->field_total_cuci()};
    }

    public static function field_linen_id()
    {
        return JenisLinen::field_primary();
    }

    public function getFieldLinenIdAttribute()
    {
        return $this->{$this->field_linen_name()};
    }

    public static function field_linen_name()
    {
        return JenisLinen::field_name();
    }

    public function getFieldLinenNameAttribute()
    {
        return $this->{$this->field_linen_name()};
    }

    public static function field_location_name()
    {
        return Ruangan::field_name();
    }

    public function getFieldLocationNameAttribute()
    {
        return $this->{$this->field_location_name()};
    }

    public function getFieldRsOriNameAttribute()
    {
        return $this->view_rs_ori_nama;
    }

    public function getFieldRsScanNameAttribute()
    {
        return $this->view_rs_scan_nama;
    }

    public function getFieldOperatorAttribute()
    {
        return $this->view_operator;
    }
}
