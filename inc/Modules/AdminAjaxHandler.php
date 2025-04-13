<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\ApiSupport;
use JetBrains\PhpStorm\NoReturn;

class AdminAjaxHandler implements Module
{
    #[NoReturn]
    public function tester(ApiSupport $support): void
    {
        $type = sanitize_key($_GET['type'] ?? '');

        switch ($type) {
            case 'posts':
                $output = $support->getThreadsPosts();
                // Hide access token
                if (isset($output['paging']['next'])) {
                    $output['paging']['next'] = self::hideAccessToken($output['paging']['next']);
                }
                break;

            case 'single':
                $output = null;
                break;

            case 'conversations':
                $output = null;
                break;

            default:
                $output = null;
                break;
        }

        if ($output) {
            $encoded = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            wp_send_json_success(['output' => $encoded]);
        }
        exit;
    }

    private static function hideAccessToken(string $url): string
    {
        $str = parse_url($url, PHP_URL_QUERY);
        parse_str($str, $query);

        if (isset($query['access_token'])) {
            $query['access_token'] = 'SECURELY-HIDDEN';
        }

        return add_query_arg($query, $url);
    }
}