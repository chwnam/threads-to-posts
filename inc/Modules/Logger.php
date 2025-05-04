<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Formatter\LineFormatter;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Handler\RotatingFileHandler;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Handler\StreamHandler;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Level;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Logger as MonologLogger;
use function Chwnam\ThreadsToPosts\ttpGetUploadsDir;

class Logger implements Module
{
    private MonologLogger $logger;

    public function __construct(string|Level $logLevel = Level::Info)
    {
        $logLevel = MonologLogger::toMonologLevel($logLevel);

        $this->logger = new MonologLogger('threads-to-posts');
        $handler      = new RotatingFileHandler(static::getLogPath(), 7, $logLevel);
        $formatter    = new LineFormatter("[%datetime%] %level_name%: %message% %context% %extra%\n", 'Y-m-d H:i:s',);

        $this->logger->pushHandler($handler->setFormatter($formatter));

        // Add a simple stdout handler when WP_CLI is running.
        if (defined('WP_CLI') && WP_CLI) {
            $handler   = new StreamHandler('php://stdout', $logLevel);
            $formatter = new LineFormatter("[%level_name%] %message%\n");
            $this->logger->pushHandler($handler->setFormatter($formatter));
        }
    }

    public function get(): MonologLogger
    {
        return $this->logger;
    }

    public static function getLogPath(): string
    {
        return ttpGetUploadsDir('threads-to-posts') . '/threads-to-posts.log';
    }
}
