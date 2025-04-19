<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Monolog\Logger as MonologLogger;
use Chwnam\ThreadsToPosts\Interfaces\TaskQueue;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use Chwnam\ThreadsToPosts\Modules\Logger as LoggerModule;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
use Chwnam\ThreadsToPosts\Supports\Threads\ApiCallException;
use Chwnam\ThreadsToPosts\Supports\Threads\ConversationsFields;
use Chwnam\ThreadsToPosts\Supports\Threads\Fields;
use Chwnam\ThreadsToPosts\Supports\Threads\PostFields;

/**
 * Simple task runner
 *
 * NOTE: Never use commas in task names.
 *       Comma is the separator for the option queue.
 */
class SimpleTaskRunner implements TaskRunner, Support
{
    private Api $api;

    private MonologLogger $logger;

    private TaskQueue $queue;

    private ScrapSupport $scrap;

    private bool $forever;

    private int $maxTask;

    private int $numTask;

    private int $sleep;

    private string $task;

    public function __construct(Api $api, LoggerModule $logger, TaskQueue $queue, ScrapSupport $scrap)
    {
        $this->api     = $api;
        $this->logger  = $logger->get();
        $this->forever = false;
        $this->maxTask = 0;
        $this->numTask = 0;
        $this->queue   = $queue;
        $this->scrap   = $scrap;
        $this->sleep   = 0;
        $this->task    = '';
    }

    /**
     * Get current task
     *
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    public function run(array|string $args = ''): void
    {
        $defaults = [
            'forever'  => false,
            'max_task' => 25,
            'sleep'    => 2,
        ];

        $args = wp_parse_args($args, $defaults);

        $this->forever = (bool)$args['forever'];
        $this->maxTask = max(0, (int)$args['max_task']);
        $this->numTask = 0;
        $this->sleep   = max(1, (int)$args['sleep']);

        while ($this->queue->size() > 0 && ($this->forever || $this->numTask < $this->maxTask)) {
            $this->task = $this->queue->pop();
            if (!$this->task) {
                continue;
            }

            $this->logger->info(
                sprintf(
                    '[%d/%d] Task %s',
                    $this->numTask + 1,
                    $this->maxTask,
                    $this->task,
                )
            );

            $result = $this->_doTask($this->task);
            if ($result) {
                $this->logger->info(sprintf('Task %s is successful.', $this->task));
            } else {
                // Fail count?
                $this->logger->error(sprintf('Task %s is failed.', $this->task));
            }

            ++$this->numTask;
            sleep($this->sleep);
        }

        $this->cleanup();
    }

    private function _doTask(string $task): bool
    {
        // Aliases
        if ('light-scrap' === $task) {
            $this->queue->push('t::fields=id,timestamp');
            return true;
        } elseif ('heavy-scrap' === $task) {
            $this->queue->push('tx::', true);
            return true;
        }

        if (preg_match(
            '/^(?<job>[tc])(?<intent>x?):(?<id>\d*)(?::(?<params>.*))?$/',
            $task,
            $matches
        )) {
            $job    = $matches['job'];
            $intent = $matches['intent']; // x: heavy-scrap, empty: light-scrap.
            $id     = $matches['id'];
            $params = $matches['params'] ?? '';

            try {
                // Route
                if ('t' === $job) {
                    if (empty($id)) {
                        return $this->_scrapThreadsList($params, $intent);
                    } else {
                        return $this->_scrapThreadSingle($id, $params, $intent);
                    }
                } elseif ('c' === $job && $id) {
                    // Get conversations
                    return $this->_scrapConverastions($id, $params, $intent);
                }
            } catch (ApiCallException $e) {
                $this->logger->error("ApiCallException catched. Message: {$e->getMessage()}");
                return false;
            }
        }

        $this->logger->error(sprintf('Task %s is not supported.', $task));

        return false;
    }

    /**
     * @throws ApiCallException
     */
    private function _scrapThreadsList(string $params, string $intent): bool
    {
        $result  = $this->api->getUserThreads($params);
        $data    = $result['data'];
        $hasNext = isset($result['paging']['next']);
        $after   = $result['paging']['cursors']['after'] ?? '';

        // In normal intent, threads posts earlier than 15 minutes are skipped.
        // We assume that timestamp field always exists and is valid.
        if ('' === $intent) {
            $data = self::filterOldItems($data);
        }

        foreach ($data as $item) {
            $this->queue->push("t$intent:$item[id]:");
        }

        // Only eXtended intent wants next pages.
        if ('x' === $intent && $hasNext && $after) {
            $newParams = self::mergeParams($params, ['after' => $after]);
            $this->queue->push("t$intent::$newParams", true);
        }

        return true;
    }

    /**
     * @throws ApiCallException
     */
    private function _scrapThreadSingle(string $threadsId, string $params, string $intent): bool
    {
        $params = self::mergeParams($params, ['fields' => PostFields::getFields(Fields::ALL)]);
        $data   = $this->api->getUserSingleThread($threadsId, $params);

        $this->scrap->updateThreadsMedia($data);
        $this->queue->push("c$intent:$threadsId:fields=" . ConversationsFields::getFields(Fields::ALL));

        return true;
    }

    /**
     * @throws ApiCallException
     */
    private function _scrapConverastions(string $threadsId, string $params, string $intent): bool
    {
        $result  = $this->api->getMediaConversation($threadsId, $params);
        $data    = $result['data'] ?? [];
        $hasNext = isset($result['paging']['next']);
        $after   = $result['paging']['cursors']['after'] ?? '';

        // In normal intent, threads replies earlier than 15 minutes are skipped.
        // We assume that timestamp field always exists and is valid.
        if ('' === $intent) {
            $data = self::filterOldItems($data);
        }

        $this->scrap->updateConversations($data);

        // Only eXtended intent wants next pages.
        if ('x' === $intent && $hasNext && $after) {
            $newParams = self::mergeParams($params, ['after' => $after]);
            $this->queue->push("c$intent:$threadsId:$newParams");
        }

        return true;
    }

    private function cleanup(): void
    {
        $this->queue->save();
        $this->task = '';
    }

    public function getQueue(): TaskQueue
    {
        return $this->queue;
    }

    public function setQueue(TaskQueue $queue): void
    {
        $this->queue = $queue;
    }

    private static function filterOldItems(array $input, int $thresh = 15 * MINUTE_IN_SECONDS): array
    {
        $output = [];

        foreach ($input as $item) {
            $timestamp = date_create_from_format('Y-m-d\TH:i:sO', $item['timestamp'] ?? '');
            if ($timestamp && (time() - $timestamp->getTimestamp() > $thresh)) {
                $output[] = $item;
            }
        }

        return $output;
    }

    private static function mergeParams(string|array ...$args): string
    {
        $arrays = array_merge(
            ...array_map(fn($arg) => wp_parse_args($arg), $args)
        );

        return http_build_query($arrays);
    }
}
