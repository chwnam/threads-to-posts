<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Supports\Threads\ApiCallException;
use Chwnam\ThreadsToPosts\Supports\Threads\ConversationsFields;
use Chwnam\ThreadsToPosts\Supports\Threads\Fields;
use Chwnam\ThreadsToPosts\Supports\Threads\PostFields;
use JetBrains\PhpStorm\NoReturn;
use function Chwnam\ThreadsToPosts\ttpGetApi;

class AdminAjaxHandler implements Module
{
    #[NoReturn]
    public function tester(): void
    {
        $type = sanitize_key($_GET['type'] ?? '');
        $api  = ttpGetApi();

        try {
            switch ($type) {
                case 'posts':
                    $output = $api->getUserThreads(
                        ['fields' => PostFields::getFields(Fields::ID, Fields::TEXT)]
                    );
                    // Hide access token
                    if (isset($output['paging']['next'])) {
                        $output['paging']['next'] = self::hideAccessToken($output['paging']['next']);
                    }
                    break;

                case 'single':
                    $id     = sanitize_text_field($_GET['id'] ?? '');
                    $output = $api->getUserSingleThread(
                        $id,
                        ['fields' => PostFields::getFields(Fields::ALL)]
                    );
                    break;

                case 'conversations':
                    $id     = sanitize_text_field($_GET['id'] ?? '');
                    $output = $api->getMediaConversation(
                        $id,
                        ['fields' => ConversationsFields::getFields(Fields::ALL)]
                    );
                    break;

                default:
                    $output = null;
                    break;
            }
        } catch (ApiCallException $e) {
            wp_die($e->getMessage());
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