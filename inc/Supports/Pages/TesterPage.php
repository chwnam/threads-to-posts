<?php

namespace Chwnam\ThreadsToPosts\Supports\Pages;

use Bojaghi\Contract\Support;
use Bojaghi\Template\Template;
use Chwnam\ThreadsToPosts\Modules\Scripts;
use Chwnam\ThreadsToPosts\Supports\Threads\Api;
use function Chwnam\ThreadsToPosts\ttpGetToken;

class TesterPage implements Support
{
    public function __construct(private Template $template) { }

    public function render(): void
    {
        echo $this->template->template('tester');
        wp_enqueue_script('ttp-tester');
        Scripts::addLivereload();
    }
}
