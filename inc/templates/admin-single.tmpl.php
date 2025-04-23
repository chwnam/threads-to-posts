<?php

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template\Template;

/**
 *
 *
 * @var Template $this
 *
 *  Context
 *  -------
 *  - id: string
 *  - owner: stirng
 *  - username: string
 *  - text: string
 *  - shortcode: string
 *  - timestamp: string
 *  - permalink: string
 *  - back_link: string
 *  - show_embled: bool
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<table class="form-table" role="presentation">
    <tbody>
    <tr>
        <th scope="row">
            ID
        </th>
        <td>
            <?php echo esc_html($this->get('id')); ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            Owner
        </th>
        <td>
            <?php echo esc_html($this->get('owner')); ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            Username
        </th>
        <td>
            <?php echo esc_html($this->get('username')); ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            Text
        </th>
        <td>
            <?php echo wp_kses_post($this->get('text')); ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            Timestamp
        </th>
        <td>
            <?php echo esc_html($this->get('timestamp')); ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            Permalink
        </th>
        <td>
            <a href="<?php echo esc_url($this->get('permalink')); ?>"
               target="ttp-permalink">
                <?php echo esc_html($this->get('permalink')); ?>
            </a>
        </td>
    </tr>
    <?php if ($this->get('show_embled')) : ?>
        <tr>
            <th scope="row">
                Embed
            </th>
            <td>
                <?php echo $this->fragment('embed'); ?>
            </td>
        </tr>
    <?php endif; ?>
    <!-- <tr>
        <th scope="row">
        </th>
        <td>
        </td>
    </tr> -->
    </tbody>
</table>
<p class="submit">
    <a class="button button-primary"
       href="<?php echo esc_url($this->get('back_link')); ?>">
        Back
    </a>
</p>
