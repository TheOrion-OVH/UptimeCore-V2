<?php

return [
    'check_interval' => env('MONITOR_CHECK_INTERVAL', 60),
    'default_timeout' => env('MONITOR_DEFAULT_TIMEOUT', 10),
    'max_retries' => env('MONITOR_MAX_RETRIES', 3),
    
    'types' => [
        'http' => 'HTTP/HTTPS',
        'ping' => 'Ping (ICMP)',
        'tcp' => 'TCP Port',
        'dns' => 'DNS',
        'ssl' => 'SSL/TLS',
    ],
    
    'intervals' => [
        30 => '30 secondes',
        60 => '1 minute',
        300 => '5 minutes',
        600 => '10 minutes',
        1800 => '30 minutes',
    ],
];

