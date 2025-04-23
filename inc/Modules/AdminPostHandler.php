<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use Chwnam\ThreadsToPosts\Supports\TokenSupport;
use function Chwnam\ThreadsToPosts\ttpCall;
use function Chwnam\ThreadsToPosts\ttpGet;

class AdminPostHandler implements Module
{
    /**
     * @uses TokenSupport::getAuthorization()
     * @uses TokenSupport::redirectionCallback()
     */
    public function accessToken(): void
    {
        $step = sanitize_key($_GET['step'] ?? '');

        if (empty($step) && wp_verify_nonce($_GET['nonce'] ?? '', '_ttp_access_token')) {
            wp_redirect(ttpCall(TokenSupport::class, 'getAuthorization'));
            exit;
        }

        if ('redirect' == $step) {
            ttpCall(TokenSupport::class, 'redirectionCallback');
            // This action is fired by redirection from Threads, not by our site.
            // Relying on referrer is not a good idea.
            wp_redirect(admin_url('tools.php?page=ttp&tab=settings'));
            exit;
        }
    }

    /**
     * @param Options $options
     *
     * @return void
     */
    public function deleteToken(Options $options): void
    {
        $options->ttp_token->delete();
        wp_redirect(wp_get_referer());
    }

    /**
     * @return void
     *
     * @uses TokenSupport::refreshLongLivedToken()
     */
    public function forceRefreshToken(): void
    {
        ttpCall(TokenSupport::class, 'refreshLongLivedToken');
        wp_redirect(wp_get_referer());
    }

    /**
     * @return void
     */
    public function updateScrapMode(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to do this action.');
        }

        $value = sanitize_key($_REQUEST['ttp_scrap_mode'] ?? 'disabled');
        ttpGet(Options::class)->ttp_scrap_mode->update($value);
        // Clear staged timestamp.
        CronHandler::clearStaged();

        // Set the cron schedule.
        switch ($value) {
            default:
            case 'disabled':
                wp_clear_scheduled_hook('ttp_cron_scrap');
                break;

            case 'light':
                wp_clear_scheduled_hook('ttp_cron_scrap');
                wp_schedule_event(
                    timestamp:  time(),
                    recurrence: 'ttp_scrap_schedule',
                    hook:       'ttp_cron_scrap',
                );
                break;

            case 'heavy':
                $runner = ttpGet(TaskRunner::class);
                $queue  = $runner->getQueue();
                $queue->clear();
                $queue->push('heavy-scrap');
                $queue->save();

                wp_clear_scheduled_hook('ttp_cron_scrap');
                wp_schedule_event(
                    timestamp:  time(),
                    recurrence: 'ttp_scrap_schedule',
                    hook:       'ttp_cron_scrap',
                );
                break;
        }

        wp_redirect(wp_get_referer());
    }
}
