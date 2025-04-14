<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Bojaghi\Contract\Support;
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
                    'id'        => 'index',
                    'title'     => 'Index',
                    'url'       => add_query_arg('tab', 'index'),
                    'is_active' => $isActive('') || $isActive('index'),
                    'callback'  => '',
                ],
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
