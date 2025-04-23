<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\Modules;

use Bojaghi\Contract\Module;

/**
 * This is a blank module for aliasing.
 */
class AliasedModule implements Module
{
    public function __construct(public string $value)
    {
    }
}
