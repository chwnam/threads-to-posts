<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use JetBrains\PhpStorm\NoReturn;
use WP_CLI;
use WP_CLI\ExitException;
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
     * Add a task to the queue.
     *
     * ## OPTIONS
     *
     * <task>...: Task identifiers
     *
     * [--append]: Push tasks at the head of the queue.
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
     * Export the task queue to stdout.
     *
     * ## OPTIONS:
     * <file_name>: File name to export. To echo to stdout, use '-' as the file name.
     *
     * @param $args
     *
     * @return void
     * @throws ExitException
     */
    public function export($args): void
    {
        list($fileName) = $args;

        $fp = fopen('-' === $fileName ? 'php://stdout' : $fileName, 'w');
        if (!$fp) {
            WP_CLI::error('Cannot open a file.');
            exit;
        }

        $queue = $this->runner->getQueue();
        $items = $queue->export();
        foreach ($items as $item) {
            fwrite($fp, $item . "\n");
        }

        fclose($fp);

        WP_CLI::success('Queue exported.');
    }

    /**
     * Import queue status from a file.
     *
     * ## OPTIONS
     *
     * <file_name>: File name to import.
     * [--append]: Append tasks to the queue.
     *
     * @param $args
     *
     * @return void
     */
    public function import($args): void
    {
        list($fileName) = $args;
        $append = isset($kwargs['append']);

        $items = file_get_contents($fileName);
        $items = explode("\n", $items);

        if ($append) {
            $items = array_reverse($items);
        }
        $queue = $this->runner->getQueue();
        foreach ($items as $item) {
            $queue->push($item, $append);
        }

        WP_CLI::success('Queue imported.');
    }

    /**
     * Initialization.
     *
     * Clear the queue and add t:scan
     *
     * ## OPTIONS
     *
     * [--yes]: Do not ask
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
     * Remove tasks from the queue.
     *
     * Note that one or more same tasks may be queued at the same time.
     * Use '--all' option to remove all duplicated tasks.
     *
     * ## OPTIONS
     *
     * <task_idx>...: Indice of tasks.
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
     * Run the task queue.
     *
     * ## OPTIONS
     * [--forever]: Run until the queue is empty
     * [--max_task=<max_task>]: Run at most <max_task>. This switch cannot be used with --forever. Defaults to 25.
     * [--sleep=<sleep>]: Sleep <sleep> seconds after every fetch. Defaults to 2. Minimum 1.
     *
     * @param $_
     * @param $kwargs
     *
     * @return void
     */
    public function run($_, $kwargs): void
    {
        // pcntl_signal(SIGINT, [$this, 'shutdown']);

        $this->runner->run($kwargs);

        WP_CLI::success('Run finished.');
    }

    #[NoReturn]
    public function shutdown(): void
    {
        WP_CLI::line('Received SIGINT. Shutting down.');

        $queue = $this->runner->getQueue();
        $task  = $this->runner->getTask();

        if ($task !== $queue->peek()) {
            $queue->push($task, true);
        }

        $this->runner->getQueue()->save();
        exit;
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
