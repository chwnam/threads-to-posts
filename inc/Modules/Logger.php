<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class Logger implements Module
{
    private MonologLogger $logger;

    public function __construct()
    {
        $this->logger = new MonologLogger('threads-to-posts');
        $handler      = new StreamHandler(static::getLogPath());
        $formatter    = new LineFormatter("[%datetime%] %level_name%: %message%\n", 'Y-m-d H:i:s',);

        $this->logger->pushHandler($handler->setFormatter($formatter));
    }

    public function get(): MonologLogger
    {
        return $this->logger;
    }

    private static function getLogPath(): string
    {
        $d   = wp_upload_dir();
        $dir = "{$d['basedir']}/threads-to-posts";

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
            $fp = @fopen($dir . '/.htaccess', 'w');
            if ($fp) {
                fwrite($fp, "Deny from all\n");
            }
            fclose($fp);
        }

        return "$dir/ttp.log";
    }
}
