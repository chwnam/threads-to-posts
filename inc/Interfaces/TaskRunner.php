<?php

namespace Chwnam\ThreadsToPosts\Interfaces;

interface TaskRunner
{
    public function run(array|string $args = ''): void;

    public function getQueue(): TaskQueue;

    public function setQueue(TaskQueue $queue): void;
}
