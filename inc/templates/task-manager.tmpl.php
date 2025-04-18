<?php

use Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context:
 */
?>

<div class="wrap">
    <h1>Task Manager</h1>
    <hr class="wp-header-end">

    <h2>Task Queue Status</h2>
    <button id="do-task"
            type="button"
            class="button button-primary" onclick="alert('implement me!')">
        DO 25 TASKS
    </button>
    <ol>
        <?php foreach ((array)$this->get('tasks') as $task) : ?>
            <li><?php echo esc_html($task); ?></li>
        <?php endforeach; ?>
    </ol>
</div>
