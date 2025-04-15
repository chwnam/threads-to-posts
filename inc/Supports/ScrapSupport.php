<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;

class ScrapSupport implements Support
{
    public function __construct()
    {
    }

    /**
     * Convert Theads posts, replies to our custom posts
     *
     * Threads posts, replies have these fields:
     * - id
     * - owner.id (optionam): posts only
     * - shortcode
     * - text
     * - timestamp
     * - username (optional)
     *
     * @param array $threadsMedia
     * @return array|false
     */
    public function convertThreadsMedia(array $threadsMedia): array|false
    {
        $id        = $threadsMedia['id'] ?? '';
        $owner     = $threadsMedia['owner']['id'] ?? '';
        $shortcode = $threadsMedia['shortcode'] ?? '';
        $text      = $threadsMedia['text'] ?? '';
        $timestamp = $threadsMedia['timestamp'] ?? '';
        $username  = $threadsMedia['username'] ?? '';
        $datetime  = date_create_from_format('Y-m-d\TH:i:sO', $timestamp);

        if (!($id && $shortcode && $text && $timestamp && $datetime)) {
            return false;
        }

        $datetime->setTimezone(wp_timezone());

        $output = [
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_date'      => $datetime->format('Y-m-d H:i:s'),
            'post_content'   => $text,
            'post_status'    => 'publish',
            'post_title'     => $shortcode,
            'post_type'      => 'ttp_threads',
            'post_name'      => "ttp-$id",
            'meta_input'     => [
                '_ttp_username' => $username,
            ],
        ];

        // owner.id is not presented in reply.
        if ($owner) {
            $output['meta_input']['_ttp_owner'] = $owner;
        }

        return $output;
    }

    /**
     * Filter only user's threads from conversations
     *
     * @param array $conversations
     *
     * @return array
     */
    public function filterConversations(array $conversations): array
    {
        if (empty($conversations)) {
            return [];
        }

        $myOwn = [];

        // First pass: filter only my replites.
        foreach ($conversations as $c) {
            $id               = $c['id'] ?? '';
            $repliedTo        = $c['replied_to']['id'] ?? '';
            $rootPost         = $c['root_post']['id'] ?? '';
            $isReplyOwnedByMe = $c['is_reply_owned_by_me'] ?? false;

            if (!($id && $repliedTo && $rootPost)) {
                continue;
            }

            if ($isReplyOwnedByMe) {
                $myOwn[$id] = true;
            }
        }

        $myOwn[$conversations[0]['root_post']['id']] = true;

        // Second pass: exclude replies that are replied to others.
        $filtered = [];

        foreach ($conversations as $c) {
            $repliedTo = $c['replied_to']['id'];
            if (isset($myOwn[$repliedTo])) {
                $filtered[] = $c;
            }
        }

        return $filtered;
    }

    public function updateThreadsMedia(array $threadsMedia): void
    {
        $post = $this->convertThreadsMedia($threadsMedia);
        if (!$post) {
            return;
        }

        $posts     = get_posts("name={$post['post_name']}&numberofposts=1&post_type=ttp_threads");
        $isChanged = $posts && $posts[0]->post_content !== $post['post_content'];

        if ($isChanged) {
            $post['ID'] = $posts[0]->ID;
            wp_update_post($post);
        } else {
            wp_insert_post($post);
        }
    }

    public function updateConversations(array $conversations): void
    {
        global $wpdb;

        $conversations = $this->filterConversations($conversations);
        if (empty($conversations)) {
            return;
        }

        // Get rootPostId's wp_post.ID
        // No parent, no update
        $rootPostId = $conversations[0]['root_post']['id'];
        $query      = $wpdb->prepare(
            query: "SELECT ID FROM $wpdb->posts WHERE post_type='ttp_threads' AND post_name=%s LIMIT 0, 1",
            args:  "ttp-$rootPostId",
        );
        $postParent = $wpdb->get_var($query);
        if (!$postParent) {
            return;
        }

        // Get already inserted items.
        $names       = array_map(fn($c) => 'ttp-' . $c['id'], $conversations);
        $placeholder = implode(',', array_fill(0, count($names), '%s'));
        $query       = $wpdb->prepare(
            "SELECT post_name, ID, post_content FROM $wpdb->posts " .
            " WHERE post_type='ttp_threads' AND post_name IN ($placeholder)",
            $names,
        );
        $results     = $wpdb->get_results($query, OBJECT_K);

        foreach ($conversations as $c) {
            $post = $this->convertThreadsMedia($c);
            if (!$post) {
                continue;
            }

            $post['post_parent'] = $postParent;
            $inserted            = $results[$post['post_name']] ?? false;
            $postId              = $inserted->ID ?? false;
            $isChanged           = $inserted && $inserted->post_content !== $post['post_content'];

            if (!$postId) {
                wp_insert_post($post);
            } elseif ($isChanged) {
                $post['ID'] = $postId;
                wp_update_post($post);
            }
        }
    }
}
