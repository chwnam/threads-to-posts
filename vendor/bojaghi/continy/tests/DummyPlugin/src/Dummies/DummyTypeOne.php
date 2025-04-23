<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\Dummies;

class DummyTypeOne implements IDummy
{
    public function __construct(private string $dummy)
    {
    }

    public function dummyMethod(): string
    {
        return 'dummy-' . $this->dummy;
    }
}
