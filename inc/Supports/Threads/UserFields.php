<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class UserFields extends Fields
{
    const NAME                        = 'name';
    const THREADS_PROFILE_PICTURE_URL = 'threads_profile_picture_url';
    const THREADS_BIOGRAPHY           = 'threads_biography';
    const RECENTLY_SEARCHED_KEYWORDS  = 'recently_searched_keywords';

    /**
     * @link https://developers.facebook.com/docs/threads/reference/user?locale=ko_KR#get---threads-user-id--fields-id-username----
     */
    const _ALL_FIELDS = [
        self::ID,
        self::USERNAME,
        self::NAME,
        self::THREADS_PROFILE_PICTURE_URL,
        self::THREADS_BIOGRAPHY,
        // You cannot include this field with calling 'me' API.
        // self::RECENTLY_SEARCHED_KEYWORDS,
    ];
}