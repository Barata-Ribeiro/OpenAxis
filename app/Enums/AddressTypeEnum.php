<?php

namespace App\Enums;

enum AddressTypeEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case BILLING = 'billing';
    case SHIPPING = 'shipping';
    case BILLING_AND_SHIPPING = 'billing_and_shipping';
    case OTHER = 'other';

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
            self::BILLING => 'Billing Address',
            self::SHIPPING => 'Shipping Address',
            self::BILLING_AND_SHIPPING => 'Billing and Shipping Address',
            self::OTHER => 'Other Address',
        };
    }
}
