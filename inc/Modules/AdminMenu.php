<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\PrimaryPage;
use Chwnam\ThreadsToPosts\Supports\SettingsPage;
use function Chwnam\ThreadsToPosts\ttpGet;

class AdminMenu implements Module
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addMenu']);
    }

    public function addMenu(): void
    {
        add_menu_page(
            'Threads to Posts',
            'TTP',
            'manage_options',
            'ttp',
            [$this, 'outputPrimary'],
        );

        add_submenu_page(
            'ttp',
            'Threads to Posts - Settings',
            'Settings',
            'manage_options',
            'ttp-settings',
            [$this, 'outputSettings'],
        );
    }

    public function outputPrimary(): void
    {
        ttpGet(PrimaryPage::class)->render();
    }

    public function outputSettings(): void
    {
        ttpGet(SettingsPage::class)->render();
    }
}
