<?php

declare(strict_types=1);

namespace App\Enum;

enum SeatStatus: string
{
    case AVAILABLE = 'available';
    case HELD = 'held';
    case CONFIRMED = 'confirmed';
}
