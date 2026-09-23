<?php

namespace App\Properties;

trait OpnameEntity
{
    public static function field_primary(): string { return 'opname_id'; }
    public static function field_name(): string { return 'opname_nama'; }
    public static function field_mulai(): string { return 'opname_mulai'; }
    public static function field_selesai(): string { return 'opname_selesai'; }
    public static function field_rs_id(): string { return 'opname_id_rs'; }
    public static function field_status(): string { return 'opname_status'; }
    public static function field_capture(): string { return 'opname_capture'; }
    public static function field_created_at(): string { return 'opname_created_at'; }
    public static function field_updated_at(): string { return 'opname_updated_at'; }
    public static function field_created_by(): string { return 'opname_created_by'; }
    public static function field_updated_by(): string { return 'opname_updated_by'; }
}
