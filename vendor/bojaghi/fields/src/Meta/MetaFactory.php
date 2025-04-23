<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Meta;

use Chwnam\ThreadsToPosts\Vendor\JetBrains\PhpStorm\ArrayShape;

class MetaFactory
{
    public const ARGS_SHAPE = [
        'object_subtype'    => 'string',

        /**
         * type:
         *   - string, boolean, integer, number, array, and object.
         *   - array: indexed array.
         *   - object: object and associative array.
         */
        'type'              => 'string',

        'description'       => 'string',
        'single'            => 'bool',
        'default'           => 'mixed',
        'sanitize_callback' => 'callable|null',
        'auth_callback'     => 'callable|null',
        'show_in_rest'      => 'array|bool',
        'revisions_enabled' => 'bool',

        /**
         * filter:
         *   - called within get() method.
         */
        'get_filter'        => 'callable|null',
    ];

    /** @var array<string, Meta> */
    protected static array $store = [];

    /** @var array extended arguments */
    protected static array $extended = [];

    public static function add(
        string       $key,
        string       $objectType = 'post',
        #[ArrayShape(self::ARGS_SHAPE)]
        array|string $args = [],
    ): bool
    {
        $args  = static::parseArgs($args);
        $index = static::makeIndex($objectType, $args['object_subtype'], $key);

        static::$extended[$index] = [
            'get_filter' => $args['get_filter'],
        ];

        return register_meta($objectType, $key, $args);
    }

    protected static function parseArgs(array|string $args): array
    {
        return wp_parse_args(
            $args,
            [
                'object_subtype'    => '',
                'type'              => 'string',
                'description'       => '',
                'single'            => true,
                'default'           => '',
                'sanitize_callback' => null,
                'auth_callback'     => null,
                'show_in_rest'      => false,
                'revisions_enabled' => false,
                'get_filter'        => null,
            ],
        );
    }

    protected static function makeIndex(string $objectType, string $objectSubtype, string $key): string
    {
        return "$objectType:$objectSubtype:$key";
    }

    public static function get(string $key, string $objectType = 'post', string $objectSubtype = ''): Meta
    {
        global $wp_meta_keys;

        $index = static::makeIndex($objectType, $objectSubtype, $key);

        if (!isset(static::$store[$index])) {
            $args = array_merge(
                static::parseArgs($wp_meta_keys[$objectType][$objectSubtype][$key] ?? ''),
                static::$extended[$index]
            );

            static::$store[$index] = new Meta($key, $objectType, $args);
        }

        return static::$store[$index];
    }

    public static function remove(
        string $key,
        string $objectType = 'post',
        string $objectSubtype = '',
    ): bool
    {
        unset(static::$store[static::makeIndex($objectType, $objectSubtype, $key)]);

        return unregister_meta_key($objectType, $key, $objectSubtype);
    }
}
