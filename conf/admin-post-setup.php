<?php

if (!defined('ABSPATH')) exit;

use Bojaghi\AdminAjax\SubmitBase;
use Chwnam\ThreadsToPosts\Modules\AdminPostHandler;

return [
    /**
     * We are not going to use 'Content-Type: application/json', so that we can safely disable this feature.
     */
    'checkContentType' => false,

    /**
     * Action request access token (long-lived token)
     *
     * @uses AdminPostHandler::accessToken()
     * @uses AdminPostHandler::deleteToken()
     * @uses AdminPostHandler::forceRefreshToken()
     * @uses AdminPostHandler::updateScrapMode()
     */
    [
        'ttp_access_token',                 // action
        'ttp/adminPostHandler@accessToken', // callback
        SubmitBase::ONLY_PRIV,              // logged-in user only
        // no automatic nonce check
        // default priority
    ],

    /**
     * Action request for forcibly refresing long-lived access token
     */
    [
        'ttp_force_refresh_token',                // action
        'ttp/adminPostHandler@forceRefreshToken', // callback
        SubmitBase::ONLY_PRIV,                    // logged-in user only
        '_ttp_force_refresh_token'                // Automatic nonce check
        // default priority
    ],

    /**
     * Action request for deleting access token
     */
    [
        'ttp_delete_token',                 // action
        'ttp/adminPostHandler@deleteToken', // callback
        SubmitBase::ONLY_PRIV,              // logged-in user only
        '_ttp_delete_token'                 // Automatic nonce check
        // default priority
    ],

    /**
     * Scrap mode change
     */
    [
        'ttp_update_scrap_mode',                // action
        'ttp/adminPostHandler@updateScrapMode', // callback
        SubmitBase::ONLY_PRIV,                  // logged-in user only
        '_ttp_update_scrap_mode'                // Automatic nonce check
        // default priority
    ],
];
