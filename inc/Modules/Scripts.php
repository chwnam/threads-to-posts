<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;

class Scripts implements Module
{
    public function __construct()
    {
        $this->registerScripts();
        $this->registerStyles();
    }

    private function registerScripts(): void
    {
        $items = [
            [
                'handle' => 'ttp-settings',
                'src'    => plugins_url('assets/js/settings.js', TTP_MAIN),
                'deps'   => ['jquery', 'wp-util'],
                'ver'    => TTP_VERSION,
                'args'   => [
                    'strategy'  => 'defer',
                    'in_footer' => true,
                ],
            ]
        ];

        foreach ($items as $item) {
            if (!wp_script_is($item['handle'], 'registered')) {
                wp_register_script(
                    handle: $item['handle'],
                    src: $item['src'],
                    deps: $item['deps'] ?? [],
                    ver: $item['ver'] ?? false,
                    args: $item['args'] ?? [],
                );
            }
        }
    }

    private function registerStyles(): void
    {
    }
}
