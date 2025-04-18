<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Bojaghi\Contract\Support;
use Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;

class TaskManagerPage implements Support
{
    public function __construct(
        private TaskRunner $runner,
        private Template   $template,
    )
    {
    }

    public function render(): void
    {
        $queue = $this->runner->getQueue();

        $context = [
            'tasks' => $queue->export(),
        ];

        echo $this->template->template('task-manager', $context);
    }
}