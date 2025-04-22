<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use Chwnam\ThreadsToPosts\Supports\TokenSupport;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpGetLogger;
use function Chwnam\ThreadsToPosts\ttpGetMisc;
use function Chwnam\ThreadsToPosts\ttpGetScrapMode;
use function Chwnam\ThreadsToPosts\ttpGetUploadsDir;

class CronHandler implements Module
{
    public function __construct()
    {
        add_action('ttp_long_live_token_check', [$this, 'checkLongLiveToken']);
        add_action('ttp_cron_scrap', [$this, 'cronScrap']);
    }

    public function checkLongLiveToken(): void
    {
        $support = ttpGet(TokenSupport::class);
        $logger  = ttpGetLogger();

        if ($support->checkLongLiveTokenRefreshRequired()) {
            $support->refreshLongLivedToken();
            $logger->info('long-live access token refreshed by cron');
        } else {
            $logger->info('long-live access token is fine - no need to be refreshed');
        }
    }

    public function cronScrap(): void
    {
        $logger = ttpGetLogger();

        if (($staged = self::isStaged())) {
            $time = human_time_diff($staged);
            $logger->info("Scheduled scrap has already started $time");
            return;
        }
        self::setStaged();

        $runner    = ttpGet(TaskRunner::class);
        $scrapMode = ttpGetScrapMode();

        $logger->info("Scheduled scrap has started as $scrapMode mode.");

        if ('light' === $scrapMode) {
            // Clear the queue.
            $queue = $runner->getQueue();
            $queue->clear();
            $queue->push('light-scrap');
            $queue->save();
            /**
             * For light-scan,
             * - 1 meta task
             * - 1 list fetch
             * - 25 single fetches
             * - 25 conversations fetches
             */
            $runner->run(['max_task' => 52]);
        } elseif ('heavy' === $scrapMode) {
            // Heavy does not clear the queue.
            $runner->run(['max_task' => 55]);
        }

        $logger->info("Scheduled scrap has finished.");

        self::clearStaged();
    }

    public static function setStaged(): void
    {
        set_site_transient('_ttp_scrap_staged', time());
    }

    public static function isStaged(): int|false
    {
        return get_site_transient('_ttp_scrap_staged');
    }

    public static function clearStaged(): void
    {
        delete_site_transient('_ttp_scrap_staged');
    }
}
