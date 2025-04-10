<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Bojaghi\FieldsRender\AdminCompound as AC;
use Bojaghi\FieldsRender\Render as R;
use Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Modules\Options;

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
                'option_group' => 'ttp_options',
                'page'         => 'ttp-settings',
            ],
        );

        wp_enqueue_script('ttp-settings');
    }

    private function prepareSettings(): void
    {
        $auth       = $this->options->ttp_auth;
        $auth_value = $auth->get();
        $app_id     = $auth_value['app_id'] ?? '';
        $app_secret = $auth_value['app_secret'] ?? '';

        $token       = $this->options->ttp_token;
        $token_value = $token->get();
        $accessToken = $token_value['access_token'] ?? '';
        $userId      = $token_value['user_id'] ?? '';
        $timestamp   = $token_value['timestamp'] ?? 0;
        $expiresIn   = $token_value['expires_in'] ?? 0;

        // Authorization
        {
            add_settings_section(
                id:       'ttp_auth',
                title:    'Authorization',
                callback: '__return_empty_string',
                page:     'ttp-settings',
            );

            // APP ID
            add_settings_field(
                id:      'ttp_auth-app_id',
                title:   'App ID',
                callback: function (array $args): void {
                    $id    = $args['label_for'] ?? '';
                    $name  = $args['name'] ?? '';
                    $value = $args['value'] ?? '';

                    echo R::input(
                        [
                            'id'           => $id,
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
                section: 'ttp_auth',
                args:    [
                             'label_for' => 'ttp-app_id',
                             'name'      => $auth->getKey(),
                             'value'     => $app_id,
                         ],
            );

            // APP Secret
            add_settings_field(
                id:      'ttp_auth-app_secret',
                title:   'App Secret',
                callback: function (array $args): void {
                    $id    = $args['label_for'] ?? '';
                    $name  = $args['name'] ?? '';
                    $value = $args['value'] ?? '';

                    echo R::input(
                        [
                            'id'    => $id,
                            'class' => 'text',
                            'name'  => "{$name}[app_secret]",
                            'type'  => 'password',
                            'value' => $value,
                        ],
                    );
                    echo AC::description('Threads App secret');
                },
                page:    'ttp-settings',
                section: 'ttp_auth',
                args:    [
                             'label_for' => 'ttp-app_secret',
                             'name'      => $auth->getKey(),
                             'value'     => $app_secret,
                         ],
            );

            // Callback URLs
            add_settings_field(
                id:      'ttp_auth-callback_urls',
                title:   'Callback URLs',
                callback: function (): void {
                    echo R::open('p');
                    echo R::label(
                        'Redirect Callback URL',
                        ['id' => 'ttp-redirect_callback_url']
                    );
                    echo R::input(
                        [
                            'id'       => 'ttp-redirect_callback_url',
                            'class'    => 'text large-text',
                            'type'     => 'text',
                            'readonly' => true,
                            'value'    => TokenSupport::getRedirectionCallbackUrl(),
                        ]
                    );
                    echo R::close();

                    echo R::open('p');
                    echo R::label(
                        'Uninstall Callback URL',
                        ['id' => 'ttp-uninstall_callback_url']
                    );
                    echo R::input(
                        [
                            'id'       => 'ttp-uninstall_callback_url',
                            'class'    => 'text large-text',
                            'type'     => 'text',
                            'readonly' => true,
                            'value'    => TokenSupport::getUninstallCallbackUrl(),
                        ],
                    );
                    echo R::close();

                    echo R::open('p');
                    echo R::label(
                        'Delete Callback URL',
                        ['id' => 'ttp-delete_callback_url']
                    );
                    echo R::input(
                        [
                            'id'       => 'ttp-delete_callback_url',
                            'class'    => 'text large-text',
                            'type'     => 'text',
                            'readonly' => true,
                            'value'    => TokenSupport::getDeleteCallbackUrl(),
                        ],
                    );
                    echo R::close();
                },
                page:    'ttp-settings',
                section: 'ttp_auth',
            );
        }

        {
            add_settings_section(
                id:       'ttp_token',
                title:    'Token',
                callback: '__return_empty_string',
                page:     'ttp-settings',
            );

            add_settings_field(
                id:      'ttp_token_status',
                title:   'Token Status',
                callback: function (array $args): void {
                    $id           = $args['label_for'] ?? '';
                    $isAvailable  = $args['is_available'] ?? false;
                    $isAuthorized = $args['is_authorized'] ?? false;
                    $userId       = $args['user_id'] ?? '';
                    $timestamp    = $args['timestamp'] ?? 0;
                    $expiresIn    = $args['expires_in'] ?? 0;

                    if ($isAvailable) {
                        if (!$isAuthorized) {
                            echo R::open(
                                'a',
                                [
                                    'id'    => $id,
                                    'class' => 'button button-secondary',
                                    'href'  => add_query_arg(
                                        [
                                            'action' => 'ttp_access_token',
                                            'nonce'  => wp_create_nonce('_ttp_access_token'),
                                        ],
                                        admin_url('admin-post.php'),
                                    ),
                                    'value' => 'Authorize',
                                ],
                            );
                            echo 'Authorize';
                            echo R::close();
                        } else {
                            echo 'User ID: ' . $userId . '<br>';
                            echo 'Timestamp: ' . wp_date('Y-m-d H:i:s', $timestamp) . '<br>';
                            echo 'Expiration: ' . wp_date('Y-m-d H:i:s', $timestamp + $expiresIn) . '<br>';
                            echo R::open(
                                'a',
                                [
                                    'id'    => 'ttp-force_refresh_token',
                                    'class' => 'button button-secondary',
                                    'href'  => add_query_arg(
                                        [
                                            'action' => 'ttp_force_refresh_token',
                                            'nonce'  => wp_create_nonce('_ttp_force_refresh_token'),
                                        ],
                                        admin_url('admin-post.php'),
                                    ),
                                    'type'  => 'button',
                                ],
                            );
                            echo 'Force Refresh';
                            echo R::close();
                        }
                    } else {
                        echo AC::description('Please setup Threads App ID and App Secret.');
                    }
                },
                page:    'ttp-settings',
                section: 'ttp_token',
                args:    [
                             'label_for'     => 'ttp-request_token',
                             'is_available'  => $app_id && $app_secret,
                             'is_authorized' => (bool)$accessToken,
                             'user_id'       => $userId,
                             'timestamp'     => $timestamp,
                             'expires_in'    => $expiresIn,
                         ],
            );
        }
    }
}
