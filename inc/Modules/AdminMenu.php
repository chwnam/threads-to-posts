<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\Pages\AdminTabbed;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpGetLogger;

class AdminMenu implements Module
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'scripts']);
        add_action('admin_menu', [$this, 'addMenu']);
    }

    /**
     * Hook callback to output stylesheets inside head tag
     *
     * @param string $hook
     * @return void
     */
    public function scripts(string $hook): void
    {
        if ('tools_page_ttp' !== $hook) {
            return;
        }

        switch ($_GET['tab'] ?? '') {
            case 'scrap':
                wp_enqueue_style('ttp-scrap');
                break;

            case 'tester':
                wp_enqueue_style('ttp-tester');
                break;

            case 'settings':
                wp_enqueue_style('ttp-settings');
                break;
        }
    }

    public function addMenu(): void
    {
        add_submenu_page(
            'edit.php?post_type=ttp_threads',
            'Threads to Posts Settings',
            'Settings',
            'manage_options',
            'ttp',
            [$this, 'outputPage'],
        );
        $logger = ttpGetLogger();
        $logger->debug('admin menu added');
    }

    public function outputPage(): void
    {
        ttpGet(AdminTabbed::class)->render();
    }
}
