<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use WP_CLI;
use function Chwnam\ThreadsToPosts\ttpGet;

/**
 * Threads to Posts plugin commands.
 */
class CliHandler implements Module
{
    private TaskRunner $runner;

    public function __construct()
    {
        $this->runner = ttpGet(TaskRunner::class);
    }

    /**
     * Add task to queue
     *
     * ## OPTIONS
     *
     * <task>...
     * : Task identifiers
     *
     * [--append]
     * : Push tasks at the head of the queue.
     *
     * @param array $args
     * @param array $kwargs
     *
     * @return void
     */
    public function add(array $args, array $kwargs): void
    {
        $append = isset($kwargs['append']);
        $queue  = $this->runner->getQueue();

        if ($append) {
            $args = array_reverse($args);
        }
        foreach ($args as $task) {
            $queue->push($task, $append);
        }

        $queue->save();
        WP_CLI::success('Task(s) queued.');
    }

    /**
     * Initialization
     *
     * Clear queue and add t:scan
     *
     * ## OPTIONS
     *
     * [--yes]
     * : Do not ask
     *
     * @param $args
     * @param $kwargs
     *
     * @return void
     */
    public function init($args, $kwargs): void
    {
        WP_CLI::confirm('Queue will be cleared. Are you sure?', $kwargs);

        $queue = $this->runner->getQueue();
        $queue->clear();
        $queue->push('t:scan');
        $queue->save();

        WP_CLI::success('Queue initialized. Run `wp ttp run` to start.');
    }

    /**
     * Remove tasks from queue.
     *
     * Note that one or more same tasks may be queued at the same time.
     * Use '--all' option to remove all duplicated tasks.
     *
     * ## OPTIONS
     *
     * <tasks>...
     * : List of tasks.
     *
     * [--all]
     * : Remove all same tasks found in the queue.
     *
     * @param $args
     * @param $kwargs
     *
     * @return void
     */
    public function remove($args, $kwargs): void
    {
        $all   = isset($kwargs['all']);
        $queue = $this->runner->getQueue();
        $items = $queue->export();

        foreach ($args as $task) {
            do {
                $pos = array_search($task, $items, true);
                if (false === $pos) {
                    break;
                }
                array_splice($items, $pos, 1);
            } while ($all);
        }

        $queue->import($items);
        $queue->save();

        WP_CLI::success('Task(s) removed.');
    }

    /**
     * Remove all queued tasks.
     */
    public function reset(): void
    {
        WP_CLI::confirm('Are you sure?');

        $queue = $this->runner->getQueue();
        $queue->clear();
        $queue->save();

        WP_CLI::success('Queue cleared.');
    }

    public function run(): void
    {
        $this->runner->run();

        WP_CLI::success('Run finished.');
    }

    /**
     * Output task queue status
     *
     * @return void
     */
    public function status(): void
    {
        $queue = $this->runner->getQueue();
        $items = $queue->export();

        if ($items) {
            WP_CLI::line('Tasks:');
            foreach ($items as $item) {
                WP_CLI::line('  ' . $item);
            }
            WP_CLI::line("\nTotal {$queue->size()} task(s).");
        } else {
            WP_CLI::line('No tasks queued.');
        }
    }
}
