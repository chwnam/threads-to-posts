<?php
/**
 * Plugin Name:       Threas to Posts
 * Plugin URI:        https://github.com/chwnam/threads-to-posts
 * Version:           0.10.0
 * Description:       Export your Threads postings to WordPress postings.
 * Author:            chwnam
 * Requires at least: 6.7
 * Requires PHP:      8.0
 */

require __DIR__ . '/vendor/autoload.php';

use Bojaghi\Continy\ContinyException;
use function Chwnam\ThreadsToPosts\ttp;

const TTP_MAIN    = __FILE__;
const TTP_VERSION = '0.10.0';

try {
    ttp();
} catch (ContinyException $e) {
    wp_die($e->getMessage());
}
