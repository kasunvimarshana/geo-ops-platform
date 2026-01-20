<?php

namespace App\Enums;

/**
 * User Role Enum
 *
 * Defines all available user roles in the system.
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case DRIVER = 'driver';
    case BROKER = 'broker';
    case ACCOUNTANT = 'accountant';

    /**
     * Get all role values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all role values as a comma-separated string.
     *
     * @return string
     */
    public static function valuesString(): string
    {
        return implode(',', self::values());
    }
}
