<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract;

use Chwnam\ThreadsToPosts\Vendor\Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    public function call(callable|array|string $callable, array|callable $args = []);
}
