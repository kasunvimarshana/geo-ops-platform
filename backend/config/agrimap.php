<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Default Settings
    |--------------------------------------------------------------------------
    |
    | These settings define the default configuration for the GeoOps platform.
    | These can be overridden by organization-specific settings.
    |
    */

    'defaults' => [
        'currency' => env('DEFAULT_CURRENCY', 'LKR'),
        'tax_percentage' => env('DEFAULT_TAX_PERCENTAGE', 0),
        'subscription_package' => env('DEFAULT_SUBSCRIPTION_PACKAGE', 'free'),
        'invoice_prefix' => env('INVOICE_PREFIX', 'INV'),
        'language' => env('APP_LOCALE', 'en'),
        'timezone' => env('APP_TIMEZONE', 'Asia/Colombo'),
    ],

    /*
    |--------------------------------------------------------------------------
    | GPS and Location Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for GPS tracking and land measurement accuracy.
    |
    */

    'gps' => [
        // Minimum accuracy threshold in meters (locations less accurate will be rejected)
        'accuracy_threshold' => env('GPS_ACCURACY_THRESHOLD', 20),
        
        // Interval for tracking updates in seconds
        'tracking_interval' => env('TRACKING_INTERVAL_SECONDS', 60),
        
        // Minimum polygon points required for a valid measurement
        'min_polygon_points' => 3,
        
        // Maximum polygon points allowed
        'max_polygon_points' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for file uploads (receipts, documents, etc.).
    |
    */

    'uploads' => [
        // Maximum file size in KB
        'max_receipt_size' => env('MAX_RECEIPT_FILE_SIZE', 5120), // 5MB
        
        // Allowed file extensions
        'allowed_receipt_extensions' => explode(',', env('ALLOWED_RECEIPT_EXTENSIONS', 'jpg,jpeg,png,pdf')),
        
        // Storage path for receipts
        'receipt_path' => 'receipts',
        
        // Storage path for invoices
        'invoice_path' => 'invoices',
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Limits
    |--------------------------------------------------------------------------
    |
    | Define usage limits for each subscription package.
    | Use -1 for unlimited access.
    |
    */

    'subscription_limits' => [
        'free' => [
            'measurements_per_month' => env('FREE_MEASUREMENTS_PER_MONTH', 10),
            'drivers' => env('FREE_DRIVERS_LIMIT', 1),
            'pdf_exports_per_month' => env('FREE_PDF_EXPORTS_PER_MONTH', 5),
            'jobs_per_month' => env('FREE_JOBS_PER_MONTH', 20),
            'customers' => env('FREE_CUSTOMERS_LIMIT', 10),
        ],
        'basic' => [
            'measurements_per_month' => env('BASIC_MEASUREMENTS_PER_MONTH', 100),
            'drivers' => env('BASIC_DRIVERS_LIMIT', 3),
            'pdf_exports_per_month' => env('BASIC_PDF_EXPORTS_PER_MONTH', 50),
            'jobs_per_month' => env('BASIC_JOBS_PER_MONTH', 200),
            'customers' => env('BASIC_CUSTOMERS_LIMIT', 50),
        ],
        'pro' => [
            'measurements_per_month' => env('PRO_MEASUREMENTS_PER_MONTH', -1), // unlimited
            'drivers' => env('PRO_DRIVERS_LIMIT', -1), // unlimited
            'pdf_exports_per_month' => env('PRO_PDF_EXPORTS_PER_MONTH', -1), // unlimited
            'jobs_per_month' => env('PRO_JOBS_PER_MONTH', -1), // unlimited
            'customers' => env('PRO_CUSTOMERS_LIMIT', -1), // unlimited
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Pricing
    |--------------------------------------------------------------------------
    |
    | Define pricing for each subscription package (in LKR).
    |
    */

    'subscription_pricing' => [
        'free' => [
            'monthly' => 0,
            'annually' => 0,
        ],
        'basic' => [
            'monthly' => env('BASIC_MONTHLY_PRICE', 2500),
            'annually' => env('BASIC_ANNUAL_PRICE', 25000),
        ],
        'pro' => [
            'monthly' => env('PRO_MONTHLY_PRICE', 7500),
            'annually' => env('PRO_ANNUAL_PRICE', 75000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for invoice generation and management.
    |
    */

    'invoices' => [
        'prefix' => env('INVOICE_PREFIX', 'INV'),
        'due_days' => env('INVOICE_DUE_DAYS', 30),
        'late_fee_percentage' => env('INVOICE_LATE_FEE_PERCENTAGE', 5),
        'include_tax' => env('INVOICE_INCLUDE_TAX', false),
        'tax_percentage' => env('INVOICE_TAX_PERCENTAGE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Types and Rates
    |--------------------------------------------------------------------------
    |
    | Default service types and their default rates per acre.
    |
    */

    'service_types' => [
        'ploughing' => [
            'name' => 'Ploughing',
            'default_rate_per_acre' => env('RATE_PLOUGHING', 2500),
        ],
        'harrowing' => [
            'name' => 'Harrowing',
            'default_rate_per_acre' => env('RATE_HARROWING', 2000),
        ],
        'seeding' => [
            'name' => 'Seeding',
            'default_rate_per_acre' => env('RATE_SEEDING', 1500),
        ],
        'harvesting' => [
            'name' => 'Harvesting',
            'default_rate_per_acre' => env('RATE_HARVESTING', 5000),
        ],
        'leveling' => [
            'name' => 'Leveling',
            'default_rate_per_acre' => env('RATE_LEVELING', 3000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Status Transitions
    |--------------------------------------------------------------------------
    |
    | Define valid status transitions for jobs.
    |
    */

    'job_status_transitions' => [
        'pending' => ['assigned', 'cancelled'],
        'assigned' => ['in_progress', 'pending', 'cancelled'],
        'in_progress' => ['completed', 'assigned'],
        'completed' => ['billed'],
        'billed' => ['paid'],
        'paid' => [],
        'cancelled' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for notifications and alerts.
    |
    */

    'notifications' => [
        'subscription_expiry_warning_days' => env('SUBSCRIPTION_EXPIRY_WARNING_DAYS', 7),
        'license_expiry_warning_days' => env('LICENSE_EXPIRY_WARNING_DAYS', 30),
        'payment_due_reminder_days' => env('PAYMENT_DUE_REMINDER_DAYS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Additional security configurations.
    |
    */

    'security' => [
        'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('LOCKOUT_DURATION', 900), // 15 minutes
        'session_timeout' => env('SESSION_TIMEOUT', 7200), // 2 hours
        'require_email_verification' => env('REQUIRE_EMAIL_VERIFICATION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features.
    |
    */

    'features' => [
        'enable_offline_sync' => env('ENABLE_OFFLINE_SYNC', true),
        'enable_bluetooth_printing' => env('ENABLE_BLUETOOTH_PRINTING', true),
        'enable_email_invoices' => env('ENABLE_EMAIL_INVOICES', true),
        'enable_sms_notifications' => env('ENABLE_SMS_NOTIFICATIONS', false),
        'enable_subscription_enforcement' => env('ENABLE_SUBSCRIPTION_ENFORCEMENT', true),
        'enable_audit_logs' => env('ENABLE_AUDIT_LOGS', true),
    ],
];
