<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection;

class ReflectionTester
{
    public function __construct(
        public DependencyOne $dependencyOne,
        public DependencyTwo $dependencyTwo,
    ) {
    }
}
