<?php

namespace Bojaghi\Cron\Tests;

use Bojaghi\Cron\Cron;

class TestCron extends \WP_UnitTestCase
{
    public function test()
    {
        $cron = new Cron(
            [
                'main_file' => __FILE__,
                [
                    'timestamp'       => 0,
                    'hook'            => 'test_single_hook',
                    'args'            => [1, 2, 3],
                    'is_single_event' => true,
                ],
                [
                    'timestamp'       => 0,
                    'schedule'        => 'daily',
                    'hook'            => 'test_recurring_hook',
                    'args'            => ['x', 'y', 'z'],
                    'is_single_event' => false,
                ]
            ],
        );

        // Test activation
        $cron->activate();

        $schedule = wp_get_scheduled_event('test_single_hook', [1, 2, 3]);
        $this->assertIsObject($schedule);
        $this->assertGreaterThan(0, $schedule->timestamp);        // timestamp
        $this->assertEquals('test_single_hook', $schedule->hook); // hook
        $this->assertEquals([1, 2, 3], $schedule->args);          // args

        $schedule = wp_get_scheduled_event('test_recurring_hook', ['x', 'y', 'z']);
        $this->assertIsObject($schedule);
        $this->assertGreaterThan(0, $schedule->timestamp);           // timestamp
        $this->assertEquals('test_recurring_hook', $schedule->hook); // hook
        $this->assertEquals('daily', $schedule->schedule);           // schedule
        $this->assertEquals(['x', 'y', 'z'], $schedule->args);       // args

        // Test deactivation
        $cron->deactivate();

        $this->assertFalse(wp_get_scheduled_event('test_single_hook', [1, 2, 3]));
        $this->assertFalse(wp_get_scheduled_event('test_recurring_hook', ['x', 'y', 'z']));
    }
}
