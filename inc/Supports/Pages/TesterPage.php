<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Bojaghi\Contract\Support;
use Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Modules\Scripts;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
use function Chwnam\ThreadsToPosts\ttpGetAuth;
use function Chwnam\ThreadsToPosts\ttpGetToken;

class TesterPage implements Support
{
    public function __construct(private Template $template) { }

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
            'status' => $status,
        ];

        echo $this->template->template('tester', $context);
        wp_enqueue_script('ttp-tester');
        Scripts::addLivereload();
    }
}
