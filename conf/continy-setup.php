<?php

if (!defined('ABSPATH')) exit;

use Bojaghi\AdminAjax\AdminAjax;
use Bojaghi\AdminAjax\AdminPost;
use Bojaghi\Continy\Continy;
use Bojaghi\Cpt\CustomPosts;
use Bojaghi\Cron;
use Bojaghi\Fields;
use Bojaghi\Template;
use Chwnam\ThreadsToPosts\Interfaces\TaskQueue;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;
use Chwnam\ThreadsToPosts\Modules;
use Chwnam\ThreadsToPosts\Modules\CliHandler;
use Chwnam\ThreadsToPosts\Supports;
use function Chwnam\ThreadsToPosts\ttpGetAuth;
use function Chwnam\ThreadsToPosts\ttpGetToken;

return [
    'main_file' => TTP_MAIN,    // 플러그인 메인 파일
    'version'   => TTP_VERSION, // 플러그인의 버전

    /**
     * 훅 선언
     *
     * 키: 훅 이름
     * 값: 콜백 함수에서 허용하는 인자 수, 0 이상의 정수
     */
    'hooks'     => [
        'admin_init' => 0,
        'init'       => 0,
    ],

    /**
     * 바인딩 선언
     *
     * 키: 별명 (alias)
     * 값: 실제 클래스 (FQCN)
     */
    'bindings'  => [
        'bojaghi/adminAjax'    => AdminAjax::class,
        'bojaghi/adminPost'    => AdminPost::class,
        'bojaghi/cpt'          => CustomPosts::class,
        'bojaghi/cron'         => Cron\Cron::class,
        'bojachi/cronSched'    => Cron\CronSchedule::class,
        'bojaghi/template'     => Template\Template::class,
        'ttp/actvDctv'         => Modules\ActivationDeactivation::class,
        'ttp/adminAjaxHandler' => Modules\AdminAjaxHandler::class,
        'ttp/adminEdit'        => Modules\AdminEdit::class,
        'ttp/adminMenu'        => Modules\AdminMenu::class,
        'ttp/adminPostHandler' => Modules\AdminPostHandler::class,
        'ttp/cronHandler'      => Modules\CronHandler::class,
        'ttp/logger'           => Modules\Logger::class,
        'ttp/options'          => Modules\Options::class,
        'ttp/scripts'          => Modules\Scripts::class,
        // interface mapping
        TaskQueue::class       => Supports\OptionTaskQueue::class,
        TaskRunner::class      => Supports\SimpleTaskRunner::class,
    ],

    /**
     * 클래스 의존성 주입 선언
     *
     * 키: 별명, 또는 FQCN
     * 값: 배열, 또는 함수 - 함수는 배열을 리턴해야 함
     */
    'arguments' => [
        // Bojaghi module arguments
        'bojaghi/adminAjax'                   => fn(Continy $continy) => [
            dirname(TTP_MAIN) . '/conf/admin-ajax-setup.php', // configuration
            $continy,                                         // container interface
        ],
        'bojaghi/adminPost'                   => fn(Continy $continy) => [
            dirname(TTP_MAIN) . '/conf/admin-post-setup.php', // configuration
            $continy,                                         // container interface
        ],
        'bojaghi/cpt'                         => dirname(TTP_MAIN) . '/conf/cpt-setup.php',
        'bojaghi/cron'                        => dirname(TTP_MAIN) . '/conf/cron-setup.php',
        'bojachi/cronSched'                   => dirname(TTP_MAIN) . '/conf/cron-sched-setup.php',
        'bojaghi/template'                    => [
            [
                'infix'  => 'tmpl',
                'scopes' => [dirname(TTP_MAIN) . '/inc/templates'],
            ]
        ],

        // TTP module arguments
        'ttp/logger'                          => [
            'logLevel' => defined('WP_DEBUG') && WP_DEBUG ? 'debug' : 'info',
        ],
        'ttp/options'                         => dirname(TTP_MAIN) . '/conf/options-setup.php',

        // Supports arguments
        Supports\Threads\Api::class           => function (): array {
            $token = ttpGetToken();
            return [
                'accessToken' => $token->access_token,
                'userId'      => $token->user_id,
            ];
        },
        Supports\OptionTaskQueue::class       => function (): array {
            $value = ttpGetToken();
            return [
                'userId' => $value->user_id,
            ];
        },
        Supports\Threads\Authorization::class => function (): array {
            $value = ttpGetAuth();
            return [
                'appId'                => $value->app_id,
                'appSecret'            => $value->app_secret,
                'redirectCallbackUrl'  => Supports\TokenSupport::getRedirectionCallbackUrl(),
                'uninstallCallbackUrl' => Supports\TokenSupport::getUninstallCallbackUrl(),
                'deleteCallbackUrl'    => Supports\TokenSupport::getDeleteCallbackUrl(),
            ];
        },
    ],

    /**
     * 모듈 선언
     */
    'modules'   => [
        '_'    => [
            'bojaghi/cron',
            'bojachi/cronSched',
            'ttp/actvDctv',
            function () {
                if ('cli' === php_sapi_name() && (defined('WP_CLI') && WP_CLI)) {
                    \WP_CLI::add_command('ttp', CliHandler::class);
                }
            }
        ],
        'init' => [
            Continy::PR_HIGH    => [
                'ttp/options',
            ],
            Continy::PR_DEFAULT => [
                // Bojaghi
                'bojaghi/adminAjax',
                'bojaghi/adminPost',
                'bojaghi/cpt',
                // TTP
                'ttp/adminEdit',
                'ttp/adminMenu',
                'ttp/cronHandler',
                'ttp/scripts'
            ],
        ],
    ],
];
