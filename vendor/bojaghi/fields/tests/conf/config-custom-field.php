<?php

return [
    // object type: post
    'post' => [
        'foo' => [
            'object_subtype' => 'post',
            'type'           => 'string',
            'description'    => 'Foo custom field',
            'single'         => true,
            'default'        => 'foo-default',
            'show_in_rest'   => false,
        ],
    ],

    // object type: term
    'term' => [
        'bar' => [
            'object_subtype' => 'post_tag',
            'type'           => 'string',
            'description'    => '',
            'single'         => true,
            'default'        => 'bar-default',
            'show_in_rest'   => false,
        ],
    ],

    // object type: user
    'user' => [
        'baz' => [
            'type'           => 'string',
            'description'    => '',
            'single'         => true,
            'default'        => 'baz-default',
            'show_in_rest'   => false,
        ],
    ],
];
