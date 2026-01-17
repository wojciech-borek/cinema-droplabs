<?php

declare(strict_types=1);

namespace App\Enum;

enum AllocationStatus: string
{
    case HELD = 'HELD';
    case CONFIRMED = 'CONFIRMED';
    case EXPIRED = 'EXPIRED';
    case CANCELLED = 'CANCELLED';
}
