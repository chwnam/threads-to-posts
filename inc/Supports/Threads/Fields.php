<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class Fields
{
    const _ALL_FIELDS = [];
    const ALL         = '__all__';
    const ID          = 'id';
    const USERNAME    = 'username';

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