<?php

namespace Chwnam\ThreadsToPosts\Tests;

use Chwnam\ThreadsToPosts\Supports\ScrapSupport;
use WP_UnitTestCase;
use function Chwnam\ThreadsToPosts\ttpGet;

class TestScrapSupport extends WP_UnitTestCase
{
    private $scrap;

    public function setUp(): void
    {
        // IMPORTANT: Set timezone.
        update_option('timezone_string', 'Asia/Seoul');

        $this->scrap = ttpGet(ScrapSupport::class);
    }

    /**
     * Test conversion against Threads post
     *
     * Post has _ttp_owner
     *
     * @return void
     */
    public function test_convertThreadsMedia(): void
    {
        $jsonText = static::getSampleMedia();
        $media    = json_decode($jsonText, true);
        $output   = $this->scrap->convertThreadsMedia($media);

        // Assert output structure.
        $this->assertIsArray($output);
        $this->assertArrayHasKey('comment_status', $output);
        $this->assertArrayHasKey('ping_status', $output);
        $this->assertArrayHasKey('post_date', $output);
        $this->assertArrayHasKey('post_content', $output);
        $this->assertArrayHasKey('post_status', $output);
        $this->assertArrayHasKey('post_title', $output);
        $this->assertArrayHasKey('post_type', $output);
        $this->assertArrayHasKey('post_name', $output);
        $this->assertArrayHasKey('meta_input', $output);
        $this->assertIsArray($output['meta_input']);
        $this->assertArrayHasKey('_ttp_owner', $output['meta_input']);
        $this->assertArrayHasKey('_ttp_username', $output['meta_input']);

        // Assert output values
        $this->assertEquals('closed', $output['comment_status']);
        $this->assertEquals('closed', $output['ping_status']);
        $this->assertEquals('2025-04-14 15:20:50', $output['post_date']);
        $this->assertEquals($media['text'], $output['post_content']);
        $this->assertEquals('publish', $output['post_status']);
        $this->assertEquals($media['shortcode'], $output['post_title']);
        $this->assertEquals('ttp_threads', $output['post_type']);
        $this->assertEquals('ttp-' . $media['id'], $output['post_name']);
        $this->assertEquals($media['owner']['id'], $output['meta_input']['_ttp_owner']);
        $this->assertEquals($media['username'], $output['meta_input']['_ttp_username']);
    }

    public function test_updateThreadsMedia(): void
    {
        $jsonText = static::getSampleMedia();
        $media    = json_decode($jsonText, true);
        $this->scrap->updateThreadsMedia($media);

        // Check update
        $posts = get_posts("numberposts=1&post_type=ttp_threads&name=ttp-{$media['id']}");
        $this->assertNotEmpty($posts);
        $this->assertEquals('ttp-' . $media['id'], $posts[0]->post_name);

        // Text update.
        $media['text'] = 'Updated Text';

        $this->scrap->updateThreadsMedia($media);
        $posts = get_posts("numberofposts=1&post_type=ttp_threads&name=ttp-{$media['id']}");
        $this->assertEquals('Updated Text', $posts[0]->post_content);
    }

    private static function getSampleMedia(): string
    {
        return '{
            "id": "17888331330239479",
            "media_product_type": "THREADS",
            "media_type": "TEXT_POST",
            "permalink": "https://www.threads.com/@test_user/post/DIaq4o4T55X",
            "owner": {
                "id": "9491711744278740"
            },
            "username": "test_user",
            "text": "Threads text",
            "timestamp": "2025-04-14T06:20:50+0000",
            "shortcode": "DIaq4o4T55X",
            "is_quote_post": false
        }';
    }

    /**
     * Test conversion against Threads reply
     *
     * Post does not have _ttp_owner
     *
     * @return void
     */
    public function test_convertThreadsMediaReply(): void
    {
        $jsonText = self::getSampleMediaReply();
        $media    = json_decode($jsonText, true);
        $output   = $this->scrap->convertThreadsMedia($media);

        // Assert output structure.
        $this->assertIsArray($output);
        $this->assertArrayHasKey('comment_status', $output);
        $this->assertArrayHasKey('ping_status', $output);
        $this->assertArrayHasKey('post_date', $output);
        $this->assertArrayHasKey('post_content', $output);
        $this->assertArrayHasKey('post_status', $output);
        $this->assertArrayHasKey('post_title', $output);
        $this->assertArrayHasKey('post_type', $output);
        $this->assertArrayHasKey('post_name', $output);
        $this->assertArrayHasKey('meta_input', $output);
        $this->assertIsArray($output['meta_input']);
        $this->assertArrayNotHasKey('_ttp_owner', $output['meta_input']);
        $this->assertArrayHasKey('_ttp_username', $output['meta_input']);

        // Assert output values
        $this->assertEquals('closed', $output['comment_status']);
        $this->assertEquals('closed', $output['ping_status']);
        $this->assertEquals('2025-04-09 16:12:33', $output['post_date']);
        $this->assertEquals($media['text'], $output['post_content']);
        $this->assertEquals('publish', $output['post_status']);
        $this->assertEquals($media['shortcode'], $output['post_title']);
        $this->assertEquals('ttp_threads', $output['post_type']);
        $this->assertEquals('ttp-' . $media['id'], $output['post_name']);
        $this->assertEquals($media['username'], $output['meta_input']['_ttp_username']);
    }

    public function test_updateThreadsMediaReply(): void
    {
        $threads = json_decode(static::getSampleMedia(), true);
        $reply   = json_decode(static::getSampleMediaReply(), true);

        $this->scrap->updateThreadsMedia($threads);
        $this->scrap->updateConversations([$reply]);

        // Check update
        $parent = get_posts("numberposts=1&post_type=ttp_threads&name=ttp-{$threads['id']}");
        $posts  = get_posts("numberposts=1&post_type=ttp_threads&name=ttp-{$reply['id']}");
        $this->assertNotEmpty($posts);
        $this->assertEquals('ttp-' . $reply['id'], $posts[0]->post_name);
        $this->assertEquals($parent[0]->ID, $posts[0]->post_parent);

        // Text update.
        $reply['text'] = 'Updated Text';

        $this->scrap->updateConversations([$reply]);
        $posts = get_posts("numberposts=1&post_type=ttp_threads&name=ttp-{$reply['id']}");
        $this->assertEquals($reply['text'], $posts[0]->post_content);
    }

    public static function getSampleMediaReply(): string
    {
        return '{
            "id": "12345677890",
            "text": "Test Text",
            "username": "test_user",
            "permalink": "https://www.threads.com/@test_user/post/DIN40-PT9oT",
            "timestamp": "2025-04-09T07:12:33+0000",
            "media_product_type": "THREADS",
            "media_type": "TEXT_POST",
            "shortcode": "DIN40-PT9oT",
            "is_quote_post": false,
            "has_replies": true,
            "root_post": {"id": "17888331330239479"},
            "replied_to": {"id": "17888331330239479"},
            "is_reply": true,
            "is_reply_owned_by_me": true,
            "hide_status": "NOT_HUSHED"
        }';
    }
}