<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class PostFields extends Fields
{
    const ALT_TEXT            = 'alt_text';
    const CHILDREN            = 'children';
    const GIF_URL             = 'gif_url';
    const IS_QUOTE_POST       = 'is_quote_post';
    const LINK_ATTACHMENT_URL = 'link_attachment_url';
    const MEDIA_PRODUCT_TYPE  = 'media_product_type';
    const MEDIA_TYPE          = 'media_type';
    const MEDIA_URL           = 'media_url';
    const OWNER               = 'owner';
    const PERMALINK           = 'permalink';
    const POLL_ATTACHMENT     = 'poll_attachment';
    const QUOTED_POST         = 'quoted_post';
    const REPOSTED_POST       = 'reposted_post';
    const SHORTCODE           = 'shortcode';
    const TEXT                = 'text';
    const THUMBNAIL_URL       = 'thumbnail_url';
    const TIMESTAMP           = 'timestamp';
    const TOPIC_TAG           = 'topic_tag';

    /* Media types */
    const MEDIA_TYPE_AUDIO          = 'AUDIO';
    const MEDIA_TYPE_CAROUSEL_ALBUM = 'CAROUSEL_ALBUM';
    const MEDIA_TYPE_IMAGE          = 'IMAGE';
    const MEDIA_TYPE_REPOST_FACADE  = 'REPOST_FACADE';
    const MEDIA_TYPE_TEXT_POST      = 'TEXT_POST';
    const MEDIA_TYPE_VIDEO          = 'VIDEO';

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
        self::POLL_ATTACHMENT,
        self::TOPIC_TAG,
    ];
}