<?php

namespace App\Dao\Entities;

use App\Dao\Enums\BedaRsType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;

trait TransaksiEntity
{
    public static function field_primary()
    {
        return 'transaksi_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_rfid()
    {
        return 'transaksi_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{$this->field_rfid()};
    }

    public static function field_key()
    {
        return 'transaksi_key';
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
        return 'transaksi_beda_rs';
    }

    public function getFieldDescriptionAttribute()
    {
        return BedaRsType::getDescription($this->{$this->field_description()});
    }

    public static function field_ruangan_id()
    {
        return 'transaksi_id_ruangan';
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
        return 'transaksi_status';
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
        return 'transaksi_barcode';
    }

    public function getFieldBarcodeAttribute()
    {
        return $this->{$this->field_barcode()};
    }

    public static function field_beda_rs()
    {
        return 'transaksi_beda_rs';
    }

    public function getFieldBedaRsAttribute()
    {
        return $this->{$this->field_beda_rs()};
    }

    public function getFieldBedaRsNameAttribute()
    {
        return BedaRsType::getDescription($this->getFieldBedaRsAttribute());
    }

    public static function field_delivery()
    {
        return 'transaksi_delivery';
    }

    public function getFieldDeliveryAttribute()
    {
        return $this->{$this->field_delivery()};
    }

    public static function field_created_by()
    {
        return 'transaksi_created_by';
    }

    public static function field_created_at()
    {
        return 'transaksi_created_at';
    }

    public static function field_updated_by()
    {
        return 'transaksi_updated_by';
    }

    public static function field_updated_at()
    {
        return 'transaksi_updated_at';
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
        return 'transaksi_report';
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
        return 'transaksi_barcode_by';
    }

    public function getFieldBarcodeByAttribute()
    {
        return $this->{$this->field_barcode_by()};
    }

    public static function field_barcode_at()
    {
        return 'transaksi_barcode_at';
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
        return 'transaksi_delivery_by';
    }

    public function getFieldDeliveryByAttribute()
    {
        return $this->{$this->field_delivery_by()};
    }

    public static function field_delivery_at()
    {
        return 'transaksi_delivery_at';
    }

    public function getFieldDeliveryAtAttribute()
    {
        return $this->{$this->field_delivery_at()};
    }

    public static function field_status_bersih()
    {
        return 'transaksi_bersih';
    }

    public function getFieldStatusBersihAttribute()
    {
        return $this->{$this->field_status_bersih()};
    }

    public function getFieldStatusBersihNameAttribute()
    {
        return TransactionType::getDescription($this->getFieldStatusBersihAttribute());
    }

    public static function field_nama_linen()
    {
        return 'view_linen_nama';
    }

    public static function field_total()
    {
        return 'transaksi_total';
    }

    public function getFieldTotalAttribute()
    {
        return $this->{$this->field_total()};
    }

    public static function field_rs_ori()
    {
        return 'transaksi_rs_ori';
    }

    public function getFieldRsOriAttribute()
    {
        return $this->{$this->field_rs_ori()};
    }

    public static function field_rs_scan()
    {
        return 'transaksi_rs_scan';
    }

    public function getFieldRsScanAttribute()
    {
        return $this->{$this->field_rs_scan()};
    }

    public static function field_uuid_barcode()
    {
        return 'transaksi_uuid_barcode';
    }

    public function getFieldUuidBarcodeAttribute()
    {
        return $this->{$this->field_uuid_barcode()};
    }

    public static function field_uuid_delivery()
    {
        return 'transaksi_uuid_delivery';
    }

    public function getFieldUuidDeliveryAttribute()
    {
        return $this->{$this->field_uuid_delivery()};
    }

    public static function field_grouping()
    {
        return 'transaksi_grouping';
    }

    public function getFieldGroupingAttribute()
    {
        return $this->{$this->field_grouping()};
    }
}
