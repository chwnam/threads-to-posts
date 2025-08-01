<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Helper;

class Helper
{
    /**
     * Commonly used loadConfig method
     *
     * @param string|array $config If $config is string, it is treated as a path to config file.
     *                             If #config is array, it is configuration itself.
     *
     * @return array
     */
    public static function loadConfig(string|array $config): array
    {
        $output = [];

        if (is_string($config) && file_exists($config) && is_readable($config)) {
            $output = include $config;
        } elseif (is_array($config)) {
            $output = $config;
        }

        return $output;
    }

    /**
     * Separates an input array into two arrays: one associative array and the other indexed array.
     *
     * @param array $input The input array to be separated.
     *
     * @return array An array containing two sub-arrays:
     *                the first with associative elements, and the second with numeric-indexed values.
     */
    public static function separateArray(array $input): array
    {
        $associative = [];
        $indexed     = [];
        $index       = 0;

        foreach ($input as $key => $value) {
            if (is_int($key) && $index === $key) {
                $indexed[$index++] = $value;
            } else {
                $associative[$key] = $value;
            }
        }

        return [
            $associative,
            array_values($indexed),
        ];
    }

    /**
     * Prefix helper
     *
     * @param string $input
     * @param string $prefix
     *
     * @return string
     */
    public static function prefixed(string $input, string $prefix): string
    {
        return str_starts_with($input, $prefix) ? $input : ($prefix . $input);
    }

    /**
     * Un-prefix string
     *
     * @param string $input
     * @param string $prefix
     *
     * @return string
     */
    public static function unprefixed(string $input, string $prefix): string
    {
        return str_starts_with($input, $prefix) ? substr($input, strlen($prefix)) : $input;
    }

    /**
     * Convert input string to camel-cased string
     *
     * @param string $input
     *
     * @return string
     */
    public static function toCamelCase(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $input))));
    }

    /**
     * Converts a given camelCase or PascalCase string to snake_case.
     *
     * @param string $input The input string in camelCase or PascalCase format.
     *
     * @return string The converted string in snake_case format.
     */
    public static function toSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
