<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use WP_Post;
use WP_Query;
use WP_Screen;
use function Chwnam\ThreadsToPosts\ttpGetTemplate;
use function Chwnam\ThreadsToPosts\ttpPostPermalink;
use function Chwnam\ThreadsToPosts\ttpUnprefix;

/**
 * Customize edit.php for CPT 'ttp_threads'
 */
class AdminEdit implements Module
{
    public function __construct()
    {
        add_action('current_screen', [$this, 'init']);
        add_filter('use_block_editor_for_post_type', [$this, 'disableBlockEditor'], 10, 2);
    }

    public function disableBlockEditor(bool $value, string $postType): bool
    {
        return 'ttp_threads' === $postType ? false : $value;
    }

    public function init(WP_Screen $screen): void
    {
        // something only for the single edit page.
        if ('post' === $screen->base && 'ttp_threads' === $screen->post_type) {
            add_action('add_meta_boxes_ttp_threads', [$this, 'editMetaBoxes']);
            add_action('edit_form_top', [$this, 'outputContent']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueAdminSingle'], 500);
        }

        // hooks only for the list page.
        if ('edit' === $screen->base && 'ttp_threads' === $screen->post_type) {
            add_filter("bulk_actions-$screen->id", [$this, 'editBulkActions']);
            add_filter('page_row_actions', [$this, 'editRowActions'], 10, 2);
            add_filter('quick_edit_enabled_for_post_type', [$this, 'removeQuickEdit'], 10, 2);
            add_filter('the_title', [$this, 'editTitle'], 10, 2);
            add_action('pre_get_posts', [$this, 'editMainQuery']);
        }
    }

    public function editBulkActions(array $actions): array
    {
        unset($actions['edit']);

        return $actions;
    }

    public function editMetaBoxes(): void
    {
        remove_meta_box('submitdiv', 'ttp_threads', 'side');
    }

    /**
     * Edit the 'where' clause of the main query
     *
     * @param WP_Query $query
     *
     * @return void
     */
    public function editMainQuery(WP_Query $query): void
    {
        if (!$query->is_main_query()) {
            return;
        }

        // Fetch only the topmost posts.
        remove_action('pre_get_posts', [$this, 'editMainQuery']);
//        $callback = function ($where) use (&$callback) {
//            remove_filter('posts_where', $callback);
//            $where .= ' AND post_parent=0';
//            return $where;
//        };
//        add_filter('posts_where', $callback, 10, 2);

        // Default order
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }

    public function editRowActions(array $actions, WP_Post $post): array
    {
        if ('ttp_threads' === $post->post_type) {
            // Modify text.
            if (isset($actions['edit']) && preg_match(';(<a.+>).+(</a>);', $actions['edit'], $matches)) {
                $label           = 'View';
                $actions['edit'] = "$matches[1]$label$matches[2]";
            }
        }

        return $actions;
    }

    public function editTitle(string $title, int $postId): string
    {
        if ('ttp_threads' === get_post_type($postId)) {
            $text  = get_post_field('post_content', $postId);
            $text  = wp_trim_words($text, 12);
            $title = $text;
        }

        return $title;
    }

    public function enqueueAdminSingle(): void
    {
        wp_enqueue_style('ttp-admin-single');
    }

    public function outputContent(WP_Post $post): void
    {
        $context = [
            'id'         => ttpUnprefix($post->post_name),
            'owner'      => get_post_meta($post->ID, '_ttp_owner', true),
            'permalink'  => ttpPostPermalink($post),
            'shortcode'  => $post->post_content,
            'text'       => wpautop(wptexturize($post->post_content)),
            'timestamp'  => $post->post_date,
            'username'   => get_post_meta($post->ID, '_ttp_username', true),
            'show_embed' => false,
        ];

        $referer = wp_get_referer();
        $admin   = admin_url('edit.php');
        if (str_starts_with($referer, $admin)) {
            // To keep the query string.
            $context['back_link'] = $referer;
        } else {
            // Visited from nowhere.
            $context['back_link'] = $admin . '?post_type=ttp_threads';
        }

        echo ttpGetTemplate()->template('admin-single', $context);
        wp_enqueue_script('ttp-admin-single');
    }

    public function removeQuickEdit(bool $enabled, string $post_type): bool
    {
        return 'ttp_threads' === $post_type ? false : $enabled;
    }
}
