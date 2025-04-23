<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection;

class DependencyTwo
{
    public function __construct(public IDependencyTwoOne $twoOne)
    {
    }
}
