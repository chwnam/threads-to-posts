<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Logger;
use Chwnam\ThreadsToPosts\Interfaces\TaskQueue;
use function Chwnam\ThreadsToPosts\ttpGetLogger;

class OptionTaskQueue implements TaskQueue, Support
{
    private Logger $logger;

    private array $queue;

    private string $queueNaee;

    public function __construct(string $userId)
    {
        $this->logger    = ttpGetLogger();
        $this->queue     = [];
        $this->queueNaee = '_ttp_task_queue_' . $userId;

        $this->load();
    }

    public function isEmpty(): bool
    {
        return 0 === count($this->queue);
    }

    public function peek(): string
    {
        return $this->queue[0] ?? '';
    }

    public function pop(): string
    {
        $task = array_shift($this->queue) ?: '';

        $this->logger->debug("Task $task is popped.");

        return $task;
    }

    public function push(string $task, bool $prioritize = false): void
    {
        $task = trim($task);
        if (empty($task)) {
            return;
        }

        if ($prioritize) {
            $this->queue = [$task, ...$this->queue];
            $this->logger->debug("Task $task is pushed, prioritize=true.");
        } else {
            $this->queue[] = $task;
            $this->logger->debug("Task $task is pushed.");
        }
    }

    public function size(): int
    {
        return count($this->queue);
    }

    public function import(array $tasks): void
    {
        $this->queue = self::filterTask($tasks);
    }

    public function export(): array
    {
        return self::filterTask($this->queue);
    }

    public function clear(): void
    {
        $this->queue = [];
    }

    public function save(): void
    {
        $data = base64_encode(gzcompress(implode("\n", $this->export())));

        set_site_transient($this->queueNaee, $data);
    }

    public function load(): void
    {
        $data = explode("\n", gzuncompress(base64_decode(get_site_transient($this->queueNaee) ?: '')) ?: '');

        $this->import($data);
    }

    private static function filterTask(array $tasks): array
    {
        return array_filter($tasks, fn($t) => strlen($t) > 0);
    }
}
