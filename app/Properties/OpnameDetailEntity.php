<?php

namespace App\Properties;

trait OpnameDetailEntity
{
    public static function field_primary(): string { return 'opname_detail_id'; }
    public static function field_opname(): string { return 'opname_detail_id_opname'; }
    public static function field_rfid(): string { return 'opname_detail_rfid'; }
    public static function field_code(): string { return 'opname_detail_code'; }
    public static function field_transaksi(): string { return 'opname_detail_transaksi'; }
    public static function field_proses(): string { return 'opname_detail_proses'; }
    public static function field_hilang(): string { return 'opname_detail_hilang'; }
    public static function field_ketemu(): string { return 'opname_detail_ketemu'; }
    public static function field_scan_rs(): string { return 'opname_detail_scan_rs'; }
    public static function field_sync(): string { return 'opname_detail_sync'; }
    public static function field_reff(): string { return 'opname_detail_reff'; }
    public static function field_scan_by(): string { return 'opname_detail_scan_by'; }
    public static function field_waktu(): string { return 'opname_detail_waktu'; }
    public static function field_created_at(): string { return 'opname_detail_created_at'; }
}
