<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Helper;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Container;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\ContinyFactory;
use Exception;
use Throwable;

/**
 * These static methods are very frequently used if you are using continy as your container.
 */
class Facades
{
    /**
     * @param array|string                 $config
     * @param class-string<ContinyFactory> $continyFactoryClass
     *
     * @return Container
     */
    public static function container(array|string $config = '', string $continyFactoryClass = ''): Container
    {
        static $continy = null;

        if (is_null($continy)) {
            try {
                if (!in_array(ContinyFactory::class, class_implements($continyFactoryClass), true)) {
                    throw new Exception('The class ' . $continyFactoryClass . ' must implement ' . ContinyFactory::class);
                }
                $continy = $continyFactoryClass::create($config);
            } catch (Throwable $e) {
                wp_die($e->getMessage());
            }
        }

        return $continy;
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @param callable|false  $constructorCall Parameter of the callback:
     *                                         - 0th: continy instance
     *                                         - 1st: FQCN string of $id
     *                                         - 2nd: raw $id string
     *                                         Return of the callback: $id's real instance
     *
     * @return T|object|null
     * @see    Continy::instantiate()
     * @sample $this->get('myThing', function ($continy, $className, $id) { return new $className(); });
     */
    public static function get(string $id, callable|false $constructorCall = false)
    {
        try {
            $instance = self::container()->get($id, $constructorCall);
        } catch (Throwable $_) {
            return null;
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
    public static function call(string $id, string $method, array|false $args = false): mixed
    {
        try {
            $container = self::container();
            $instance  = $container->get($id);
            if (!$instance) {
                throw new Exception("Instance $id not found");
            }
            return $container->call([$instance, $method], $args);
        } catch (Throwable $e) {
            wp_die($e->getMessage());
        }
    }

    public static function parseCallback(string|array|callable $callback): callable|null
    {
        return static::container()->parseCallback($callback);
    }
}
