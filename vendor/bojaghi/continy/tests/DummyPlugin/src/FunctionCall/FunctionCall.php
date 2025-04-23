<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\FunctionCall;

class FunctionCall
{
    public static function call(Dep_A $a, Dep_B $b): string
    {
        ++$a->count;
        ++$b->count;

        return 'success';
    }

    public function configuredCall(string $x, string $y): string
    {
        return $x . $y;
    }
}
