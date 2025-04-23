<?php

namespace Bojaghi\FieldsRender\Tests;

use Bojaghi\FieldsRender\Filter;
use Bojaghi\FieldsRender\Render as R;
use \WP_UnitTestCase;

class TestFilter extends WP_UnitTestCase
{
    public function tearDown(): void
    {
        R::flushStack();
    }

    public function test_canonAttrs()
    {
        $this->assertEquals(
            ['class' => 'btn', 'id' => 'submit'],
            Filter::canonAttrs('class=btn&id=submit'),
        );

        $this->assertEquals(
            [
                'class' => 'btn btn-primary mt-2 mb-2 px-0',
                'id'    => 'submit',
            ],
            Filter::canonAttrs(
                [
                    'class' => [
                        'btn',
                        'btn-primary',
                        'mt-2 mb-2 px-0',
                    ],
                    'id'    => 'submit'
                ],
            ),
        );
    }

    public function test_deepAttrFilter()
    {
        $input    = "a a b   c c d d e   c e f a f";
        $function = function ($value) {
            $map = [
                'a' => 1,
                'b' => 2,
                'c' => 3,
                'd' => 4,
                'e' => 5,
                'f' => 6,
            ];
            return $value . $map[$value];
        };

        $this->assertEquals(
            "a1 b2 c3 d4 e5 f6",
            Filter::deepAttrFilter($input, $function),
        );
    }
}