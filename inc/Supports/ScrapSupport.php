<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Chwnam\ThreadsToPosts\Supports\Threads\Crawler;
use Chwnam\ThreadsToPosts\Supports\Threads\PostFields;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Support;
use Exception;

class ScrapSupport implements Support
{
    public function __construct(private Crawler $crawler, private bool $enableRepostFetch = false)
    {
    }

    /**
     * Convert Theads posts, replies to our custom posts
     *
     * Threads posts, replies have these fields:
     * - id
     * - owner.id (optional): posts only
     * - shortcode
     * - text
     * - timestamp
     * - username (optional)
     *
     * @param array $threadsMedia
     *
     * @return array|false
     */
    public function convertThreadsMedia(array $threadsMedia): array|false
    {
        $id                = $threadsMedia['id'] ?? '';
        $mediaType         = $threadsMedia['media_type'] ?? '';
        $mediaUrl          = $threadsMedia['media_url'] ?? '';
        $owner             = $threadsMedia['owner']['id'] ?? '';
        $shortcode         = $threadsMedia['shortcode'] ?? '';
        $text              = $threadsMedia['text'] ?? '';
        $timestamp         = $threadsMedia['timestamp'] ?? '';
        $username          = $threadsMedia['username'] ?? '';
        $repostedPostId    = $threadsMedia['reposted_post']['id'] ?? '';
        $isQuotePost       = $threadsMedia['is_quote_post'] ?? false;
        $quotedPostId      = $threadsMedia['quoted_post']['id'] ?? '';
        $linkAttachmentUrl = $threadsMedia['link_attachment_url'] ?? '';
        $toplicTag         = $threadsMedia['topic_tag'] ?? '';
        $datetime          = date_create_from_format('Y-m-d\TH:i:sO', $timestamp);

        if (!($id && $shortcode && $timestamp && $datetime)) {
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
                '_ttp_timestamp'     => time(),
                '_ttp_is_quote_post' => $isQuotePost,
                '_ttp_media_type'    => $mediaType,
                '_ttp_username'      => $username,
            ],
        ];

        // owner.id is not presented in reply.
        if ($owner) {
            $output['meta_input']['_ttp_owner'] = $owner;
        }

        // 'reposted_post' appeart only when you repost your own posts.
        if ($repostedPostId) {
            $output['meta_input']['_ttp_reposted_post_id'] = $repostedPostId;
        }

        // 'quoted_post' appears only  when you quote your own posts.
        if ($isQuotePost && $quotedPostId) {
            $output['meta_input']['_ttp_quoted_post_id'] = $quotedPostId;
        }

        // 'media_url' field
        if ($mediaUrl) {
            $output['meta_input']['_ttp_media_url'] = $mediaUrl;
        }

        // 'link_attachment_url' field
        if ($linkAttachmentUrl) {
            $output['meta_input']['_ttp_link_attachment_url'] = $linkAttachmentUrl;
        }

        // 'toplic_tag' field
        if ($toplicTag) {
            $output['meta_input']['_ttp_topic_tag'] = $toplicTag;
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
            $repliedTo = $c['replied_to']['id'] ?? false;
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

        $posts = get_posts("name={$post['post_name']}&numberposts=1&post_type=ttp_threads");
        $isNew = empty($posts);

        // Raw scrap if post that is not my own is reposted.
        // The other user's posts that are reposted havw no 'reposted_post' field.
        $mediaType    = $threadsMedia['media_type'] ?? '';
        $repostedPost = $threadsMedia['reposted_post']['id'] ?? '';
        $permalink    = $threadsMedia['permalink'] ?? '';

        if (
            $this->enableRepostFetch &&
            PostFields::MEDIA_TYPE_REPOST_FACADE === $mediaType &&
            empty($repostedPost) &&
            $permalink
        ) {
            try {
                $post['post_content'] = $this->crawler->fetch($permalink)->extractOgDescription();
                sleep(1); // Be polite.
            } catch (Exception) {
                // Skip
            }
        }

        if ($isNew) {
            wp_insert_post($post);
        } else {
            $post['ID'] = $posts[0]->ID;
            wp_update_post($post);
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

        // 1st pass: get my own replies
        $myOwnReplies = [
            // Add root post id, too.
            $conversations[0]['root_post']['id'] => true
        ];
        foreach ($conversations as $c) {
            if ($c['is_reply_owned_by_me']) {
                $myOwnReplies[$c['id']] = true;
            }
        }

        // 2nd pass: get replies that are replied to me.
        $repliedToMe = [];
        foreach ($conversations as $c) {
            $isReplyOwnedByMe = $c['is_reply_owned_by_me'];
            $repliedTo        = $c['replied_to']['id'];
            if ($isReplyOwnedByMe && isset($myOwnReplies[$repliedTo])) {
                $repliedToMe[] = $c;
            }
        }

        // 3rd pass: update only my own replies.
        foreach ($repliedToMe as $c) {
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
