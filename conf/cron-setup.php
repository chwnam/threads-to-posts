<?php

// No direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    'main_file' => TTP_MAIN,
    [
        'timestamp' => 0,
        'schedule'  => 'daily',
        'hook'      => 'ttp_long_live_token_check',
    ],
];
