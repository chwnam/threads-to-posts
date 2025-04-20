<?php

// No direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    [
        'schedule' => 'ttp_scrap_schedule',
        'display'  => 'Threads to Posts scrap schedule (10min.)',
        'interval' => 10 * MINUTE_IN_SECONDS,
    ]
];
