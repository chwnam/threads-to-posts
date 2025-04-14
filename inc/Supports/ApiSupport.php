<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
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

    public function getThreadsPosts(string|array $args = ''): array
    {
        return $this->api->getUserThreads($args);
    }

    public function getThreadsSinglePost(string $threadsMediaId, string|array $args = ''): array
    {
        return $this->api->getUserSingleThread($threadsMediaId, $args);
    }

    public function getThreadsConversations(string $threadsMediaId, string|array $args = ''): array
    {
        return $this->api->getMediaConversation($threadsMediaId, $args);
    }
}
