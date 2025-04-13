<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Bojaghi\Contract\Support;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpTemplate;

class AdminTabbed implements Support
{
    public function render(): void
    {
        $isActive = fn($id) => ($_GET['tab'] ?? '') == $id;

        $tabs = [
            [
                'id'        => 'index',
                'title'     => 'Index',
                'url'       => add_query_arg('tab', 'index'),
                'is_active' => $isActive('') || $isActive('index'),
                'callback'  => '',
            ],
            [
                'id'        => 'tester',
                'title'     => 'Tester',
                'url'       => add_query_arg('tab', 'tester'),
                'is_active' => $isActive('tester'),
                'callback'  => function () { ttpGet(TesterPage::class)->render(); },
            ],
            [
                'id'        => 'settings',
                'title'     => 'Settings',
                'url'       => add_query_arg('tab', 'settings'),
                'is_active' => $isActive('settings'),
                'callback'  => function () { ttpGet(SettingsPage::class)->render(); },
            ],
        ];

        echo ttpTemplate()->template('admin', ['tabs' => $tabs]);
    }
}
