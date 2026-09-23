<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class RsStatusEnum extends Enum
{
    use EnumTrait;

    const FREE = 'FREE';

    const DEDICATED = 'DEDICATED';

    const GROUP = 'GROUP';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::FREE => 'Free',
            self::DEDICATED => 'Dedicated',
            self::GROUP => 'Group',
            default => parent::getDescription($value),
        };
    }
}
