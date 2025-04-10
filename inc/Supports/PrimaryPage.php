<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;

class PrimaryPage implements Support
{
    public function render(): void
    {
        echo 'okay';
        /*
         * Authorization
         * Cron status
         * Log
         */
    }
}
