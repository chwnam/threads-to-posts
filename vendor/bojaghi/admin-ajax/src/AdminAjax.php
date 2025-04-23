<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\AdminAjax;

class AdminAjax extends SubmitBase
{
    public function getPrivAction($tag): string
    {
        return "wp_ajax_$tag";
    }

    public function getNoPrivAction($tag): string
    {
        return "wp_ajax_nopriv_$tag";
    }
}
