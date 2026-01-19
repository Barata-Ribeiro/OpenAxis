<?php

namespace App\Enums;

enum ClientTypeEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case INDIVIDUAL = 'individual';
    case COMPANY = 'company';

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
            self::INDIVIDUAL => 'Individual',
            self::COMPANY => 'Company',
        };
    }
}
