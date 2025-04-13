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

    public static function addLivereload(): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            wp_enqueue_script('ttp-livereload');
        }
    }

    private function registerScripts(): void
    {
        $items = [
            [
                'handle' => 'ttp-settings',
                'src'    => plugins_url('assets/settings.js', TTP_MAIN),
                'deps'   => ['jquery', 'wp-util'],
                'ver'    => TTP_VERSION,
                'args'   => [
                    'strategy'  => 'defer',
                    'in_footer' => true,
                ],
            ],
            [
                'handle' => 'ttp-tester',
                'src'    => plugins_url('assets/tester.js', TTP_MAIN),
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
                    src:    $item['src'],
                    deps:   $item['deps'] ?? [],
                    ver:    self::versionHelper($item['ver'] ?? false),
                    args:   $item['args'] ?? [],
                );
            }
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            wp_register_script(
                'ttp-livereload',
                'http://localhost:35729/livereload.js?sipver=1',
                [],
                null,
                ['in_footer' => true]
            );
        }
    }

    private function registerStyles(): void
    {
        $items = [
            [
                'handle' => 'ttp-settings',
                'src'    => plugins_url('assets/settings.css', TTP_MAIN),
                'deps'   => [],
                'ver'    => TTP_VERSION,
            ],
            [
                'handle' => 'ttp-tester',
                'src'    => plugins_url('assets/tester.css', TTP_MAIN),
                'deps'   => [],
                'ver'    => TTP_VERSION,
            ],
        ];

        foreach ($items as $item) {
            if (!wp_style_is($item['handle'], 'registered')) {
                wp_register_style(
                    handle: $item['handle'],
                    src:    $item['src'],
                    deps:   $item['deps'] ?? [],
                    ver:    self::versionHelper($item['ver'] ?? false),
                    media:  $item['media'] ?? 'all',
                );
            }
        }
    }

    private function versionHelper(string|false $ver): string|false
    {
        return (defined('WP_DEBUG') && WP_DEBUG) ? ((string)time()) : $ver;
    }
}
