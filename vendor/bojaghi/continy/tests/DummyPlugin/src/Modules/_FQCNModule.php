<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\Modules;

use Bojaghi\Contract\Module;

/**
 * Module used for testing '_'.
 *
 * '_' modules are instantizted without hooks.
 * This class will test whether the container can load this module by its FQCN, not by its alias.
 */
class _FQCNModule implements Module
{
    public static int $value = 0;

    public function __construct()
    {
        self::$value++;
    }
}
