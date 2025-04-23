<?php

use TypistTech\Imposter\ImposterFactory;

if ('cli' !== php_sapi_name()) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

$imposter = ImposterFactory::forProject(__DIR__);
$imposter->run();
