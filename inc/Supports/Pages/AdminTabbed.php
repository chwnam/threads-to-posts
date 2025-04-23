<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Support;
use function Chwnam\ThreadsToPosts\ttpGet;
use function Chwnam\ThreadsToPosts\ttpGetMisc;
use function Chwnam\ThreadsToPosts\ttpGetTemplate;

class AdminTabbed implements Support
{
    public function render(): void
    {
        $isActive  = fn($id) => ($_GET['tab'] ?? '') == $id;
        $miscSetup = ttpGetMisc();

        $tabs = array_filter(
            [
                [
                    'id'        => 'scrap',
                    'title'     => 'Scrap',
                    'url'       => add_query_arg('tab', 'scrap'),
                    'is_active' => $isActive('') || $isActive('scrap'),
                    'callback'  => function () { ttpGet(ScrapPage::class)->render(); },
                ],
//                [
//                    'id'        => 'task-manager',
//                    'title'     => 'Task Manager',
//                    'url'       => add_query_arg('tab', 'task-manager'),
//                    'is_active' => $isActive('task-manager'),
//                    'callback'  => function () { ttpGet(TaskManagerPage::class)->render(); },
//                ],
                ...[
                    $miscSetup->enable_tester ? [
                        'id'        => 'tester',
                        'title'     => 'Tester',
                        'url'       => add_query_arg('tab', 'tester'),
                        'is_active' => $isActive('tester'),
                        'callback'  => function () { ttpGet(TesterPage::class)->render(); },
                    ] : []
                ],
                [
                    'id'        => 'settings',
                    'title'     => 'Settings',
                    'url'       => add_query_arg('tab', 'settings'),
                    'is_active' => $isActive('settings'),
                    'callback'  => function () { ttpGet(SettingsPage::class)->render(); },
                ],
            ]
        );

        echo ttpGetTemplate()->template('admin', ['tabs' => $tabs]);
    }
}
