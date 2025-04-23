<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\CustomPosts;

class CustomPosts
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
            [$type, $args] = $item;
            if (!post_type_exists($type)) {
                register_post_type($type, $args);
            }
        }
    }
}
