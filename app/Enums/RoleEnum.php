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

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::ADMIN => 'Administrator Utama',
            self::EDITOR => 'Editor',
            self::DEVELOPER => 'Developer',
            self::USER => 'Pengguna Biasa',
            default => parent::getDescription($value),
        };
    }
}
