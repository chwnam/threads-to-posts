<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

use Bojaghi\Contract\Support;

abstract class ApiBase implements Support
{
    /**
     * @throws ApiCallException
     */
    public function request(string $url, array|string $args = ''): array
    {
        $defaults = ['method' => 'GET'];
        $args     = wp_parse_args($args, $defaults);

        $args['method'] = strtoupper($args['method']);

        if ('GET' === $args['method'] && !empty($args['body'])) {
            $url = add_query_arg($args['body'], $url);
            unset($args['body']);
        }

        $r       = wp_remote_request($url, $args);
        $code    = wp_remote_retrieve_response_code($r);
        $message = wp_remote_retrieve_response_message($r);
        $body    = wp_remote_retrieve_body($r);

        if (200 !== $code) {
            throw new ApiCallException($message, $code);
        }

        return json_decode($body, true) ?: [];
    }
}
