<?php

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Supports\TokenSupport;

/**
 * @var Template $this
 *
 * Context
 * -------
 * -
 */
?>

<ul class="ttp-callback-url-guide">
    <li>
        <label for="ttp-redirect_callback_url" class="label">Redirect Callback URL</label><input
                id="ttp-redirect_callback_url"
                class="text regular-text"
                readonly="readonly"
                title="Redirect callback URL"
                type="text"
                value="<?php echo esc_url(TokenSupport::getRedirectionCallbackUrl()); ?>">
    </li>
    <li>
        <label for="ttp-uninstall_callback_url" class="label">Uninstall Callback URL</label><input
                id="ttp-uninstall_callback_url"
                class="text regular-text"
                readonly="readonly"
                title="Uninstall callback URL"
                type="text"
                value="<?php echo esc_url(TokenSupport::getUninstallCallbackUrl()); ?>">
    </li>
    <li>
        <label for="ttp-delete_callback_url" class="label">Delete Callback URL</label><input
                id="ttp-delete_callback_url"
                class="text regular-text"
                readonly="readonly"
                title="Delete callback URL"
                type="text"
                value="<?php echo esc_url(TokenSupport::getDeleteCallbackUrl()); ?>">
    </li>
</ul>
