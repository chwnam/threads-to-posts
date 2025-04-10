<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Modules\Options;
use Chwnam\ThreadsToPosts\Supports\Threads\Authorization;
use Exception;
use JetBrains\PhpStorm\NoReturn;

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
     * Step 1: User make a request to authorize to Threads.
     *
     * @return void
     */
    #[NoReturn]
    public function request(): void
    {
        // Redirect to Threads and stop.
        wp_redirect($this->auth->getRequestUri());
        exit;
    }

    /**
     * Step 2: Threads redirects user to our site with 'code'.
     *         Exchange code with short-lived access token.
     *         Exchange short-lived access token with long-lived access token.
     *
     * @return void
     */
    public function redirectionCallback(): void
    {
        try {
            $shortLivedToken = $this->auth->exchangeCodeWithAccessToken();
            $longLivedToken  = $this->auth->exchangeWithLongLivedToken($shortLivedToken['access_token']);

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
            $token     = $this->options->ttp_token->get();
            $refreshed = $this->auth->refreshLongLivedToken($token['access_token']);

            $refreshed['user_id'] = $token['user_id'];

            $this->options->ttp_token->update($refreshed);
        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
        // Refresh can be done by WP-cron. You cannot use redirection here.
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

        return time() > ($timestamp + $expiresIn - self::TIME_THRESH);
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
