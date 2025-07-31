<?php

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template\Template;

/**
 *
 *
 * @var Template $this
 *
 *  Context
 *  -------
 *  - back_link: string
 *  - show_embled: bool
 *  - id: string
 *  - is_quote_post: string
 *  - media_type: string
 *  - owner: stirng
 *  - permalink: string
 *  - quoted_post_id: string
 *  - reposted_post_id: string
 *  - shortcode: string
 *  - text: string
 *  - timestamp: string
 *  - username: string
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="ttp-admin-single-container">
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
                Media Type
            </th>
            <td>
                <?php echo esc_html($this->get('media_type')); ?>
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
        <tr>
            <th scope="row">
                Media URL
            </th>
            <td>
                <?php if ($this->get('media_url')) : ?>
                    <a href="<?php echo esc_url($this->get('media_url')); ?>"
                       target="ttp-media_url">
                        <?php echo esc_html($this->get('media_url')); ?>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                Repost ID
            </th>
            <td>
                <?php if ($this->get('reposted_post_id')) : ?>
                    <?php echo esc_html($this->get('reposted_post_id')); ?>
                <?php elseif ('REPOST_FACADE' === $this->get('media_type')) : ?>
                    Not available, reposted from the other user's post.
                <?php else: ?>
                    Not reposted
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                Quoted Post
            </th>
            <td>
                <?php if (filter_var($this->get('is_quote_post'), FILTER_VALIDATE_BOOLEAN)) : ?>
                    Yes,
                    <?php if ($this->get('quoted_post_id')) : ?>
                        and it is from your post <?php echo esc_html($this->get('quoted_post_id')); ?>.
                    <?php else: ?>
                        and it is from the other user's post.
                    <?php endif; ?>
                <?php else : ?>
                    No.
                <?php endif; ?>

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
    <div id="ttp-fetch-article-container">
        <h3>Fetch Article</h3>
        <button
                id="ttp-fetch-article"
                class="button button-primary"
                data-id="<?php echo esc_attr($this->get('id')); ?>"
                data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_fetch_article')); ?>">
            Fetch Now
        </button>
        <div>
            <pre id="ttp-fetch-article-result">
            </pre>
        </div>
    </div>
</div>
