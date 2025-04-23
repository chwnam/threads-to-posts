<?php

namespace Bojaghi\Cron\Tests;

use Bojaghi\Cron\CronSchedule;

class TestCronSchedule extends \WP_UnitTestCase
{
    public function test()
    {
        $cs = new CronSchedule(
            [
                [
                    'display'  => 'Test Cron Schedule',
                    'interval' => 7200,
                    'schedule' => 'test_cron',
                ]
            ],
        );

        // apply_filters is used in the function.
        $schedules = wp_get_schedules();

        // Check the item is properly exists.
        $this->assertArrayHasKey('test_cron', $schedules);

        // Alias.
        $test = $schedules['test_cron'];

        // Check the others.
        $this->assertEquals('Test Cron Schedule', $test['display']);
        $this->assertEquals(7200, $test['interval']);
    }
}
