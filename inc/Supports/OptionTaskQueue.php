<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Interfaces\TaskQueue;

class OptionTaskQueue implements TaskQueue, Support
{
    private array $queue;

    private string $queueNaee;

    public function __construct(string $userId)
    {
        $this->queue     = [];
        $this->queueNaee = '_ttp_task_queue_' . $userId;

        $this->load();
    }

    public function isEmpty(): bool
    {
        return 0 === count($this->queue);
    }

    public function pop(): string
    {
        return array_shift($this->queue) ?: '';
    }

    public function push(string $task, bool $prioritize = false): void
    {
        if ($prioritize) {
            $this->queue = [$task, ...$this->queue];
        } else {
            $this->queue[] = $task;
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
        update_option($this->queueNaee, implode(',', $this->export()));
    }

    public function load(): void
    {
        $this->import(explode(',', get_option($this->queueNaee) ?: ''));
    }

    private static function filterTask(array $tasks): array
    {
        return array_filter($tasks, fn($t) => strlen($t) > 0);
    }
}
