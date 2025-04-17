<?php
/**
 * Plugin Name:       Threas to Posts
 * Plugin URI:        https://github.com/chwnam/threads-to-posts
 * Version:           0.8.0
 * Description:       Export your Threads postings to WordPress postings.
 * Author:            chwnam
 * Requires at least: 6.7
 * Requires PHP:      8.0
 */

require __DIR__ . '/vendor/autoload.php';

use Bojaghi\Continy\ContinyException;
use Bojaghi\Continy\ContinyNotFoundException;
use function Chwnam\ThreadsToPosts\ttp;

const TTP_MAIN    = __FILE__;
const TTP_VERSION = '0.8.0';

try {
    ttp();
} catch (ContinyException|ContinyNotFoundException $e) {
    wp_die($e->getMessage());
}

add_action('wp_loaded', function () {
// use this for deactivation hook
    $admin = get_role('administrator');
//    foreach (array_keys($admin->capabilities) as $cap) {
//        if (str_contains($cap, 'threads_')) {
//            $admin->remove_cap($cap);
//        }
//    }

// TODO: reuse this for user-role-editor.
//    $admin->add_cap('edit_threads_posts');
//    $admin->add_cap('edit_published_threads_posts');
//    $admin->add_cap('edit_threads_post');
//    $admin->add_cap('edit_others_threads_post');

//    $admin->add_cap('delete_threads_post');
//    $admin->add_cap('delete_threads_posts');
//    $admin->add_cap('delete_published_threads_posts');
//    $admin->add_cap('delete_others_threads_posts');
});
