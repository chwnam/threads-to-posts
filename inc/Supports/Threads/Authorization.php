<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

use Bojaghi\Contract\Support;
use Exception;

class Authorization implements Support
{
    /**
     * Constroctor
     *
     * These params can be found in you App dashboard > Use cases > Customize > Settings
     *
     * @param string $appId App ID
     * @param string $appSecret App Secret
     * @param string $redirectCallbackUrl Redirection callback URI
     * @param string $uninstallCallbackUrl Uninstall callback URI
     * @param string $deleteCallbackUrl Delete callback URI
     */
    public function __construct(
        private string $appId,
        private string $appSecret,
        private string $redirectCallbackUrl,
        private string $uninstallCallbackUrl,
        private string $deleteCallbackUrl,
    )
    {
    }

    public function getRequestUri(): string
    {
        return add_query_arg(
            urlencode_deep(
                [
                    'client_id'     => $this->appId,
                    'redirect_uri'  => $this->redirectCallbackUrl,
                    'scope'         => 'threads_basic,threads_read_replies',
                    'response_type' => 'code',
                    'state'         => self::generateState(),
                ],
            ),
            'https://threads.net/oauth/authorize',
        );
    }

    /**
     * @param string $state
     * @param string $code
     *
     * @return array{access_token: string, user_id: string}
     * @throws Exception
     */
    public function exchangeCodeWithAccessToken(string $code = '', string $state = ''): array
    {
        if (empty($code)) {
            $code = sanitize_text_field($_GET['code'] ?? '');
        }

        if (empty($state)) {
            $state = sanitize_text_field($_GET['state'] ?? '');
        }

        if (!self::verifyState($state)) {
            throw new Exception('Invalid state');
        }

        self::removeState($state);

        $r = wp_remote_post(
            'https://graph.threads.net/oauth/access_token',
            [
                'body' => [
                    'client_id'     => $this->appId,
                    'client_secret' => $this->appSecret,
                    'code'          => $code,
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => $this->redirectCallbackUrl,
                ],
            ],
        );

        $responseCode = wp_remote_retrieve_response_code($r);
        $resposeBody  = wp_remote_retrieve_body($r);

        if (200 !== $responseCode) {
            throw new Exception('Access token request failed with code: ' . $responseCode);
        }

        $data        = json_decode($resposeBody, true);
        $accessToken = $data['access_token'] ?? '';
        $userId      = $data['user_id'] ?? '';

        return [
            'access_token' => $accessToken,
            'user_id'      => $userId,
        ];
    }

    /**
     * @param string $accessToken
     *
     * @return array{acccess_token: string, token_type: string, expires_in: int, timestamp: int}
     * @throws Exception
     */
    public function exchangeWithLongLivedToken(string $accessToken): array
    {
        $r = wp_remote_get(
            add_query_arg(
                [
                    'grant_type'    => 'th_exchange_token',
                    'client_secret' => $this->appSecret,
                    'access_token'  => $accessToken,
                ],
                'https://graph.threads.net/access_token',
            )
        );

        $responseCode = wp_remote_retrieve_response_code($r);
        $resposeBody  = wp_remote_retrieve_body($r);

        if (200 !== $responseCode) {
            throw new Exception('Access token request failed with code: ' . $responseCode);
        }

        $data = wp_parse_args(
            json_decode($resposeBody, true),
            [
                'access_token' => '',
                'token_type'   => '',
                'expires_in'   => 0,
                'timestamp'    => 0,
                'user_id'      => '',
            ]
        );

        $data['timestamp'] = time();

        return $data;
    }

    /**
     * @throws Exception
     */
    public function refreshLongLivedToken(string $accessToken): array
    {
        $r = wp_remote_get(
            add_query_arg(
                [
                    'grant_type'   => 'th_refresh_token',
                    'access_token' => $accessToken,
                ],
                'https://graph.threads.net/refresh_access_token',
            )
        );

        $responseCode = wp_remote_retrieve_response_code($r);
        $resposeBody  = wp_remote_retrieve_body($r);

        if (200 !== $responseCode) {
            throw new Exception('Access token request failed with code: ' . $responseCode);
        }

        $data = wp_parse_args(
            json_decode($resposeBody, true),
            [
                'access_token' => '',
                'token_type'   => '',
                'expires_in'   => 0,
                'timestamp'    => 0,
                'user_id'      => '',
            ]
        );

        $data['timestamp'] = time();

        return $data;
    }

    private static function generateState(): string
    {
        $id    = time() . wp_generate_password(8, false);
        $value = wp_generate_password(12, false);

        set_transient('_ttp_auth_state_' . $id, $value, 10 * MINUTE_IN_SECONDS);

        return "$id:$value";
    }

    private static function verifyState(string $state): bool
    {
        $exploded = explode(':', $state, 2);
        $id       = $exploded[0] ?? '';
        $value    = $exploded[1] ?? '';

        return get_transient('_ttp_auth_state_' . $id) === $value;
    }

    private static function removeState(string $state): void
    {
        $exploded = explode(':', $state, 2);
        $id       = $exploded[0] ?? '';

        delete_transient('_ttp_auth_state_' . $id);
    }
}
