<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\Pages\AdminTabbed;
use function Chwnam\ThreadsToPosts\ttpGet;

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
            'tools.php',
            'Threads to Posts',
            'Threads to Posts',
            'manage_options',
            'ttp',
            [$this, 'outputPage'],
        );
    }

    public function outputPage(): void
    {
        ttpGet(AdminTabbed::class)->render();
    }
}
