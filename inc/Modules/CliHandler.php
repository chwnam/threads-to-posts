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
     * Add task to queue.
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
     * Initialization.
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
     * <task_idxs>...
     * : Indice of tasks.
     *
     * @param $args
     *
     * @return void
     */
    public function remove($args): void
    {
        $queue = $this->runner->getQueue();
        $items = $queue->export();

        $indice = array_unique(
            array_filter(
                array_map('intval', $args),
                fn($i) => is_int($i) && $i > -1,
            )
        );
        rsort($indice);

        foreach ($indice as $i) {
            if (isset($items[$i])) {
                unset($items[$i]);
            }
        }
        $items = array_values($items); // Re-construct index.

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

    /**
     * Run task queue.
     *
     * ## OPTIONS
     *
     * @return void
     */
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
            foreach ($items as $idx => $item) {
                WP_CLI::line(sprintf("%4d\t%s", $idx, $item));
            }
            WP_CLI::line("\nTotal {$queue->size()} task(s).");
        } else {
            WP_CLI::line('No tasks queued.');
        }
    }
}
