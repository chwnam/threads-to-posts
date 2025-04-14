<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class Fields
{
    const ALL                = '__all__';
    const ID                 = 'id';
    const CHILDREN           = 'children';
    const GIF_URL            = 'gif_url';
    const IS_QUOTE_POST      = 'is_quote_post';
    const MEDIA_PRODUCT_TYPE = 'media_product_type';
    const MEDIA_TYPE         = 'media_type';
    const MEDIA_URL          = 'media_url';
    const PERMALINK          = 'permalink';
    const SHORTCODE          = 'shortcode';
    const TEXT               = 'text';
    const THUMBNAIL_URL      = 'thumbnail_url';
    const TIMESTAMP          = 'timestamp';
    const USERNAME           = 'username';

    const _ALL_FIELDS = [];

    public static function getFields(...$fields): string
    {
        if (in_array(self::ALL, $fields)) {
            return implode(',', static::_ALL_FIELDS);
        }

        return implode(
            ',',
            array_unique(
                array_filter($fields, fn($field) => in_array($field, static::_ALL_FIELDS))
            )
        );
    }
}