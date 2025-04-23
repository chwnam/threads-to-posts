<?php

namespace Bojaghi\AdminAjax\Tests;

use Bojaghi\AdminAjax\AdminAjax;
use Bojaghi\AdminAjax\AdminPost;
use Bojaghi\AdminAjax\SubmitBase;
use WP_UnitTestCase;

class TestAdminAjax extends WP_UnitTestCase
{
    public function test_AdminAjax(): void
    {
        $switch   = false;
        $callback = function () use (&$switch) {
            $switch = true;
        };

        $ajax = new AdminAjax(
            [
                'test_hook_name_1',
                ['test_hook_name_2', 'admin@callback_2', SubmitBase::ONLY_NOPRIV, '_wp_nonce', 20],
                ['test_hook_name_3', $callback, SubmitBase::ALL_GRANTED, '_wp_nonce'],
                'checkContentType' => false,
            ],
        );

        $theProp = getAccessibleProperty(AdminAjax::class, 'items');
        $items   = $theProp->getValue($ajax);

        // 0th item should select its callback as 'test_hook_name_1'.
        // nopriv action did not set.
        $this->assertIsInt(has_action('wp_ajax_test_hook_name_1', [$ajax, 'dispatch']));
        $this->assertFalse(has_action('wp_ajax_nopriv_test_hook_name_1', [$ajax, 'dispatch']));
        $this->assertEquals('test_hook_name_1', $items['test_hook_name_1'][0]);

        // 1st item has full list, and it should have nopriv action only.
        // callback name, and nonce key, and priority should be set.
        $this->assertFalse(has_action('wp_ajax_test_hook_name_2', [$ajax, 'dispatch']));
        $this->assertEquals(20, has_action('wp_ajax_nopriv_test_hook_name_2', [$ajax, 'dispatch']));
        $this->assertEquals('admin@callback_2', $items['test_hook_name_2'][0]);
        $this->assertEquals('_wp_nonce', $items['test_hook_name_2'][1]);

        // 2nd item priv and nopriv both actions are should be set.
        $this->assertIsInt(has_action('wp_ajax_test_hook_name_3', [$ajax, 'dispatch']));
        $this->assertIsInt(has_action('wp_ajax_nopriv_test_hook_name_3', [$ajax, 'dispatch']));
        $this->assertEquals($callback, $items['test_hook_name_3'][0]);

        // Calling 2nd item, prove that nonce is verified.
        $_REQUEST = $_POST = ['_wp_nonce' => wp_create_nonce('test_hook_name_3'), 'action' => 'test_hook_name_3'];
        do_action('wp_ajax_nopriv_test_hook_name_3');
        unset($_POST, $_REQUEST);
        $this->assertTrue($switch);
    }

    public function test_AdminPost(): void
    {
        $switch   = false;
        $callback = function () use (&$switch) {
            $switch = true;
        };

        $post = new AdminPost(
            [
                'test_hook_name_1',
                ['test_hook_name_2', 'admin@callback_2', SubmitBase::ONLY_NOPRIV, '_wp_nonce', 20],
                ['test_hook_name_3', $callback, SubmitBase::ALL_GRANTED, '_wp_nonce'],
                'checkContentType' => false,
            ],
        );

        $theProp = getAccessibleProperty(AdminAjax::class, 'items');
        $items   = $theProp->getValue($post);

        // 0th item should select its callback as 'test_hook_name_1'.
        // nopriv action did not set.
        $this->assertIsInt(has_action('admin_post_test_hook_name_1', [$post, 'dispatch']));
        $this->assertFalse(has_action('admin_post_nopriv_test_hook_name_1', [$post, 'dispatch']));
        $this->assertEquals('test_hook_name_1', $items['test_hook_name_1'][0]);

        // 1st item has full list, and it should have nopriv action only.
        // callback name, and nonce key, and priority should be set.
        $this->assertFalse(has_action('admin_post_test_hook_name_2', [$post, 'dispatch']));
        $this->assertEquals(20, has_action('admin_post_nopriv_test_hook_name_2', [$post, 'dispatch']));
        $this->assertEquals('admin@callback_2', $items['test_hook_name_2'][0]);
        $this->assertEquals('_wp_nonce', $items['test_hook_name_2'][1]);

        // 2nd item priv and nopriv both actions are should be set.
        $this->assertIsInt(has_action('admin_post_test_hook_name_3', [$post, 'dispatch']));
        $this->assertIsInt(has_action('admin_post_nopriv_test_hook_name_3', [$post, 'dispatch']));
        $this->assertEquals($callback, $items['test_hook_name_3'][0]);

        // Calling 2nd item, prove that nonce is verified.
        $_REQUEST = $_POST = ['_wp_nonce' => wp_create_nonce('test_hook_name_3'), 'action' => 'test_hook_name_3'];
        do_action('admin_post_nopriv_test_hook_name_3');
        unset($_POST, $_REQUEST);
        $this->assertTrue($switch);
    }

    public function test_SubmitBase_checkContentType(): void
    {
        new AdminAjax(['checkContentType' => true]);
        $this->assertEquals(10, has_action('wp_loaded', [SubmitBase::class, 'parseStdinJson']));
    }

    public function test_parseStdinJson()
    {
        $tmp      = wp_tempnam();
        $callback = fn() => $tmp;

        if (file_exists($tmp)) {
            $_SERVER['CONTENT_TYPE']   = 'application/json';
            $_SERVER['REQUEST_METHOD'] = 'POST';
            file_put_contents($tmp, '{"key1":"value1","key2":"value2"}');
            add_filter('Bojaghi\\AdminAjax\\SubmitBase::parseStdin/source', $callback, 10, 2);
        }

        SubmitBase::parseStdinJson();

        if (file_exists($tmp)) {
            remove_filter('Bojaghi\\AdminAjax\\SubmitBase::parseStdinJson', $callback);
            unlink($tmp);
            unset($_SERVER['REQUEST_METHOD']);
            unset($_SERVER['CONTENT_TYPE']);
        }

        $this->assertArrayHasKey('key1', $_POST);
        $this->assertEquals('value1', $_POST['key1']);
        $this->assertArrayHasKey('key1', $_REQUEST);
        $this->assertEquals('value1', $_REQUEST['key1']);
        $this->assertArrayHasKey('key2', $_POST);
        $this->assertEquals('value2', $_POST['key2']);
        $this->assertArrayHasKey('key2', $_REQUEST);
        $this->assertEquals('value2', $_REQUEST['key2']);
    }
}
