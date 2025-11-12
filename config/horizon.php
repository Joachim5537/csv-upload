<?php

use Illuminate\Support\Str;

return [

    'name' => env('HORIZON_NAME'),
    'domain' => env('HORIZON_DOMAIN'),
    'path' => env('HORIZON_PATH', 'horizon'),
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'),
    'middleware' => ['web'],

    // Wait time thresholds for long jobs
    'waits' => [
        'redis:default' => 60,
    ],

    // How long Horizon keeps job history (in minutes)
    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    'memory_limit' => 1024, // Master supervisor memory

    // Worker configuration
    'defaults' => [
        'supervisor-uploads' => [
            'connection' => 'redis',
            'queue' => ['uploads'], // dedicate a queue for CSV uploads
            'balance' => 'auto',
            'maxProcesses' => 1,    // dedicate a single worker
            'memory' => 1024,        // 1 GB per worker
            'tries' => 3,           // retry 3 times on failure
            'timeout' => 600,       // 10 minutes per job
            'maxTime' => 3600,      // Max runtime of worker
            'maxJobs' => 100,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-uploads' => [
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
        ],
        'local' => [
            'supervisor-uploads' => [
                'maxProcesses' => 1,
            ],
        ],
    ],
];
