<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
use Chwnam\ThreadsToPosts\Supports\Threads\ApiCallException;
use function Chwnam\ThreadsToPosts\ttpGet;

class ApiSupport implements Support
{
    private Api $api;

    public function __construct(TokenSupport $tokenSupport)
    {
        if ($tokenSupport->checkLongLiveTokenRefreshRequired()) {
            $tokenSupport->refreshLongLivedToken();
        }

        $this->api = ttpGet(Api::class, true);
    }

    /**
     * @throws ApiCallException
     */
    public function getThreadsPosts(string|array $args = ''): array
    {
        return $this->api->getUserThreads($args);
    }

    /**
     * @throws ApiCallException
     */
    public function getThreadsSinglePost(string $threadsMediaId, string|array $args = ''): array
    {
        return $this->api->getUserSingleThread($threadsMediaId, $args);
    }

    /**
     * @throws ApiCallException
     */
    public function getThreadsConversations(string $threadsMediaId, string|array $args = ''): array
    {
        return $this->api->getMediaConversation($threadsMediaId, $args);
    }
}
