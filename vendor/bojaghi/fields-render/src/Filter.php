<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender;

class Filter
{
    public static function canonAttrs(string|array $attrs, string|array $defaults = ''): array
    {
        $output   = [];
        $attrs    = wp_parse_args($attrs);
        $defaults = wp_parse_args($defaults);

        foreach ($attrs as $key => $value) {
            if (is_array($value)) {
                $output[$key] = implode(' ', array_filter($value));
            } elseif (is_string($value)) {
                $output[$key] = implode(' ', preg_split('/\s+/', $value));
            }
        }

        foreach($defaults as $key => $value) {
            if (isset($output[$key])) {
                $output[$key] .= " $value";
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }

    /**
     * Generic attribute filter
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function filterGeneric(string $key, mixed $value): array
    {
        $key    = sanitize_key($key);
        $output = self::deepAttrFilter($value, 'esc_attr');

        return [$key, $output];
    }

    /**
     * Bool-like attribute filter
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function filterBool(string $key, mixed $value): array
    {
        $key = sanitize_key($key);

        if (is_bool($value)) {
            if ($value) {
                $value = $key;
            } else {
                $key   = '';
                $value = '';
            }
        } elseif ($key) {
            if (!empty($value)) {
                $value = $key;
            } else {
                $key   = '';
                $value = '';
            }
        }

        return [$key, $value];
    }

    /**
     * Sanitize CSS class
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function filterClass(string $key, mixed $value): array
    {
        $key    = sanitize_key($key);
        $output = self::deepAttrFilter($value, 'sanitize_html_class');

        return [$key, $output];
    }

    /**
     * Deep attributes filter
     *
     * @param mixed                 $input
     * @param array|string|callable $func
     *
     * @return string
     */
    public static function deepAttrFilter(mixed $input, array|string|callable $func): string
    {
        $output = '';

        if (is_string($input)) {
            $input = preg_split('/\s+/', $input);
        } else {
            $input = (array)$input;
        }

        if (is_array($input) && is_callable($func)) {
            $output = implode(' ', array_unique(array_filter(array_map($func, $input))));
        }

        return $output;
    }

    /**
     * Sanitize URL
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function filterUrl(string $key, mixed $value): array
    {
        $key    = sanitize_key($key);
        $output = self::deepAttrFilter($value, 'esc_url');

        return [$key, $output];
    }

    public static function ksesAttrs(string $objective = ''): array
    {
        return match ($objective) {
            'label__inner'   => [
                'span' => [
                    'id'    => true,
                    'class' => true,
                    'style' => true,
                ],
            ],
            'option__text'   => [
                'a'    => [
                    'id'     => true,
                    'class'  => true,
                    'href'   => true,
                    'style'  => true,
                    'target' => true,
                ],
                'span' => [
                    'id'    => true,
                    'class' => true,
                    'style' => true,
                ],
            ],
            'p__description' => [
                'a'    => [
                    'id'     => true,
                    'class'  => true,
                    'href'   => true,
                    'style'  => true,
                    'target' => true,
                ],
                'code' => [
                    'id'    => true,
                    'class' => true,
                    'style' => true,
                ],
                'br'   => [],
                'hr'   => [
                    'id'    => true,
                    'class' => true,
                    'style' => true,
                ],
                'pre'  => [
                    'id'    => true,
                    'class' => true,
                    'style' => true,
                ],
                'span' => [
                    'id'    => true,
                    'class' => true,
                    'style' => true,
                ],
            ],
            default          => [],
        };
    }
}
