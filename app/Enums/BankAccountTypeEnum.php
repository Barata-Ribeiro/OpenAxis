<?php

namespace App\Enums;

enum BankAccountTypeEnum: string
{
    // case NAMEINAPP = 'name-in-database';

    case CASH = 'cash';
    case CHECKING_ACCOUNT = 'checking_account';
    case SAVINGS_ACCOUNT = 'savings_account';
    case INVESTMENT_ACCOUNT = 'investment_account';

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
            self::CASH => 'Cash',
            self::CHECKING_ACCOUNT => 'Checking Account',
            self::SAVINGS_ACCOUNT => 'Savings Account',
            self::INVESTMENT_ACCOUNT => 'Investment Account',
        };
    }
}
