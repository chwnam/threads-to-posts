<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\Modules;

class CPT
{
    public function __construct(public int $foo)
    {
        if ( ! post_type_exists('dummy_type')) {
            register_post_type(
                'dummy_type',
                [
                    'label' => 'Dummy Type',
                ],
            );
        }
    }
}
