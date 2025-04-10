<?php

if (!defined('ABSPATH')) exit;

return [
    // key => option
    'ttp_auth' => [
        'type'              => 'array',
        'group'             => 'ttp_options',
        'description'       => 'App ID, and App secret',
        'sanitize_callback' => function ($value): array {
            return [
                'app_id'     => sanitize_text_field($value['app_id'] ?? ''),
                'app_secret' => sanitize_text_field($value['app_secret'] ?? ''),
            ];
        },
        'show_in_rest'      => false,
        'default'           => [
            'app_id'     => '',
            'app_secret' => '',
        ],
        'autoload'          => false,
        'get_filter'        => null,
    ],

    'ttp_token' => [
        'type'              => 'array',
        'group'             => 'ttp_options',
        'description'       => 'Authorization token from Threads. Must be a long-lived token.',
        'sanitize_callback' => function ($value): array {
            return [
                'access_token' => sanitize_text_field($value['access_token'] ?? ''),
                'token_type'   => sanitize_text_field($value['token_type'] ?? ''),
                'expires_in'   => absint($value['expires_in'] ?? 0),
                'timestamp'    => absint($value['timestamp'] ?? 0),
                'user_id'      => sanitize_text_field($value['user_id'] ?? ''),
            ];
        },
        'show_in_rest'      => false,
        'default'           => [
            'access_token' => '',
            'token_type'   => '',
            'expires_in'   => 0,
            'timestamp'    => 0,
            'user_id'      => '',
        ],
        'autoload'          => false,
        'get_filter'        => null,
    ],
];
