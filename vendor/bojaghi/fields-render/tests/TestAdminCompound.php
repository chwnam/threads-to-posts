<?php

namespace Bojaghi\FieldsRender\Tests;

use Bojaghi\FieldsRender\AdminCompound as AC;

class TestAdminCompound extends \WP_UnitTestCase
{
    public function test_description(): void
    {
        $result = AC::description('I am testing now!', ['id' => 'test', 'class' => 'my-class']);

        $this->assertIsString($result);
        $this->assertStringStartsWith('<p ', $result);
        $this->assertStringContainsString('id="test"', $result);
        $this->assertStringContainsString('class="', $result);
        $this->assertStringContainsString('description', $result);
        $this->assertStringContainsString('my-class', $result);
        $this->assertStringContainsString('>I am testing now!</p>', $result);
        $this->assertStringEndsWIth('</p>', $result);
    }

    /**
     * @dataProvider providerChoices
     *
     * @param string       $expected
     * @param array        $choices
     * @param string|array $value
     * @param string       $style
     * @param array        $attrs
     * @param array        $args
     *
     * @return void
     */
    public function test_choice(string $expected, array $choices, string|array $value, string $style, array $attrs, array $args): void
    {
        $this->assertEquals($expected, AC::choice($choices, $value, $style, $attrs, $args));
    }

    protected function providerChoices(): array
    {
        // TODO: add test cases
        return [
            'type=checkbox' => [
                'expected' => '',
                'choices'  => [],
                'value'    => '',
                'style'    => 'checkbox',
                'attrs'    => [],
                'args'     => [],
            ],
            'type=radio'    => [
                'expected' => '',
                'choices'  => [],
                'value'    => '',
                'style'    => 'radio',
                'attrs'    => [],
                'args'     => [],
            ],
            'type=select'   => [
                'expected' => '<select></select>',
                'choices'  => [],
                'value'    => '',
                'style'    => 'select',
                'attrs'    => [],
                'args'     => [],
            ],
        ];
    }
}