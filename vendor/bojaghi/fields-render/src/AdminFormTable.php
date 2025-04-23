<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender\Filter as F;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\FieldsRender\Render as R;

/**
 * table[class="form-table"] is really used frequently.
 */
class AdminFormTable
{
    public static function tableOpen(string|array $attrs = ""): string
    {
        return R::open('table', F::canonAttrs($attrs, 'class=form-table&role=presentation'));
    }

    public static function trOpen(string|array $attrs = ""): string
    {
        return R::open('tr', $attrs);
    }

    public static function thOpen(string|array $attrs = ""): string
    {
        return R::open('th', F::canonAttrs($attrs, 'scope=row'));
    }

    public static function tdOpen(string|array $attrs = ""): string
    {
        return R::open('td', $attrs);
    }

    public static function close(): string
    {
        return R::close();
    }
}
