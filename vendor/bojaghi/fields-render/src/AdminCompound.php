<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender\Filter as F;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender\Render as R;

/**
 * Mix of fields often re-used in administration screen.
 */
class AdminCompound
{
    /**
     * Create p.description and text
     *
     * @param string       $text
     * @param string|array $attrs
     *
     * @return string
     */
    public static function description(string $text, string|array $attrs = ''): string
    {
        return R::open('p', F::canonAttrs($attrs, 'class=description')) .
            wp_kses($text, F::ksesAttrs('p__description')) .
            R::close();
    }

    /**
     * You may draw various selectable fields.
     *
     * @param array        $choices Numeric, or associative array for user-choice.
     * @param string|array $value   Selected value(s).
     * @param string       $style   One of 'checkbox', 'radio', and 'select'.
     * @param array        $attrs   Attributes to the field tag.
     * @param array        $args    Optional arguments for displaying fields.
     *
     * @return string
     */
    public static function choice(array $choices, string|array $value, string $style = 'select', array $attrs = [], array $args = []): string
    {
        $output = '';

        if (wp_is_numeric_array($choices)) {
            $choices = array_combine($choices, $choices);
        }

        $args = wp_parse_args(
            $args,
            [
                'orientation' => 'vertical', // or 'horizontal'.
            ],
        );

        if (in_array($style, ['checkbox', 'radio'], true)) {
            $style = [
                'list-type: none;',
                'margin: 0;',
                'padding: 0;',
            ];
            if ('horizontal' === $args['orientation']) {
                $style[] = 'display: flex;';
            }
            /* e.g.
             * <ul style="list-type: none; margin: 0; padding: 0;">
             *   <li>
             *     <input type="checkbox|radio" id="..." name="..." value="..."/>
             *   <li>
             *   ...
             * </ul>
             */
            R::open('ul', F::canonAttrs($attrs, ['style' => $style]));

            foreach ($choices as $val => $label) {
                R::open('li');

                if (isset($attrs['id'])) {
                    $attrs['id'] .= '-' . $val;
                }
                if ('checkbox' === $style) {
                    R::checkbox($label, $val == $value, $attrs);
                } else {
                    R::radio($label, $val == $value, $attrs);
                }

                R::close();
            }

            R::close();
        } elseif ('select' === $style) {
            // Select: default. To allow multi-selection, add 'multiple' to $attrs.
            $output = R::select($choices, $value, $attrs);
        }

        return $output;
    }
}
