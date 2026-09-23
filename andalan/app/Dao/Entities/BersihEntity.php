<?php

namespace App\Dao\Entities;

use App\Dao\Enums\BedaRsType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;

trait BersihEntity
{
    public static function field_primary()
    {
        return 'bersih_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_rfid()
    {
        return 'bersih_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{$this->field_rfid()};
    }

    public static function field_key()
    {
        return 'bersih_key';
    }

    public function getFieldKeyAttribute()
    {
        return $this->{$this->field_key()};
    }

    public static function field_name()
    {
        return self::field_key();
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'bersih_beda_rs';
    }

    public function getFieldDescriptionAttribute()
    {
        return BedaRsType::getDescription($this->{$this->field_description()});
    }

    public static function field_ruangan_id()
    {
        return 'bersih_id_ruangan';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{$this->field_ruangan_id()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

    public static function field_status_transaction()
    {
        return 'bersih_status';
    }

    public function getFieldStatusTransactionAttribute()
    {
        return $this->{$this->field_status_transaction()};
    }

    public function getFieldStatusTransactionNameAttribute()
    {
        return TransactionType::getDescription($this->getFieldStatusTransactionAttribute());
    }

    public static function field_barcode()
    {
        return 'bersih_barcode';
    }

    public function getFieldBarcodeAttribute()
    {
        return $this->{$this->field_barcode()};
    }

    public static function field_delivery()
    {
        return 'bersih_delivery';
    }

    public function getFieldDeliveryAttribute()
    {
        return $this->{$this->field_delivery()};
    }

    public static function field_created_by()
    {
        return 'bersih_created_by';
    }

    public static function field_created_at()
    {
        return 'bersih_created_at';
    }

    public static function field_updated_by()
    {
        return 'bersih_updated_by';
    }

    public static function field_updated_at()
    {
        return 'bersih_updated_at';
    }

    public function getFieldCreatedByAttribute()
    {
        return $this->{$this->field_created_by()};
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{$this->field_created_at()};
    }

    public function getFieldUpdatedByAttribute()
    {
        return $this->{$this->field_updated_by()};
    }

    public function getFieldUpdatedAtAttribute()
    {
        return $this->{$this->field_updated_at()};
    }

    public static function field_report()
    {
        return 'bersih_report';
    }

    public function getFieldReportAttribute()
    {
        return $this->{$this->field_report()};
    }

    public function getFieldCreatedNameAttribute()
    {
        return $this->{User::field_username()};
    }

    public static function field_barcode_by()
    {
        return 'bersih_barcode_by';
    }

    public function getFieldBarcodeByAttribute()
    {
        return $this->{$this->field_barcode_by()};
    }

    public static function field_barcode_at()
    {
        return 'bersih_barcode_at';
    }

    public function getFieldBarcodeAtAttribute()
    {
        return $this->{$this->field_barcode_at()};
    }

    public function getFieldBarcodeNameAttribute()
    {
        return $this->{User::field_username()};
    }

    public static function field_delivery_by()
    {
        return 'bersih_delivery_by';
    }

    public function getFieldDeliveryByAttribute()
    {
        return $this->{$this->field_delivery_by()};
    }

    public static function field_delivery_at()
    {
        return 'bersih_delivery_at';
    }

    public function getFieldDeliveryAtAttribute()
    {
        return $this->{$this->field_delivery_at()};
    }

    public static function field_status()
    {
        return 'bersih_status';
    }

    public function getFieldStatusAttribute()
    {
        return $this->{$this->field_status()};
    }

    public static function field_nama_linen()
    {
        return 'jenis_nama';
    }

    public static function field_total()
    {
        return 'bersih_total';
    }

    public function getFieldTotalAttribute()
    {
        return $this->{$this->field_total()};
    }

    public static function field_rs_id()
    {
        return 'bersih_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_uuid_barcode()
    {
        return 'bersih_uuid_barcode';
    }

    public function getFieldUuidBarcodeAttribute()
    {
        return $this->{$this->field_uuid_barcode()};
    }

    public static function field_uuid_delivery()
    {
        return 'bersih_uuid_delivery';
    }

    public function getFieldUuidDeliveryAttribute()
    {
        return $this->{$this->field_uuid_delivery()};
    }
}
