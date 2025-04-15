<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Interfaces\TaskQueue;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
use Chwnam\ThreadsToPosts\Supports\Threads\ApiCallException;
use Chwnam\ThreadsToPosts\Supports\Threads\ConversationsFields;
use Chwnam\ThreadsToPosts\Supports\Threads\Fields;
use Chwnam\ThreadsToPosts\Supports\Threads\PostFields;

class SimpleTaskRunner implements TaskRunner, Support
{
    private Api $api;

    private TaskQueue $queue;

    private ScrapSupport $scrap;

    private bool $forever;

    private int $maxTask;

    private int $numTask;

    private int $sleep;

    public function __construct(Api $api, TaskQueue $queue, ScrapSupport $scrap)
    {
        $this->api     = $api;
        $this->forever = false;
        $this->maxTask = 0;
        $this->numTask = 0;
        $this->queue   = $queue;
        $this->scrap   = $scrap;
        $this->sleep   = 0;
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
        $this->maxTask = (int)$args['max_task'];
        $this->numTask = 0;
        $this->sleep   = max(1, (int)$args['sleep']);

        while ($this->queue->size() > 0 && ($this->forever || $this->numTask < $this->maxTask)) {
            $task = $this->queue->pop();
            if (!$task) {
                continue;
            }
            if (!$this->_doTask($task)) {
                $this->queue->push($task);
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
        if (preg_match('/([tc]):(scan|(?:\d+,)?(?:after|before)=\w+|\d+)/', $task, $matches)) {
            $symbol  = $matches[1];
            $subject = $matches[2];

            // route
            if ('t' === $symbol) {
                if ('scan' == $subject) {
                    return $this->_doThreadsScan();
                } elseif (is_numeric($subject)) {
                    return $this->_doThreadSingle($subject);
                } elseif (str_starts_with($subject, 'before') || str_starts_with($subject, 'after')) {
                    return $this->_doThreadResume($subject);
                }
            } elseif ('c' === $symbol) {
                if (is_numeric($subject)) {
                    return $this->_doConversationScan($subject);
                } elseif (str_contains($subject, 'before') || str_contains($subject, 'after')) {
                    return $this->_doConversationResume($subject);
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
                $this->queue->push("t:after=$after");
            }
        } catch (ApiCallException $e) {
            return false;
        }

        return true;
    }

    private function _doThreadResume(string $subject): bool
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
                $this->queue->push("t:after=$after");
            }
        } catch (ApiCallException $e) {
            return false;
        }

        return true;
    }

    private function _doThreadSingle(string $subject): bool
    {
        try {
            $data = $this->api->getUserSingleThread($subject, ['fields' => PostFields::getFields(Fields::ALL)]);
            $text = $data['text'] ?? ''; // Some thread data does not have text. Skip if it does.
            if ($data && $text) {
                $this->scrap->updateThreadsMedia($data);
                $this->queue->push("c:$subject");
            }
        } catch (ApiCallException $e) {
            return false;
        }
        return true;
    }

    private function _doConversationScan(string $subject): bool
    {
        try {
            $fields = ConversationsFields::getFields(Fields::ALL);
            $result = $this->api->getMediaConversation($subject, ['fields' => $fields]);
            $data   = $result['data'] ?? [];
            $after  = $result['paging']['cursors']['after'] ?? '';

            if ($data) {
                $this->scrap->updateConversations($data);
                if ($after) {
                    $this->queue->push("c:$subject,after=$after");
                }
            }
        } catch (ApiCallException $e) {
            return false;
        }
        return true;
    }

    private function _doConversationResume(string $subject): bool
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

            $result = $this->api->getMediaConversation($threadId, $args);
            $data   = $result['data'] ?? [];
            $after  = $result['paging']['cursors']['after'] ?? '';

            if ($data) {
                $this->scrap->updateConversations($data);
                if ($after) {
                    $this->queue->push("c:$threadId,after=$after");
                }
            }
        } catch (ApiCallException $e) {
            return false;
        }
        return true;
    }

    private function cleanup(): void
    {
        $this->queue->save();
    }
}
