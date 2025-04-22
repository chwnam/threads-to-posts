<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Bojaghi\Contract\Support;
use Bojaghi\FieldsRender\AdminCompound as AC;
use Bojaghi\FieldsRender\Render as R;
use Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Modules\CronHandler;
use Chwnam\ThreadsToPosts\Modules\Options;
use function Chwnam\ThreadsToPosts\ttpGetToken;
use function Chwnam\ThreadsToPosts\ttpGetLogger;
use function Chwnam\ThreadsToPosts\ttpGetTemplate;

class SettingsPage implements Support
{
    public function __construct(
        private Options  $options,
        private Template $template,
    )
    {
    }

    public function render(): void
    {
        $this->prepareSettings();

        echo $this->template->template(
            'settings',
            [
                'title'        => 'Settings',
                'option_group' => 'ttp-options',
                'page'         => 'ttp-settings',
            ],
        );

        wp_enqueue_script('ttp-settings');
    }

    private function prepareSettings(): void
    {
        $this->prepareSectionAuthorization();
        $this->prepareSectionToken();
        $this->prepareSectionCron();
        $this->prepareSectionMisc();
    }

    private function prepareSectionAuthorization(): void
    {
        $auth       = $this->options->ttp_auth;
        $auth_value = $auth->get();
        $appId      = $auth_value['app_id'] ?? '';
        $appSecret  = $auth_value['app_secret'] ?? '';

        add_settings_section(
            id:       'ttp-auth',
            title:    'Authorization',
            callback: '__return_empty_string',
            page:     'ttp-settings',
        );

        // APP ID
        add_settings_field(
            id:      'ttp-auth-app_id',
            title:   'App ID',
            callback: function (array $args): void {
                $name  = $args['name'] ?? '';
                $value = $args['value'] ?? '';
                echo R::input(
                    [
                        'id'           => $args['label_for'] ?? '',
                        'autocomplete' => 'off',
                        'class'        => 'text',
                        'name'         => "{$name}[app_id]",
                        'type'         => 'text',
                        'value'        => $value,
                    ],
                );
                echo AC::description('Threads App ID');
            },
            page:    'ttp-settings',
            section: 'ttp-auth',
            args:    [
                         'label_for' => 'ttp-app_id',
                         'name'      => $auth->getKey(),
                         'value'     => $appId,
                     ],
        );

        // APP Secret
        add_settings_field(
            id:      'ttp-auth-app_secret',
            title:   'App Secret',
            callback: function (array $args): void {
                $name  = $args['name'] ?? '';
                $value = $args['value'] ?? '';
                echo R::input(
                    [
                        'id'            => $args['label_for'] ?? '',
                        'class'         => 'text',
                        'name'          => "{$name}[app_secret]",
                        'type'          => 'password',
                        'value'         => $value,
                        'data-bwignore' => 'true',
                    ],
                );
                echo AC::description('Threads App secret');
            },
            page:    'ttp-settings',
            section: 'ttp-auth',
            args:    [
                         'label_for' => 'ttp-app_secret',
                         'name'      => $auth->getKey(),
                         'value'     => $appSecret,
                     ],
        );

        // Callback URLs
        add_settings_field(
            id:      'ttp-auth-callback_urls',
            title:   'Callback URLs',
            callback: function (): void { echo ttpGetTemplate()->template('callback-url-guide'); },
            page:    'ttp-settings',
            section: 'ttp-auth',
        );
    }

    private function prepareSectionToken(): void
    {
        $auth       = $this->options->ttp_auth;
        $auth_value = $auth->get();
        $appId      = $auth_value['app_id'] ?? '';
        $appSecret  = $auth_value['app_secret'] ?? '';

        $token       = $this->options->ttp_token;
        $token_value = $token->get();
        $accessToken = $token_value['access_token'] ?? '';
        $userId      = $token_value['user_id'] ?? '';
        $timestamp   = $token_value['timestamp'] ?? 0;
        $expiresIn   = $token_value['expires_in'] ?? 0;

        add_settings_section(
            id:       'ttp-token',
            title:    'Access Token',
            callback: '__return_empty_string',
            page:     'ttp-settings',
        );

        add_settings_field(
            id:      'ttp-token_status',
            title:   'Token Status',
            callback: function (array $args): void { echo ttpGetTemplate()->template('token-status', $args); },
            page:    'ttp-settings',
            section: 'ttp-token',
            args:    [
                         'is_available'  => $appId && $appSecret,
                         'is_authorized' => (bool)$accessToken,
                         'user_id'       => $userId,
                         'timestamp'     => $timestamp,
                         'expires_in'    => $expiresIn,
                     ],
        );
    }

    private function prepareSectionCron(): void
    {
        $args      = ['cron_details' => []];
        $schedules = wp_get_schedules();

        /**
         * Targets
         *
         * @see CronHandler
         */
        $event = wp_get_scheduled_event('ttp_long_live_token_check');
        if ($event) {
            $args['cron_details'][] = [
                'title'     => 'Long-live access token check',
                'timestamp' => $event->timestamp,
                'schedule'  => $schedules[$event->schedule]['display'] ?? '',
            ];
        }

        $event = wp_get_scheduled_event('ttp_cron_scrap');
        if ($event) {
            $args['cron_details'][] = [
                'title'     => 'Cron scrap',
                'timestamp' => $event->timestamp,
                'schedule'  => $schedules[$event->schedule]['display'] ?? '',
            ];
        }

        add_settings_section(
            id:       'ttp-cron',
            title:    'WP Cron',
            callback: '__return_empty_string',
            page:     'ttp-settings',
        );

        add_settings_field(
            id:      'ttp-cron_status',
            title:   'Cron Status',
            callback: function (array $args): void { echo ttpGetTemplate()->template('cron-status', $args); },
            page:    'ttp-settings',
            section: 'ttp-cron',
            args:    $args,
        );
    }

    private function prepareSectionMisc(): void
    {
        $option = $this->options->ttp_misc;
        $name   = $option->getKey();
        $value  = $option->get();

        add_settings_section(
            id:       'ttp-misc',
            title:    'Misc.',
            callback: '__return_empty_string',
            page:     'ttp-settings',
        );

        add_settings_field(
            id:      'ttp-enable_tester',
            title:   'Enable Tester',
            callback: function (array $args): void {
                echo R::checkbox(
                    label:   'Enable tester tab for debugging and testring',
                    checked: $args['value']['enable_tester'],
                    attrs:   [
                                 'id'   => $args['label_for'],
                                 'name' => $args['name'] . '[enable_tester]',
                             ]
                );
            },
            page:    'ttp-settings',
            section: 'ttp-misc',
            args:    [
                         'label_for' => 'ttp-enable_tester',
                         'name'      => $name,
                         'value'     => $value,
                     ],
        );
    }
}
