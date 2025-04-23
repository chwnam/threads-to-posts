<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Option;

use Chwnam\ThreadsToPosts\Vendor\JetBrains\PhpStorm\ArrayShape;

class OptionFactory
{
    public const ARGS_SHAPE = [
        /**
         * Value type
         *   - string, boolean, integer, number, array, and object.
         *   - array: indexed array.
         *   - object: object and associative array.
         */
        'type'              => 'string',

        /**
         * Group
         * - REQUIRED.
         */
        'group'             => 'string',
        'label'             => 'string',
        'description'       => 'string',
        'sanitize_callback' => 'callable|null',
        'show_in_rest'      => 'array|bool',
        'default'           => 'mixed',
        'autoload'          => 'boolean',

        /**
         * filter:
         *   - called within get() method.
         */
        'get_filter'        => 'callable|null',
    ];

    /** @var array<string, Option> */
    protected static array $store = [];

    public static function add(
        string       $name,
        #[ArrayShape(self::ARGS_SHAPE)]
        array|string $args = [],
    ): void
    {
        $args = static::parseArgs($args);

        if (empty($args['group'])) {
            wp_die('Key \'group\' in $args is required.');
        }

        register_setting($args['group'], $name, $args);
    }

    protected static function parseArgs(array|string $args): array
    {
        return wp_parse_args(
            $args,
            [
                'type'              => 'string',
                'group'             => 'fallback_option_group',
                'label'             => '',
                'description'       => '',
                'sanitize_callback' => null,
                'show_in_rest'      => false,
                'autoload'          => true,
                'get_filter'        => null,
            ],
        );
    }

    public static function get(string $name): Option
    {
        global $wp_registered_settings;

        if (!isset(static::$store[$name])) {
            $args = static::parseArgs($wp_registered_settings[$name] ?? '');

            static::$store[$name] = new Option($name, $args);
        }

        return static::$store[$name];
    }

    public static function remove(string $name): void
    {
        global $wp_registered_settings;

        if (isset(static::$store[$name])) {
            unset(static::$store[$name]);
            $args = $wp_registered_settings[$name];
            unregister_setting($args['group'], $name);
        }
    }
}
