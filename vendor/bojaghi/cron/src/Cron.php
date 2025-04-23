<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Cron;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Helper\Helper;

class Cron implements Module
{
    private array $items;

    /**
     * @param array|string $config
     *
     * @example Sample configuration
     * [
     *   'is_theme'  => false,
     *   'main_file' => '/path/to/plugin/main/file.php',
     *   [
     *     'timestamp'       => 0,      // Time to invoke this hook
     *     'schedule'        => '',     // Scheduled name, e.g. daily, hourly, and so on.
     *     'hook'            => '',     // Hook name
     *     'args'            => [],     // Optional: arguments.
     *     'wp_error'        => false,  // Optional: raise wp_error if error occur.
     *     'is_single_event' => false,  // Optional: single event, or recurring event.
     *   ],
     *   // ... more cron items
     * ]
     */
    public function __construct(array|string $config = '')
    {
        [$assoc, $indexed] = Helper::separateArray(Helper::loadConfig($config));

        $assoc = wp_parse_args(
            $assoc,
            [
                'is_theme'  => false, // true if this module is used in themes
                'main_file' => '',    // ignore if this module is used in thees.
            ],
        );

        $isTheme     = (bool)$assoc['is_theme'];
        $mainFile    = (string)$assoc['main_file'];
        $this->items = $indexed;

        if ($this->items) {
            if ($isTheme) {
                add_action('after_setup_theme', [$this, 'activate']);
                add_action('switch_theme', [$this, 'deactivate']);
            } elseif ($mainFile) {
                register_activation_hook($mainFile, [$this, 'activate']);
                register_deactivation_hook($mainFile, [$this, 'deactivate']);
            }
        }
    }

    public function activate(): void
    {
        foreach ($this->items as $item) {
            $this->register($item);
        }
    }

    public function deactivate(): void
    {
        foreach ($this->items as $item) {
            $this->unregister($item);
        }
    }

    public static function validateItem(array $item): array|false
    {
        $item = wp_parse_args(
            $item,
            [
                'timestamp'       => 0,
                'schedule'        => '',
                'hook'            => '',
                'args'            => [],
                'wp_error'        => false,
                'is_single_event' => false,
            ],
        );

        if (!$item['timestamp']) {
            $item['timestamp'] = time();
        }

        return (
            $item['hook'] &&
            $item['timestamp'] > 0 &&
            ($item['is_single_event'] || $item['schedule'])
        ) ? $item : false;
    }

    private function register(array $item): void
    {
        $item = self::validateItem($item);
        if (!$item) {
            return;
        }

        if ($item['is_single_event']) {
            // Single event.
            wp_schedule_single_event(
                $item['timestamp'],
                $item['hook'],
                $item['args'],
                $item['wp_error'],
            );
        } else {
            // Recurring event
            wp_schedule_event(
                $item['timestamp'],
                $item['schedule'],
                $item['hook'],
                $item['args'],
                $item['wp_error'],
            );
        }
    }

    private function unregister(array $item): void
    {
        $item = self::validateItem($item);
        if (!$item) {
            return;
        }

        if (wp_next_scheduled($item['hook'], $item['args'])) {
            wp_clear_scheduled_hook($item['hook'], $item['args']);
        }
    }
}
