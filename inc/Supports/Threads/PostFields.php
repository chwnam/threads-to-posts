<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class PostFields extends Fields
{
    const ALT_TEXT            = 'alt_text';
    const LINK_ATTACHMENT_URL = 'link_attachment_url';
    const OWNER               = 'owner';
    const QUOTED_POST         = 'quoted_post';
    const REPOSTED_POST       = 'reposted_post';

    /**
     * @link https://developers.facebook.com/docs/threads/retrieve-and-discover-posts/retrieve-posts#fields
     */
    const _ALL_FIELDS = [
        self::ID,
        self::MEDIA_PRODUCT_TYPE,
        self::MEDIA_TYPE,
        self::MEDIA_URL,
        self::PERMALINK,
        self::OWNER,
        self::USERNAME,
        self::TEXT,
        self::TIMESTAMP,
        self::SHORTCODE,
        self::THUMBNAIL_URL,
        self::CHILDREN,
        self::IS_QUOTE_POST,
        self::QUOTED_POST,
        self::REPOSTED_POST,
        self::ALT_TEXT,
        self::LINK_ATTACHMENT_URL,
        self::GIF_URL,
    ];
}