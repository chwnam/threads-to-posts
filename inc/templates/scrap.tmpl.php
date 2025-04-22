<?php

use Bojaghi\Template\Template;

/**
 * Scrap tab template
 *
 * @var Template $this
 *
 * Context
 * -------
 * - scrap_mode: string
 * - settings_url: string
 * - status: string
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<?php
$this->extends('token-status-required')
    ->assign('title', 'Scrap')
    ->start('okay');
?>
    <form class="ttp-scrap ttp-scrap-okay" action="<?php echo admin_url('admin-post.php'); ?>" method="post">
        <fieldset>
            <legend>Current Scrap Mode</legend>
            <ul>
                <li>
                    <input id="ttp_scrap_mode-disabled"
                           name="ttp_scrap_mode"
                           value="disabled"
                           type="radio"
                        <?php checked('disabled' === $this->get('scrap_mode')); ?>>
                    <label for="ttp_scrap_mode-disabled">
                        Disabled
                        <?php
                        echo 'disabled' === $this->get('scrap_mode') ?
                            '<span class="current">(current)</span' : '';
                        ?>
                    </label>
                </li>
                <li>
                    <input id="ttp_scrap_mode-light"
                           name="ttp_scrap_mode"
                           value="light"
                           type="radio"
                        <?php checked('light' === $this->get('scrap_mode')); ?>>
                    <label for="ttp_scrap_mode-light">
                        Light mode
                        <?php
                        echo 'light' === $this->get('scrap_mode') ?
                            '<span class="current">(current)</span' : '';
                        ?>
                    </label>
                </li>
                <li>
                    <input id="ttp_scrap_mode-heavy"
                           name="ttp_scrap_mode"
                           value="heavy"
                           type="radio"
                           disabled="disabled"
                        <?php checked('heavy' === $this->get('scrap_mode')); ?>>
                    <label for="ttp_scrap_mode-heavy">
                        Heavy mode
                        <?php
                        echo 'heavy' === $this->get('scrap_mode') ?
                            '<span class="current">(current)</span' : '';
                        ?>
                    </label>
                </li>
            </ul>
        </fieldset>
        <input type="hidden" name="action" value="ttp_update_scrap_mode">
        <?php wp_nonce_field('ttp_update_scrap_mode', '_ttp_update_scrap_mode'); ?>
        <?php submit_button(); ?>
    </form>
<?php
$this->end();
