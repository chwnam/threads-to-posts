<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

class AppUserFields extends Fields
{
    const IS_VERIFIED                 = 'is_verified';
    const NAME                        = 'name';
    const THREADS_PROFILE_PICTURE_URL = 'threads_profile_picture_url';
    const THREADS_BIOGRAPHY           = 'threads_biography';

    /**
     * @link https://developers.facebook.com/docs/threads/reference/user?locale=ko_KR#get---threads-user-id--fields-id-username----
     */
    const _ALL_FIELDS = [
        self::ID,
        self::IS_VERIFIED,
        self::USERNAME,
        self::NAME,
        self::THREADS_PROFILE_PICTURE_URL,
        self::THREADS_BIOGRAPHY,
    ];
}
