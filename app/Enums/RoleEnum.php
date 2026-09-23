<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class RoleEnum extends Enum
{
    use EnumTrait;

    const USER = 'user';

    const EDITOR = 'editor';

    const ADMIN = 'admin';

    const DEVELOPER = 'developer';

    const RS = 'rs';

    const LAUNDRY = 'laundry';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ADMIN => 'Administrator Utama',
            self::EDITOR => 'Editor',
            self::DEVELOPER => 'Developer',
            self::USER => 'Pengguna Biasa',
            self::RS => 'Rumah Sakit',
            self::LAUNDRY => 'Petugas Laundry',
            default => parent::getDescription($value),
        };
    }
}
