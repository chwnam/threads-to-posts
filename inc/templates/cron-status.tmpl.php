<?php

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context
 * -------
 * - cron_details: array{ title: string, timestamp: string, schedule: string }[]
 */
?>

<div id="ttp-cron_status">
    <?php foreach ($this->get('cron_details') as $detail): ?>
        <div class="ttp-cron_detail">
            <h4 title="Cron Job Name"><?php echo esc_html($detail['title']); ?></h4>
            <ul>
                <li>
                    <span class="label"
                          title="When this job is going to be called.">Next Scheduled</span>
                    <time datetime="<?php echo esc_attr(wp_date('c', $detail['timestamp'])); ?>">
                        <?php echo esc_html(wp_date('Y-m-d H:i:s', $detail['timestamp'])); ?>
                        (<?php echo esc_html(human_time_diff($detail['timestamp'])); ?> 후)
                    </time>
                </li>
                <li>
                    <span class="label"
                          title="Name of the schedule">Schedule</span><?php echo esc_html($detail['schedule']); ?>
                </li>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
