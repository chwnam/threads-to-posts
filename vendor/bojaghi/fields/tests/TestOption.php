<?php

namespace Bojaghi\Fields\Tests;

use Bojaghi\Fields\Option\Option;
use Bojaghi\Fields\Option\OptionFactory;

class TestOption extends \WP_UnitTestCase
{
    public function testOption(): void
    {
        OptionFactory::add(
            name: 'test_foo',
            args: [
                'type'         => 'string',
                'group'        => 'test_group',
                'label'        => 'TEST',
                'description'  => 'test option',
                'show_in_rest' => false,
                'default'      => '',
                'autoload'     => false,
                'get_filter'   => fn($v) => 'baz' === $v ? '(baz)' : $v,
            ],
        );

        // Check if register_setting() is successful.
        $settings = get_registered_settings();
        $this->assertArrayHasKey('test_foo', $settings);

        // Test option field
        $option = OptionFactory::get('test_foo');
        $this->assertInstanceOf(Option::class, $option);

        // Test $option->getKey()
        $this->assertEquals('test_foo', $option->getKey());

        global $wpdb;

        // Test $option->add();
        $option->add('foo');
        $value = $wpdb->get_var(
            $wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name=%s", 'test_foo'),
        );
        $this->assertEquals('foo', $value);

        // Test $option->get()
        $this->assertEquals('foo', $option->get());

        // Test $option->update()
        $option->update('bar');
        $value = $wpdb->get_var(
            $wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name=%s", 'test_foo'),
        );
        $this->assertEquals('bar', $value);
        $this->assertEquals('bar', $option->get());

        // Test $option->delete()
        $value = (int)$wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->options WHERE option_name=%s", 'test_foo'),
        );
        $this->assertEquals(1, $value);
        $option->delete();
        $value = (int)$wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->options WHERE option_name=%s", 'test_foo'),
        );
        $this->assertEquals(0, $value);

        // Test get_filter
        $option->update('baz');
        $actual = $option->get();
        $this->assertEquals('(baz)', $actual);
    }
}