<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Cron;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Helper\Helper;

class CronSchedule implements Module
{
    private array $items;

    /**
     * @param array|string $config
     *
     * @example Sample configuration
     * [
     *   [
     *     'schedule' => 'every_two_hours',
     *     'interval' => 7200,
     *     'display'  => 'Every Two Hours',
     *   ],
     *   // ... more cron schedule items
     * ]
     */
    public function __construct(array|string $config)
    {
        // Currently cron schedule has no keys.
        [, $items] = Helper::separateArray(Helper::loadConfig($config));

        $this->items = $items;

        add_filter('cron_schedules', [$this, 'cronSchedules']);
    }

    public function cronSchedules(array $schedules): array
    {
        foreach ($this->items as $item) {
            $item = self::validateItem($item);
            if (!$item) {
                continue;
            }
            $schedules[$item['schedule']] = [
                'interval' => $item['interval'],
                'display'  => $item['display'],
            ];
        }

        return $schedules;
    }

    public static function validateItem(array $item): array|false
    {
        $item = wp_parse_args(
            $item,
            [
                'schedule' => '',
                'interval' => 0,
                'display'  => '',
            ],
        );

        return ($item['schedule'] && $item['interval'] > 0 && $item['display']) ? $item : false;
    }
}
