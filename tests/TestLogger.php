<?php

namespace Chwnam\ThreadsToPosts\Tests;

use Chwnam\ThreadsToPosts\Modules\Logger;
use WP_UnitTestCase;

class TestLogger extends WP_UnitTestCase
{
    public function testGetLogPath(): void
    {
        $logpath = Logger::getLogPath();
        $upload  = wp_upload_dir();
        $basedir = $upload['basedir'];

        $this->assertEquals($basedir . '/threads-to-posts/threads-to-posts.log', $logpath);
    }
}
