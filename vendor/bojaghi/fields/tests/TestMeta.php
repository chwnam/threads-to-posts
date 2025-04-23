<?php

namespace Bojaghi\Fields\Tests;

use Bojaghi\Fields\Meta\Meta;
use Bojaghi\Fields\Meta\MetaFactory;

class TestMeta extends \WP_UnitTestCase
{
    public function testMeta(): void
    {
        // Test if register_meta() is successful.
        $returned = MetaFactory::add(
            key: '_test_foo',
            args: [
                'object_subtype' => 'post',
                'type'           => 'string',
                'description'    => 'Foo custom field',
                'single'         => true,
                'default'        => '',
                'show_in_rest'   => false,
                'get_filter'     => fn($v) => $v === 'baz' ? '(baz)' : $v,
            ],
        );
        $this->assertTrue($returned);

        // Check twice.
        $exists = registered_meta_key_exists(
            'post',
            '_test_foo',
            'post',
        );
        $this->assertTrue($exists);

        // Test meta field
        $meta = MetaFactory::get('_test_foo', 'post', 'post');
        $this->assertInstanceOf(Meta::class, $meta);

        // Test $meta->getKey();
        $this->assertEquals('_test_foo', $meta->getKey());

        // Test $meta->getObjectType();
        $this->assertEquals('post', $meta->getObjectType());

        global $wpdb;

        // Test $meta->add();
        $post = $this->factory()->post->create_and_get(['post_type' => 'post']);
        $meta->add($post, 'foo');
        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM $wpdb->postmeta WHERE post_id=%d AND meta_key=%s",
                $post->ID,
                '_test_foo',
            ),
        );
        $this->assertEquals('foo', $value);

        // Test $meta->get()
        $this->assertEquals('foo', $meta->get($post));

        // Test $meta->update()
        $meta->update($post, 'bar');
        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM $wpdb->postmeta WHERE post_id=%d AND meta_key=%s",
                $post->ID,
                '_test_foo',
            ),
        );
        $this->assertEquals('bar', $value);
        $this->assertEquals('bar', $meta->get($post));

        // Test $meta->delete()
        $value = (int)$wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id=%d AND meta_key=%s",
                $post->ID,
                '_test_foo',
            ),
        );
        $this->assertEquals(1, $value);
        $meta->delete($post);
        $value = (int)$wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id=%d AND meta_key=%s",
                $post->ID,
                '_test_foo',
            ),
        );
        $this->assertEquals(0, $value);

        // Test get_filter
        $meta->update($post, 'baz');
        $actual = $meta->get($post);
        $this->assertEquals('(baz)', $actual);
    }
}
