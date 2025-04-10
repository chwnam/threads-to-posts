<?php

use Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context:
 * - title:        string
 * - options_page: string
 * - page:         string
 *
 */
?>
<div class="wrap">
    <h1><?php echo esc_html($this->get('title')); ?></h1>
    <hr class="wp-header-end" />
    <form method="post" action="<?php echo admin_url('options.php'); ?>">
        <?php settings_fields($this->get('option_group')); ?>
        <?php do_settings_sections($this->get('page')); ?>
        <?php submit_button(); ?>
    </form>
</div>
