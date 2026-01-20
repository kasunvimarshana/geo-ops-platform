<?php

namespace App\Constants;

class Role
{
    public const ADMIN = 'admin';
    public const OWNER = 'owner';
    public const DRIVER = 'driver';
    public const BROKER = 'broker';
    public const ACCOUNTANT = 'accountant';

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::OWNER,
            self::DRIVER,
            self::BROKER,
            self::ACCOUNTANT,
        ];
    }
}
