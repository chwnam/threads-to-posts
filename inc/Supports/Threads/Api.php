<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class Api extends ApiBase
{
    public function __construct(
        private string $accessToken,
        private string $userId,
    )
    {
    }

    private function makeUrl(string $baseUrl, string|array $args, array $defaults): string
    {
        $args = array_filter(wp_parse_args($args, $defaults), fn($v) => !!$v);
        $args = array_intersect_key($args, $defaults);

        return add_query_arg(
            array_merge($args, ['access_token' => $this->accessToken]),
            $baseUrl
        );
    }

    /**
     * @link https://developers.facebook.com/docs/threads/retrieve-and-discover-posts/retrieve-posts
     *
     * @return array{
     *   data: array{
     *     id: string,
     *   }[],
     *   paging: array{
     *     cursor: array{ before: string, after: string },
     *     next: string,
     *   }
     * }
     */
    public function getUserThreads(string|array $args = ''): array
    {
        $defaults = [
            'after'  => '',
            'before' => '',
            'fields' => '',
            'limit'  => 0,
            'since'  => '',
            'until'  => '',
        ];

        $url = $this->makeUrl(
            baseUrl:  "https://graph.threads.net/v1.0/{$this->userId}/threads",
            args:     $args,
            defaults: $defaults,
        );

        $r    = wp_remote_get($url);
        $body = wp_remote_retrieve_body($r);

        return json_decode($body, true);
    }

    /**
     * @param string $threadsMediaId
     * @param string|array $args
     *
     * @return array
     *
     * @link https://developers.facebook.com/docs/threads/retrieve-and-discover-posts/retrieve-posts#retrieve-a-single-threads-media-object
     */
    public function getUserSingleThread(string $threadsMediaId, string|array $args = ''): array
    {
        $defaults = [
            'fields' => '',
        ];

        $url = $this->makeUrl(
            baseUrl:  "https://graph.threads.net/v1.0/$threadsMediaId",
            args:     $args,
            defaults: $defaults,
        );

        $r    = wp_remote_get($url);
        $body = wp_remote_retrieve_body($r);

        return json_decode($body, true);
    }

    /**
     * @param string $threadsMediaId
     * @param array|string $args
     * @return array
     *
     * @link https://developers.facebook.com/docs/threads/retrieve-and-manage-replies/replies-and-conversations#a-thread-s-conversations
     */
    public function getMediaConversation(string $threadsMediaId, array|string $args = ''): array
    {
        $defaults = [
            'fields'  => '',
            'reverse' => 'false',
        ];

        if (isset($args['reverse']) && is_bool($args['reverse'])) {
            $args['reverse'] = $args['reverse'] ? 'true' : 'false';
        }

        $url = $this->makeUrl(
            baseUrl:  "https://graph.threads.net/v1.0/$threadsMediaId/conversation",
            args:     $args,
            defaults: $defaults,
        );

        $r    = wp_remote_get($url);
        $body = wp_remote_retrieve_body($r);

        return json_decode($body, true);
    }
}
