<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

use Exception;
use Monolog\Logger;
use function Chwnam\ThreadsToPosts\ttpGetLogger;

class Authorization extends ApiBase
{
    private Logger $logger;

    /**
     * Constroctor
     *
     * These params can be found in you App dashboard > Use cases > Customize > Settings
     *
     * @param string $appId                App ID
     * @param string $appSecret            App Secret
     * @param string $redirectCallbackUrl  Redirection callback URI
     * @param string $uninstallCallbackUrl Uninstall callback URI
     * @param string $deleteCallbackUrl    Delete callback URI
     */
    public function __construct(
        private string $appId,
        private string $appSecret,
        private string $redirectCallbackUrl,
        private string $uninstallCallbackUrl,
        private string $deleteCallbackUrl,
    )
    {
        $this->logger = ttpGetLogger();
    }

    /**
     * Step 1: Get authorization
     *
     * @return string
     * @link https://developers.facebook.com/docs/threads/get-started/get-access-tokens-and-permissions#step-1--get-authorization
     */
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
     * Step 2: Exchangeo the code for a token
     *
     * @param string $state
     * @param string $code
     *
     * @return array{
     *     access_token: string,
     *     user_id: string,
     * }
     * @throws Exception
     * @link https://developers.facebook.com/docs/threads/get-started/get-access-tokens-and-permissions#step-2--exchange-the-code-for-a-token
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

        /**
         * @var array{
         *     access_token: string,
         *     user_id: string,
         * } $data
         */
        $data = $this->request(
            'https://graph.threads.net/oauth/access_token',
            [
                'method' => 'POST',
                'body'   => [
                    'client_id'     => $this->appId,
                    'client_secret' => $this->appSecret,
                    'code'          => $code,
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => $this->redirectCallbackUrl,
                ],
            ]
        );

        $this->logger->debug('short-lived token acquired: ' . self::filter($data));

        return [
            'access_token' => $data['access_token'],
            'user_id'      => $data['user_id'],
        ];
    }

    /**
     * Get a long-lived token
     *
     * @param string $accessToken
     *
     * @return array{
     *     acccess_token: string,
     *     token_type: string,
     *     expires_in: int,
     *     timestamp: int,
     * }
     * @throws Exception
     * @link https://developers.facebook.com/docs/threads/get-started/long-lived-tokens#get-a-long-lived-token
     */
    public function exchangeWithLongLivedToken(string $accessToken): array
    {
        /**
         * @var array{
         *     access_token: string,
         *     token_type: string,
         *     expires_in: int,
         * } $data
         */

        $data = $this->request(
            'https://graph.threads.net/access_token',
            [
                'method' => 'GET',
                'body'   => [
                    'grant_type'    => 'th_exchange_token',
                    'client_secret' => $this->appSecret,
                    'access_token'  => $accessToken,
                ]
            ]
        );

        $data['timestamp'] = time();

        $this->logger->debug('long-lived token acquired: ' . self::filter($data));

        return $data;
    }

    /**
     * Refresh a long-lived token
     *
     * @param string $accessToken
     *
     * @return array{
     *     acccess_token: string,
     *     token_type: string,
     *     expires_in: int,
     *     timestamp: int,
     * }
     * @throws Exception
     * @link https://developers.facebook.com/docs/threads/get-started/long-lived-tokens#refresh-a-long-lived-token
     */
    public function refreshLongLivedToken(string $accessToken): array
    {
        /**
         * @var array{
         *     access_token: string,
         *     token_type: string,
         *     expires_in: int,
         * } $data
         */
        $data = $this->request(
            'https://graph.threads.net/refresh_access_token',
            [
                'method' => 'GET',
                'body'   => [
                    'grant_type'   => 'th_refresh_token',
                    'access_token' => $accessToken,
                ]
            ]
        );

        $data['timestamp'] = time();

        $this->logger->debug('long-lived token refreshed: ' . self::filter($data));

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

        $key   = '_ttp_auth_state_' . $id;
        $saved = get_transient($key);
        delete_transient($key);

        return $saved === $value;
    }

    private static function filter(array $data): string
    {
        if (isset($data['access_token'])) {
            $data['access_token'] = '******';
        }

        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}
