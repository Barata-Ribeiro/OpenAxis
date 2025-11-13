<?php

namespace App\Enums;

enum RoleEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case SUPER_ADMIN = 'super-admin';
    case MANAGER = 'manager';
    case VENDOR = 'vendor';
    case STOCK_CLERK = 'stock-clerk';

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
            self::SUPER_ADMIN => 'Super Admins',
            self::MANAGER => 'Managers',
            self::VENDOR => 'Vendors',
            self::STOCK_CLERK => 'Stock Clerks',
        };
    }
}
