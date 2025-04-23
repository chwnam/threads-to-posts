<?php

if (!defined('ABSPATH')) exit;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\AdminAjax\SubmitBase;
use Chwnam\ThreadsToPosts\Modules\AdminAjaxHandler;

return [
    /**
     * We are not going to use 'Content-Type: application/json', so that we can safely disable this feature.
     */
    'checkContentType' => false,

    /**
     * Action tester
     *
     * @uses AdminAjaxHandler::tester()
     */
    [
        'ttp_tester',                       // action
        'ttp/adminAjaxHandler@tester',      // callback
        SubmitBase::ONLY_PRIV,              // logged-in user only
        'ttp_tester',                       // automatic nonce check
        // default priority
    ],
];
