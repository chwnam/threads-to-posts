<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;

class ActivationDeactivation implements Module
{
    public function __construct()
    {
        register_activation_hook(TTP_MAIN, [$this, 'activate']);
        register_deactivation_hook(TTP_MAIN, [$this, 'deactivate']);
    }

    public function activate(): void
    {
        // Add caps
        $admin = get_role('administrator');
        foreach (self::getCustomPostCaps() as $cap) {
            $admin->add_cap($cap);
        }
    }

    public function deactivate(): void
    {
        // Remove caps
        $admin = get_role('administrator');
        foreach (self::getCustomPostCaps() as $cap) {
            $admin->remove_cap($cap);
        }

        // Remove dynamically set schedule.
        wp_clear_scheduled_hook('ttp_cron_scrap');
    }

    /**
     * Add required ttp_threads CPT caps.
     *
     * @return string[]
     */
    private static function getCustomPostCaps(): array
    {
        return [
            'delete_others_threads_posts',
            'delete_published_threads_posts',
            'delete_threads_post',
            'delete_threads_posts',
            'edit_others_threads_post',
            'edit_published_threads_posts',
            'edit_threads_post',
            'edit_threads_posts',
        ];
    }
}
