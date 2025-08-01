<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender;

class Render
{
    private static array $stack = [];

    /**
     * Output input[type="checkbox"]
     *
     * @param string       $label
     * @param bool         $checked
     * @param array|string $attrs
     *
     * @return string
     */
    public static function checkbox(string $label, bool $checked, array|string $attrs = ''): string
    {
        $attrs            = wp_parse_args($attrs);
        $attrs['checked'] = $checked;
        $attrs['type']    = 'checkbox';

        return self::input($attrs) . self::label($label, ['for' => $attrs['id'] ?? '']);
    }

    /**
     * Create simple input
     *
     * @param array|string $attrs
     *
     * @return string
     */
    public static function input(array|string $attrs = ''): string
    {
        return self::open('input', $attrs, true);
    }

    /**
     * Output label
     *
     * @param array|string $attrs
     * @param string       $text
     *
     * @return string
     */
    public static function label(string $text = '', array|string $attrs = ''): string
    {
        return self::open('label', $attrs) . wp_kses($text, Filter::ksesAttrs('label__inner')) . self::close();
    }

    /**
     * Open a tag
     *
     * @param string       $tag
     * @param array|string $attrs
     * @param bool         $enclosed
     *
     * @return string
     */
    public static function open(string $tag, array|string $attrs = '', bool $enclosed = false): string
    {
        $tag   = sanitize_key($tag);
        $attrs = self::attrs($attrs);

        if ($enclosed) {
            $e = '/';
        } else {
            $e             = '';
            self::$stack[] = $tag;
        }

        return $tag ? "<$tag$attrs$e>" : '';
    }

    /**
     * Close opened tag.
     *
     * @return string
     */
    public static function close(): string
    {
        $tag = array_pop(self::$stack);

        return $tag ? "</$tag>" : '';
    }

    /**
     * Format attributes
     *
     * @param array|string $attrs
     *
     * @return string
     */
    public static function attrs(array|string $attrs = ''): string
    {
        $buffer = [];
        $attrs  = wp_parse_args($attrs);

        foreach ($attrs as $key => $value) {
            /** @link https://html.spec.whatwg.org/multipage/indices.html#attributes-3 */
            $key = sanitize_key($key);
            [$key, $value] = match ($key) {
                'class'                                 => Filter::filterClass($key, $value),
                //
                // URLS
                'action',
                'cite',
                'data',
                'formaction',
                'href',
                'itemid',
                'itemprop',
                'itemtype',
                'manifest',
                'ping',
                'poster',
                'src'                                   => Filter::filterUrl($key, $value),
                //
                // Boolean-like
                'allowfullscreen',
                'alpha',
                'async',
                'autofocus',
                'autoplay',
                'checked',
                'controls',
                'default',
                'defer',
                'disabled',
                'formnovalidate',
                'inert',
                'ismap',
                'itemscope',
                'loop',
                'multiple',
                'muted',
                'nomodule',
                'novalidate',
                'open',
                'playsinline',
                'readonly',
                'required',
                'reversed',
                'selected',
                'shadowrootclonable',
                'shadowrootdelegatesfocus',
                'shadowrootserializableallowfullscreen' => Filter::filterBool($key, $value),
                //
                // default
                default                                 => Filter::filterGeneric($key, $value),
            };

            if ($key) {
                $buffer[] = "$key=\"$value\"";
            }
        }

        return $buffer ? (' ' . implode(' ', $buffer)) : '';
    }

    public static function flushStack(): void
    {
        self::$stack = [];
    }

    /**
     * Get tag stack.
     *
     * @return array
     */
    public static function getStack(): array
    {
        return self::$stack;
    }

    /**
     * Output input[type="radio"]
     *
     * @param string       $label
     * @param bool         $checked
     * @param array|string $attrs
     *
     * @return string
     */
    public static function radio(string $label, bool $checked, array|string $attrs = ''): string
    {
        $attrs            = wp_parse_args($attrs);
        $attrs['checked'] = $checked;
        $attrs['type']    = 'radio';

        return self::input($attrs) . self::label($label, ['for' => $attrs['id'] ?? '']);
    }

    /**
     * Output select - option tags
     *
     * @param array        $options
     * @param string       $selected
     * @param array|string $selectAttrs
     *
     * @return string
     *
     * @example select(
     *     [
     *         'value' => 'Label',
     *         'OptGroup Label' => [
     *            'value2' => 'Label2',
     *         ],
     *     ],
     *     ....
     * )
     */
    public static function select(array $options, string $selected = '', array|string $selectAttrs = ''): string
    {
        $output = self::open('select', $selectAttrs);

        foreach ($options as $value => $text) {
            if (is_array($text)) {
                $output .= self::open('optgroup', "label=$value");
                foreach ($text as $inValue => $inText) {
                    $output .= self::open('option', ['value' => $inValue, 'selected' => $inValue == $selected]);
                    $output .= wp_kses($inText, Filter::ksesAttrs('option__text'));
                    $output .= self::close();
                }
                $output .= self::close();
            } else {
                $output .= self::open('option', ['value' => $value, 'selected' => $value == $selected]);
                $output .= wp_kses($text, Filter::ksesAttrs('option__text'));
                $output .= self::close();
            }
        }

        return $output . self::close();
    }

    /**
     * Output textarea
     *
     * @param string       $text
     * @param string|array $attrs
     *
     * @return string
     */
    public static function textarea(string $text = '', string|array $attrs = ''): string
    {
        return self::open('textarea', $attrs) . esc_textarea($text) . self::close();
    }
}
