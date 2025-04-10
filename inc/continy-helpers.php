<?php

namespace Chwnam\ThreadsToPosts;

use Bojaghi\Continy\Continy;
use Bojaghi\Continy\ContinyException;
use Bojaghi\Continy\ContinyFactory;
use Bojaghi\Continy\ContinyNotFoundException;
use Chwnam\ThreadsToPosts\Modules\Logger as LoggerModule;
use Chwnam\ThreadsToPosts\Modules\Options;
use Monolog\Logger;

/**
 * Wrapper function
 *
 * @return Continy
 * @throws ContinyException|ContinyNotFoundException
 */
function ttp(): Continy
{
    static $continy = null;

    if (is_null($continy)) {
        $continy = ContinyFactory::create(dirname(__DIR__) . '/conf/continy-setup.php');
    }

    return $continy;
}

/**
 * @template T
 * @param class-string<T> $id
 *
 * @return T|object|null
 */
function ttpGet(string $id)
{
    try {
        $instance = ttp()->get($id);
    } catch (ContinyException|ContinyNotFoundException $e) {
        $instance = null;
    }

    return $instance;
}


/**
 * @template T
 * @param class-string<T> $id
 * @param string          $method
 * @param array|false     $args
 *
 * @return mixed
 */
function ttpCall(string $id, string $method, array|false $args = false)
{
    try {
        $container = ttp();
        $instance  = $container->get($id);
        return $container->call([$instance, $method], $args);
    } catch (ContinyException|ContinyNotFoundException $e) {
        wp_die($e->getMessage());
    }
}

function ttpOptions(): Options
{
    return ttpGet(Options::class);
}

function ttpLogger(): Logger
{
    return ttpGet(LoggerModule::class)->get();
}
