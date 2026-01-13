<?php

namespace App\Enums;

enum ReceivableStatusEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case PENDING = 'pending';
    case RECEIVED = 'received';
    case CANCELED = 'canceled';

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
            self::PENDING => 'Pending',
            self::RECEIVED => 'Received',
            self::CANCELED => 'Canceled',
        };
    }
}
