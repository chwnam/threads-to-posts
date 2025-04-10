<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\TokenSupport;
use function Chwnam\ThreadsToPosts\ttpCall;

class AdminPostHandler implements Module
{
    /**
     * @uses TokenSupport::request()
     * @uses TokenSupport::redirectionCallback()
     */
    public function accessToken(): void
    {
        $step = sanitize_key($_GET['step'] ?? '');

        if (empty($step) && wp_verify_nonce($_GET['nonce'] ?? '', '_ttp_access_token')) {
            ttpCall(TokenSupport::class, 'request');
        }

        if ('redirect' == $step) {
            ttpCall(TokenSupport::class, 'redirectionCallback');
            wp_redirect(admin_url('admin.php?page=ttp-settings'));
            exit;
        }
    }

    /**
     * @return void
     *
     * @uses TokenSupport::refreshLongLivedToken()
     */
    public function forceRefreshToken(): void
    {
        ttpCall(TokenSupport::class, 'refreshLongLivedToken');
        wp_redirect(wp_get_referer());
    }
}
