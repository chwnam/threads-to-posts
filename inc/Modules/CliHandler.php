<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use Chwnam\ThreadsToPosts\Supports\Threads\ApiCallException;
use Chwnam\ThreadsToPosts\Supports\Threads\Fields;
use Chwnam\ThreadsToPosts\Supports\Threads\AppUserFields;
use JetBrains\PhpStorm\NoReturn;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Handler\AbstractHandler;
use WP_CLI;
use WP_CLI\ExitException;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpGetApi;
use function Chwnam\ThreadsToPosts\ttpGetLogger;
use function Chwnam\ThreadsToPosts\ttpGetToken;

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
     * Get the access token
     *
     * @subcommand access-token
     *
     * @return void
     * @throws ExitException
     */
    public function accessToken(): void
    {
        $token       = ttpGetToken();
        $accessToken = $token->access_token;
        $userId      = $token->user_id;

        if ($token) {
            WP_CLI::line('Access Token: ' . PHP_EOL . $accessToken);
            WP_CLI::line('User ID: ' . PHP_EOL . $userId);
        } else {
            WP_CLI::error('No access token found.');
        }
    }

    /**
     * Add a task to the queue.
     *
     * ## OPTIONS
     *
     * <task>...: Task identifiers
     *
     * [--prepend]: Push tasks at the head of the queue.
     *
     * @param array $args
     * @param array $kwargs
     *
     * @return void
     */
    public function add(array $args, array $kwargs): void
    {
        $prepend = isset($kwargs['prepend']);
        $queue   = $this->runner->getQueue();

        if ($prepend) {
            $args = array_reverse($args);
        }
        foreach ($args as $task) {
            $queue->push($task, $prepend);
        }

        $queue->save();

        WP_CLI::success('Task(s) queued.');
    }

    /**
     * Get information of my account
     *
     * @return void
     * @throws ApiCallException
     */
    public function me(): void
    {
        $api    = ttpGetApi();
        $result = $api->getMe(['fields' => AppUserFields::getFields(Fields::ALL)]);

        WP_CLI::line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Remove tasks from the queue.
     *
     * Note that one or more same tasks may be queued at the same time.
     * Use '--all' option to remove all duplicated tasks.
     *
     * ## OPTIONS
     *
     * <task_idx>...: Indice of tasks. {min}-{max} expression is also available,
     *                e.g., 2-10 (from index 2 to 10), 3- (from 3 to the end), -10 (from the beginning to 10).
     *                Param min and max are inclusive.
     *
     * [--all]: Remove all tasks. The same as 'wp ttp reset --yes'.
     * [--invert]: In this case, tasks of the specified indices are preserved, and the others are removed.
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

        if ($all) {
            $queue->clear();
            $queue->save();
            WP_CLI::success('All tasks removed.');
            return;
        }

        $items  = $queue->export();
        $indice = [];

        foreach ($args as $arg) {
            if (str_contains($arg, '-')) {
                [$start, $end] = explode('-', $arg);
                if (empty($start)) {
                    $start = 0;
                }
                if (empty($end)) {
                    $end = count($items) - 1;
                }
                for ($i = $start; $i <= $end; $i++) {
                    $indice[] = $i;
                }
            } else {
                $indice[] = $arg;
            }
        }

        $indice = array_unique(
            array_filter(
                array_map('intval', $indice),
                fn($i) => is_int($i) && $i > -1,
            )
        );

        if (isset($kwargs['invert'])) {
            $preserved = [];
            foreach ($indice as $i) {
                if (isset($items[$i])) {
                    $preserved[] = $items[$i];
                }
            }
            $items = $preserved;
        } else {
            rsort($indice); // Make sure that we are removing from the end of the array.
            foreach ($indice as $i) {
                if (isset($items[$i])) {
                    unset($items[$i]);
                }
            }
        }

        // Import after recovering array indice.
        $queue->import(array_values($items));
        $queue->save();

        WP_CLI::success('Task(s) removed.');
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
     * [--prepend]: Append tasks to the queue.
     *
     * @param $args
     *
     * @return void
     */
    public function import($args): void
    {
        list($fileName) = $args;
        $prepend = isset($kwargs['prepend']);

        $items = file_get_contents($fileName);
        $items = explode("\n", $items);

        if ($prepend) {
            $items = array_reverse($items);
        }
        $queue = $this->runner->getQueue();
        foreach ($items as $item) {
            $queue->push($item, $prepend);
        }
        $queue->save();

        WP_CLI::success('Queue imported.');
    }

    /**
     * Remove all queued tasks.
     *
     * ## OPTIONS
     * [--yes]: Do not ask
     *
     * @param array $args
     * @param array $kwargs
     *
     * @return void
     */
    public function reset(array $args, array $kwargs): void
    {
        $yes = isset($kwargs['yes']) && $kwargs['yes'];
        if (!$yes) {
            WP_CLI::confirm('Are you sure?');
        }

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
     * [--log_level=<log_level>]: Set log level. Defaults to 'info'. Available keywords: debug, info, notice, warning, error.
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
        // Set the log level.
        $logLevel = $kwargs['log_level'] ?? 'info';
        $logger   = ttpGetLogger();
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof AbstractHandler) {
                $handler->setLevel($logLevel);;
            }
        }

        // Before shutdown, save the current status.
        $callback = function () {
            $this->runner->getQueue()->push($this->runner->getTask(), true);
            $this->runner->getQueue()->save();
            WP_CLI::success('Run terminated by Ctrl+C.');
            exit;
        };

        pcntl_async_signals(true);
        pcntl_signal(SIGINT, $callback);

        $this->runner->run($kwargs);

        WP_CLI::success('Run finished.');
    }

    /**
     * Prepare Threads posts scan and scrap.
     *
     * Clear current queue and add {heavy,light}-scrap.
     *
     * ## OPTIONS
     * [--yes]: Do not ask
     * [--heavy]: Defaults to light-scrap. Use this switch to perform heavy-scrap.
     *
     * If it is light-scrap:
     * - Only fetch the first threads page, maximum 25.
     * - Only fetch the first conversations page.
     * - Save only posts and replies older than 15 minutes.
     * - Save replies only by you, replied to only yours.
     *
     * If it is heavy-scrap:
     * - All posts in all pages, all my replies that are replied to yours are scanned.
     * - Timestamp is not considered.
     *
     * ## EXAMPLES
     * wp ttp scrap
     * wp ttp scrap --heavy
     * wp ttp scrap --heavy --yes
     *
     * @param $_
     * @param $kwargs
     *
     * @return void
     */
    public function scrap($_, $kwargs): void
    {
        $type = isset($kwargs['heavy']) ? 'heavy' : 'light';
        $yes  = isset($kwargs['yes']) && $kwargs['yes'];
        if (!$yes) {
            WP_CLI::confirm('Queue will be cleared. Are you sure?', $kwargs);
        }

        $queue = $this->runner->getQueue();
        $queue->clear();
        $queue->push("$type-scrap");
        $queue->save();

        WP_CLI::success('Queue initialized. Run `wp ttp run` to start.');
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
