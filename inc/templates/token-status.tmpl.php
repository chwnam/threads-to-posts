<?php

use Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context
 * -------
 * - is_available:  bool   App ID, and App secret are entered. Ready to authorize.
 * - is_authorized: bool   Access token is issued and we have it.
 * - user_id:       int    Threads user ID.
 * - timestamp:     int    Timestamp when access token is issued.
 * - expires_in:    int    Expires in, in secodns.
 */
?>

<?php if (!$this->get('is_available')): ?>
    <p>Please setup Threads App ID and App Secret.</p>
    <?php return; ?>
<?php endif ?>

<?php if (!$this->get('is_authorized')): ?>
    <a id="ttp-request_token"
       class="button button-secondary"
       href="<?php echo esc_url(
           add_query_arg(
               [
                   'action' => 'ttp_access_token',
                   'nonce'  => wp_create_nonce('_ttp_access_token'),
               ],
               admin_url('admin-post.php'),
           )
       ); ?>">Authorize</a>
<?php else: ?>
    <ul class="ttp-token-status">
        <li>
            <span class="label">User ID</span><?php echo esc_html($this->get('user_id')); ?>
        </li>
        <li>
            <span class="label">Timestamp</span><?php echo esc_html(wp_date('Y-m-d H:i:s', $this->get('timestamp'))); ?>
        </li>
        <li>
            <span class="label">Expiration</span><?php echo esc_html(
                wp_date('Y-m-d H:i:s', $this->get('timestamp') + $this->get('expires_in'))
            ); ?>
        </li>
    </ul>
    <div>
        <a id="ttp-force_refresh_token"
           class="button button-secondary"
           href="<?php echo esc_url(
               add_query_arg(
                   [
                       'action' => 'ttp_force_refresh_token',
                       'nonce'  => wp_create_nonce('_ttp_force_refresh_token'),
                   ],
                   admin_url('admin-post.php')
               )
           ); ?>">Force Refresh</a>
        <a id="ttp-delete_token"
           class="button button-secondary"
           href="<?php echo esc_url(
               add_query_arg(
                   [
                       'action' => 'ttp_delete_token',
                       'nonce'  => wp_create_nonce('_ttp_delete_token'),
                   ],
                   admin_url('admin-post.php')
               )
           ); ?>">Delete Token</a>
    </div>
<?php endif; ?>
