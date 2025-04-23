<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\ConstructorCall;

class ConstructorCall
{
    private string $var1;

    private string $var2;

    public function __construct(string $var1, string $var2)
    {
        $this->var1 = $var1;
        $this->var2 = $var2;
    }

    public function getVar1(): string
    {
        return $this->var1;
    }

    public function getVar2(): string
    {
        return $this->var2;
    }
}
