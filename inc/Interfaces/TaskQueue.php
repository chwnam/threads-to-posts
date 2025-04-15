<?php

namespace Chwnam\ThreadsToPosts\Interfaces;

interface TaskQueue
{
    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Get the first item
     *
     * @return string
     */
    public function pop(): string;

    /**
     * Push task
     *
     * @param string $task
     * @param bool   $prioritize if true, task is pushed to the head.
     *
     * @return void
     */
    public function push(string $task, bool $prioritize = false): void;

    /**
     * Get the size of queue
     *
     * @return int
     */
    public function size(): int;

    /**
     * Restruct queue
     *
     * @param array $tasks
     *
     * @return void
     */
    public function import(array $tasks): void;

    /**
     * Get the whole list of queue
     *
     * @return array
     */
    public function export(): array;

    /**
     * Clear the queue
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Save the queue
     *
     * @return void
     */
    public function save(): void;

    /**
     * Load the queue
     *
     * @return void
     */
    public function load(): void;
}
