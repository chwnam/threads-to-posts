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

    public function getQueue(): TaskQueue
    {
        return $this->queue;
    }

    public function setQueue(TaskQueue $queue): void
    {
        $this->queue = $queue;
    }

    private function _doTask(string $task): bool
    {
        if (preg_match('/^(!?)([tc]):(scan|(?:\d+:)?(?:after|before)=\w+|\d+)$/', $task, $matches)) {
            $appendCursor = !empty($matches[1]);
            $symbol       = $matches[2];
            $subject      = $matches[3];

            // route
            if ('t' === $symbol) {
                if ('scan' == $subject) {
                    return $this->_doThreadsScan();
                } elseif (is_numeric($subject)) {
                    return $this->_doThreadSingle($subject, $appendCursor);
                } elseif (str_starts_with($subject, 'before') || str_starts_with($subject, 'after')) {
                    return $this->_doThreadsResume($subject, $appendCursor);
                }
            } elseif ('c' === $symbol) {
                if (is_numeric($subject)) {
                    return $this->_doConversationScan($subject, $appendCursor);
                } elseif (str_contains($subject, 'before') || str_contains($subject, 'after')) {
                    return $this->_doConversationResume($subject, $appendCursor);
                }
            }
        }

        return false;
    }

    private function _doThreadsScan(): bool
    {
        try {
            $result = $this->api->getUserThreads(['fields' => PostFields::getFields(Fields::ID)]);
            $data   = array_map(fn($data) => $data['id'], $result['data']);
            $after  = $result['paging']['cursors']['after'] ?? '';

            foreach ($data as $id) {
                $this->queue->push("t:$id");
            }
            if ($after) {
                $this->queue->push("!t:after=$after", true);
            }
        } catch (ApiCallException $e) {
            return false;
        }

        return true;
    }

    private function _doThreadsResume(string $subject, bool $appendCursor): bool
    {
        try {
            [$key, $value] = explode('=', $subject, 2);

            $args = [
                'after'  => 'after' === $key ? $value : '',
                'before' => 'before' === $key ? $value : '',
                'fields' => PostFields::getFields(Fields::ID),
            ];

            $result = $this->api->getUserThreads($args);
            $data   = array_map(fn($data) => $data['id'], $result['data'] ?? []);
            $after  = $result['paging']['cursors']['after'] ?? '';

            foreach ($data as $id) {
                $this->queue->push("t:$id");
            }
            if ($after) {
                $prefix = $appendCursor ? '!' : ''; // inherit $appendCursor.
                $this->queue->push("{$prefix}t:after=$after", $appendCursor);
            }
        } catch (ApiCallException $e) {
            return false;
        }

        return true;
    }

    private function _doThreadSingle(string $subject, bool $appendCursor): bool
    {
        try {
            $data = $this->api->getUserSingleThread($subject, ['fields' => PostFields::getFields(Fields::ALL)]);
            $text = $data['text'] ?? ''; // Some thread data does not have text. Skip if it does.
            if ($data && $text) {
                $prefix = $appendCursor ? '!' : ''; // inherit $appendCursor.
                $this->scrap->updateThreadsMedia($data);
                $this->queue->push("{$prefix}c:$subject");
            }
        } catch (ApiCallException $e) {
            return false;
        }
        return true;
    }

    private function _doConversationScan(string $subject, bool $appendCursor): bool
    {
        try {
            $this->_processConversationsResult(
                $subject,
                $this->api->getMediaConversation(
                    $subject,
                    ['fields' => ConversationsFields::getFields(Fields::ALL)]
                ),
                $appendCursor
            );
        } catch (ApiCallException $e) {
            return false;
        }
        return true;
    }

    private function _doConversationResume(string $subject, bool $appendCursor): bool
    {
        try {
            // Valid format c:<thread_id>,(before|after)=<cursor>
            if (!str_contains($subject, ',') || !str_contains($subject, '=')) {
                return false;
            }

            [$threadId, $cursor] = explode(',', $subject, 2);
            [$key, $value] = explode('=', $cursor, 2);

            $args = [
                'after'  => 'after' === $key ? $value : '',
                'before' => 'before' === $key ? $value : '',
                'fields' => ConversationsFields::getFields(Fields::ALL),
            ];

            $this->_processConversationsResult(
                $threadId,
                $this->api->getMediaConversation($threadId, $args),
                $appendCursor
            );
        } catch (ApiCallException $e) {
            return false;
        }
        return true;
    }

    private function _processConversationsResult(string $threadId, array $result, bool $appendCursor): void
    {
        $data  = $result['data'] ?? [];
        $after = $result['paging']['cursors']['after'] ?? '';

        if ($data) {
            $this->scrap->updateConversations($data);
            if ($after) {
                $prefix = $appendCursor ? '!' : ''; // inherit $appendCursor.
                $this->queue->push("{$prefix}c:$threadId:after=$after", $appendCursor);
            }
        }
    }

    private function cleanup(): void
    {
        $this->queue->save();
        $this->task = '';
    }
}
