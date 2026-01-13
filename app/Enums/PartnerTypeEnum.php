<?php

namespace App\Enums;

enum PartnerTypeEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case CLIENT = 'client';
    case SUPPLIER = 'supplier';
    case BOTH = 'both';

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
            self::CLIENT => 'Client',
            self::SUPPLIER => 'Supplier',
            self::BOTH => 'Client and Supplier',
        };
    }
}
