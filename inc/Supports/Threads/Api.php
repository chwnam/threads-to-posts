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
    public function getUserThreads(): array
    {
        $r = wp_remote_get(
            add_query_arg(
                [
                    'access_token' => $this->accessToken,
                ],
                "https://graph.threads.net/v1.0/{$this->userId}/threads",
            )
        );

        $body = wp_remote_retrieve_body($r);

        return json_decode($body, true);
    }

    /**
     * @param string $threadId
     *
     * @return array
     *
     * @link https://developers.facebook.com/docs/threads/retrieve-and-discover-posts/retrieve-posts#retrieve-a-single-threads-media-object
     */
    public function getUserSingleThread(string $threadId): array
    {
        $r = wp_remote_get(
            add_query_arg(
                [
                    'access_token' => $this->accessToken,
                ],
                "https://graph.threads.net/v1.0/$threadId",
            )
        );

        $body = wp_remote_retrieve_body($r);

        return json_decode($body, true);
    }

    /**
     * @param string $mediaId
     * @param bool $reverse
     *
     * @return array
     *
     * @link https://developers.facebook.com/docs/threads/retrieve-and-manage-replies/replies-and-conversations#a-thread-s-conversations
     */
    public function getMediaConversation(string $mediaId, bool $reverse = false): array
    {
        $urlform = "https://graph.threads.net/v1.0/<MEDIA_ID>/conversation?fields=id,text,timestamp,media_product_type,media_type,media_url,shortcode,thumbnail_url,children,has_replies,root_post,replied_to,is_reply,hide_status&reverse=false&access_token=<ACCESS_TOKEN>";
    }
}
