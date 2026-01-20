<?php

return [
    'secret' => env('JWT_SECRET', 'your-secret-key'),
    'ttl' => env('JWT_TTL', 60), // Time to live in minutes
    'algo' => env('JWT_ALGO', 'HS256'),
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 10), // Grace period for blacklisted tokens
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
];