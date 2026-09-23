<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class StatusEnum extends Enum
{
    use EnumTrait;

    const PENDING = 'pending';

    const REVIEW = 'review';

    const APPROVED = 'approved';

    const REJECTED = 'rejected';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::PENDING => 'Pending',
            self::REVIEW => 'Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            default => parent::getDescription($value),
        };
    }
}
