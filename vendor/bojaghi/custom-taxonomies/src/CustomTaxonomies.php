<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Tax;

class CustomTaxonomies
{
    private array $args;

    public function __construct(string|array $args)
    {
        $this->loadConfig($args);
        $this->register();
    }

    private function loadConfig(string|array $args): void
    {
        if (is_string($args)) {
            if (file_exists($args) && is_readable($args)) {
                $args = (array)include $args;
            } else {
                $args = [];
            }
        }

        $this->args = $args;
    }

    private function register(): void
    {
        foreach ($this->args as $item) {
            [$taxonomy, $objType, $args] = $item;
            if (!taxonomy_exists($taxonomy)) {
                register_taxonomy($taxonomy, $objType, $args);
            }
        }
    }
}