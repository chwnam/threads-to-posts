<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class ConversationsFields extends PostFields
{
    const HAS_REPLIES          = 'has_replies';
    const HIDE_STATUS          = 'hide_status';
    const IS_REPLY             = 'is_reply';
    const IS_REPLY_OWNED_BY_ME = 'is_reply_owned_by_me';
    const REPLIED_TO           = 'replied_to';
    const REPLY_AUDIENCE       = 'reply_audience';
    const ROOT_POST            = 'root_post';

    /**
     * @link https://developers.facebook.com/docs/threads/retrieve-and-manage-replies/replies-and-conversations#fields
     */
    const _ALL_FIELDS = [
        self::ID,
        self::TEXT,
        self::USERNAME,
        self::PERMALINK,
        self::TIMESTAMP,
        self::MEDIA_PRODUCT_TYPE,
        self::MEDIA_TYPE,
        self::MEDIA_URL,
        self::SHORTCODE,
        self::THUMBNAIL_URL,
        self::CHILDREN,
        self::IS_QUOTE_POST,
        self::HAS_REPLIES,
        self::ROOT_POST,
        self::REPLIED_TO,
        self::IS_REPLY,
        self::IS_REPLY_OWNED_BY_ME,
        self::HIDE_STATUS,
        self::REPLY_AUDIENCE,
        self::GIF_URL,
        self::POLL_ATTACHMENT,
        self::TOPIC_TAG,
    ];
}