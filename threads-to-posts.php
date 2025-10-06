<?php
/**
 * Plugin Name: Threas to Posts
 * Plugin URI: https://github.com/chwnam/threads-to-posts
 * Version: 1.0.0-beta.7
 * Description: Export your Threads postings to WordPress postings.
 * Author: changwoo
 * Author URI: https://blog.changwoo.pe.kr
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * License: GPLv2-or-later
 */

use function Chwnam\ThreadsToPosts\ttp;

const TTP_MAIN    = __FILE__;
const TTP_VERSION = '1.0.0-beta.7';

require __DIR__ . '/vendor/autoload.php';

ttp();
