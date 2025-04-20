<?php

use Bojaghi\Template\Template;

/**
 * Scrap tab template
 *
 * @var Template $this
 *
 * Context
 * -------
 * - settings_url: string
 * - status: string
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html($this->fetch('title')); ?></h1>
    <hr class="wp-header-end">

    <?php if ('error-auth' === $this->get('status')) : ?>
        <div class="ttp-scrap ttp-scrap-error">
            <p>
                Your App ID and App secret is missing.
                Enter them in <a href="<?php echo esc_url($this->get('settings_url')); ?>">the settings page</a>.
            </p>
        </div>
    <?php endif; ?>

    <?php if ('error-token' === $this->get('status')) : ?>
        <div class="ttp-scrap ttp-scrap-error">
            <p>
                Your acess token is missing.
                Authorize the app in <a href="<?php echo esc_url($this->get('settings_url')); ?>">the settings page</a>.
            </p>
        </div>
    <?php endif; ?>

    <?php
    if ('okay' === $this->get('status')) :
        echo $this->fetch('okay');
    endif;
    ?>
</div>
