<?php

namespace App\Enums;

enum ShippedOrderStatusEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case POSTED = 'posted';
    case IN_TRANSIT = 'in_transit';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';

    /**
     * Get a human-readable label for this enum case.
     *
     * Provides a display-friendly string intended for UI, logs, or selection
     * lists. Implementations may return a static value, a translated string,
     * or a computed label based on the enum case.
     *
     * @return string Human-readable label for the enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::POSTED => 'Posted',
            self::IN_TRANSIT => 'In Transit',
            self::DELIVERED => 'Delivered',
            self::RETURNED => 'Returned',
        };
    }
}
