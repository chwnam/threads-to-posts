<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Modules\Options;
use Chwnam\ThreadsToPosts\Supports\Threads\Authorization;
use Exception;

class TokenSupport implements Support
{
    const TIME_THRESH = 604800; // 7 days

    public function __construct(
        private Authorization $auth,
        private Options       $options,
    )
    {
    }

    /**
     * Step 1: User makes a request to authorize to Threads.
     *
     * @return string
     */
    public function getAuthorization(): string
    {
        return $this->auth->getRequestUri();
    }

    /**
     * Step 2: Threads redirects user to our site with 'code'.
     *         Exchange code with a short-lived access token.
     *         Exchange a short-lived access token with a long-lived access token.
     *
     * @return void
     */
    public function redirectionCallback(): void
    {
        try {
            $shortLivedToken = $this->auth->exchangeCodeWithAccessToken();
            sleep(2);

            $longLivedToken            = $this->auth->exchangeWithLongLivedToken($shortLivedToken['access_token']);
            $longLivedToken['user_id'] = $shortLivedToken['user_id'];

            $this->options->ttp_token->update($longLivedToken);
        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
        // As redirections may appear more than once, referrer data would not be safe to return to the admin.
    }

    public function refreshLongLivedToken(): void
    {
        try {
            $token                = $this->options->ttp_token->get();
            $refreshed            = $this->auth->refreshLongLivedToken($token['access_token']);
            $refreshed['user_id'] = $token['user_id'];

            $this->options->ttp_token->update($refreshed);
        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
        // WP-cron may use this method. You cannot use redirection here.
    }

    public function checkLongLiveTokenRefreshRequired(): bool
    {
        $token       = $this->options->ttp_token->get();
        $accessToken = $token['access_token'] ?? '';
        $expiresIn   = $token['expires_in'] ?? 0;
        $timestamp   = $token['timestamp'] ?? 0;

        if (!($accessToken && $expiresIn && $timestamp)) {
            return false;
        }

        $now        = time();
        $expiration = $timestamp + $expiresIn;
        $threshold  = $expiration - self::TIME_THRESH;

        return $threshold < $now && $now < $expiration;
    }

    public static function getRedirectionCallbackUrl(): string
    {
        return add_query_arg(
            [
                'action' => 'ttp_access_token',
                'step'   => 'redirect',
            ],
            admin_url('admin-post.php'),
        );
    }

    public static function getUninstallCallbackUrl(): string
    {
        return add_query_arg(
            [
                'action' => 'ttp_access_token',
                'step'   => 'uninstall',
            ],
            admin_url('admin-post.php'),
        );
    }

    public static function getDeleteCallbackUrl(): string
    {
        return add_query_arg(
            [
                'action' => 'ttp_access_token',
                'step'   => 'delete',
            ],
            admin_url('admin-post.php'),
        );
    }
}
