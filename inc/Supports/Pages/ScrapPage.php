<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Modules\CronHandler;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpGetAuth;
use function Chwnam\ThreadsToPosts\ttpGetScrapMode;
use function Chwnam\ThreadsToPosts\ttpGetToken;

class ScrapPage implements Support
{
    public function __construct(
        private Template $template,
    )
    {
    }

    public function render(): void
    {
        $status = 'okay';

        $auth = ttpGetAuth();
        if (!$auth->app_id || !$auth->app_secret) {
            $status = 'error-auth';
        }

        $token = ttpGetToken();
        if (!$token->access_token) {
            $status = 'error-token';
        }

        $context = [
            'scrap_mode'   => ttpGetScrapMode(),
            'settings_url' => admin_url('admin.php?page=ttp&tab=settings'),
            'status'       => $status,
        ];

//        ttpGet(CronHandler::class)->cronScrap();

        echo $this->template->template('scrap', $context);
        wp_enqueue_script('ttp-scrap');
    }
}
