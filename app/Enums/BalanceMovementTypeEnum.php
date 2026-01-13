<?php

namespace App\Enums;

enum BalanceMovementTypeEnum: string
{
    case INPUT = 'input';
    case OUTPUT = 'output';
    case TRANSFER = 'transfer';

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
            self::INPUT => 'Input',
            self::OUTPUT => 'Output',
            self::TRANSFER => 'Transfer',
        };
    }
}
