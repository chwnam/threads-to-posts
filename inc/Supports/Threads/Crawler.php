<?php

namespace Chwnam\ThreadsToPosts\Supports\Threads;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Support;
use Exception;

class Crawler implements Support
{
    private string $html = '';

    /**
     * @throws Exception
     */
    public function fetch(string $url): self
    {
        $r = wp_remote_get($url);

        $code    = wp_remote_retrieve_response_code($r);
        $body    = wp_remote_retrieve_body($r);
        $message = wp_remote_retrieve_response_message($r);

        if (200 !== $code) {
            throw new Exception("$message: $body", $code);
        }

        $this->html = $body;

        return $this;
    }

    public function extractMetaTagContent(string $property): string
    {
        $text  = '';
        $regex = sprintf(';<meta property="%s" content="([^"/>]+)" />;', $property);

        if (preg_match($regex, $this->html, $m)) {
            $text = html_entity_decode($m[1]);
        }

        return $text;
    }

    public function extractOgDescription(): string
    {
        return $this->extractMetaTagContent('og:description');
    }
}
