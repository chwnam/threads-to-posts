<?php

namespace Bojaghi\Continy\Tests\DummyPlugin\Supports;

use Bojaghi\Continy\Tests\DummyPlugin\Modules\AliasedModule;
use Bojaghi\Contract\Support;

class AliasedModuleSupport implements Support
{
    private string $value;

    public function __construct(
        /**
         * Here! Although 'AliasedModule' is aliased as 'aliasModule',
         * but we call it by FQCN
         */
        AliasedModule $m
    )
    {
        $this->value = $m->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}