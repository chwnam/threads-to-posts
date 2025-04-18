<?php

namespace Chwnam\ThreadsToPosts;

use Bojaghi\Continy\Continy;
use Bojaghi\Continy\ContinyException;
use Bojaghi\Continy\ContinyFactory;
use Bojaghi\Continy\ContinyNotFoundException;
use Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Modules\Logger as LoggerModule;
use Chwnam\ThreadsToPosts\Modules\Options;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
use Chwnam\ThreadsToPosts\Supports\TokenSupport;
use Monolog\Logger;
use WP_Post;

/**
 * Wrapper function
 *
 * @return Continy
 * @throws ContinyException|ContinyNotFoundException
 */
function ttp(): Continy
{
    static $continy = null;

    if (is_null($continy)) {
        $continy = ContinyFactory::create(dirname(__DIR__) . '/conf/continy-setup.php');
    }

    return $continy;
}

/**
 * @template T
 * @param class-string<T> $id
 * @param string          $method
 * @param array|false     $args
 *
 * @return mixed
 */
function ttpCall(string $id, string $method, array|false $args = false): mixed
{
    try {
        $container = ttp();
        $instance  = $container->get($id);
        return $container->call([$instance, $method], $args);
    } catch (ContinyException|ContinyNotFoundException $e) {
        wp_die($e->getMessage());
    }
}

/**
 * @template T
 * @param class-string<T> $id
 * @param bool            $constructorCall
 *
 * @return T|object|null
 */
function ttpGet(string $id, bool $constructorCall = false)
{
    try {
        $instance = ttp()->get($id, $constructorCall);
    } catch (ContinyException|ContinyNotFoundException $e) {
        $instance = null;
    }

    return $instance;
}

function ttpGetApi(): Api
{
    $tokenSupport = ttpGet(TokenSupport::class);

    if ($tokenSupport->checkLongLiveTokenRefreshRequired()) {
        $tokenSupport->refreshLongLivedToken();
    }

    return ttpGet(Api::class, true);
}

/**
 * Helper for retrieving authrization information
 *
 * @return object{ app_id: string, app_secret: string }
 */
function ttpGetAuth(): object
{
    return (object)ttpGet(Options::class)->ttp_auth->get();
}

function ttpGetLogger(): Logger
{
    return ttpGet(LoggerModule::class)->get();
}

/**
 * Heper for retrieving misc option
 *
 * @return object{
 *     enable_tester: boolean,
 * }
 */
function ttpGetMisc(): object
{
    return (object)ttpGet(Options::class)->ttp_misc->get();
}

function ttpGetTemplate(): Template
{
    return ttpGet(Template::class);
}

/**
 * Helper for retrieving token information
 *
 * @return object{
 *   access_token: string,
 *   token_type: string,
 *   expires_in: integer,
 *   timestamp: integer,
 *   user_id: integer|string,
 * }
 */
function ttpGetToken(): object
{
    return (object)ttpGet(Options::class)->ttp_token->get();
}

function ttpPrefix(string $string): string
{
    return "ttp-$string";
}

function ttpUnprefix(string $string): string
{
    return substr($string, 4);
}

function ttpPostPermalink(WP_Post $post): string
{
    if ('ttp_threads' !== $post->post_type) {
        return '';
    }

    $userName  = get_post_meta($post->ID, '_ttp_user_name', true);
    $shortcode = $post->post_title;

    return "https://www.threads.net/@$userName/post/$shortcode";
}
