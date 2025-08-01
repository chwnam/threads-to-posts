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

        $args = wp_parse_args(
            $args,
            [
                'orientation' => 'vertical', // or 'horizontal'.
            ],
        );

        if (in_array($style, ['checkbox', 'radio'], true)) {
            $inlineStyle = ['list-type: none;', 'margin: 0;', 'padding: 0;'];
            if ('horizontal' === $args['orientation']) {
                $inlineStyle[] = 'display: flex;';
            }

            $value = (array)$value;

            /* e.g.
             * <ul style="list-type: none; margin: 0; padding: 0;">
             *   <li>
             *     <input type="checkbox|radio" id="..." name="..." value="..."/>
             *   <li>
             *   ...
             * </ul>
             */
            $output .= R::open('ul', F::canonAttrs($attrs, ['style' => $inlineStyle]));

            foreach ($choices as $val => $label) {
                $output .= R::open('li');

                $itemAttrs = $attrs;
                if (isset($itemAttrs['id'])) {
                    $itemAttrs['id'] .= '-' . $val;
                }
                if (isset($itemAttrs['name']) && !str_ends_with($itemAttrs['name'], '[]') && 'checkbox' === $style) {
                    $itemAttrs['name'] .= '[]';
                }
                $itemAttrs['value'] = $val;

                if ('checkbox' === $style) {
                    $output .= R::checkbox($label, in_array($val, $value), $itemAttrs);
                } else {
                    $output .= R::radio($label, in_array($val, $value), $itemAttrs);
                }

                $output .= R::close();
            }

            $output .= R::close();
        } elseif ('select' === $style) {
            // Select: default. To allow multi-selection, add 'multiple' to $attrs.
            $output = R::select($choices, $value, $attrs);
        }

        return $output;
    }
}
