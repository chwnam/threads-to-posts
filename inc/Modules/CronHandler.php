<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\TokenSupport;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpLogger;

class CronHandler implements Module
{
    public function __construct()
    {
        add_action('ttp_long_live_token_check', [$this, 'checkLongLiveToken']);
    }

    public function checkLongLiveToken(): void
    {
        $support = ttpGet(TokenSupport::class);
        $logger  = ttpLogger();

        if ($support->checkLongLiveTokenRefreshRequired()) {
            $support->refreshLongLivedToken();
            $logger->info('long-live access token refreshed by cron');
        } else {
            $logger->info('long-live access token is fine - no need to be refreshed');
        }
    }
}
