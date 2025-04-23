<?php
/**
 * Plugin Name: Test Dummy plugin.
 * Description: Our test dummy plugin activated while unit test bootstrap
 */

namespace Bojaghi\Continy\Tests\DummyPlugin;

if ( ! defined('ABSPATH')) {
    exit;
}

function getTestDummyPlugin(): \Bojaghi\Continy\Continy
{
    static $continy = null;

    if (is_null($continy)) {
        try {
            $continy = \Bojaghi\Continy\ContinyFactory::create(__DIR__ . '/conf/setup.php');
        } catch (\Bojaghi\Continy\ContinyException $e) {
            die($e->getMessage());
        }
    }

    return $continy;
}

getTestDummyPlugin();
