<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\AdminAjax;

class AdminPost extends SubmitBase
{
    public function getPrivAction($tag): string
    {
        return "admin_post_$tag";
    }

    public function getNoPrivAction($tag): string
    {
        return "admin_post_nopriv_$tag";
    }
}
