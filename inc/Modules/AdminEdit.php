<?php

namespace Chwnam\ThreadsToPosts\Modules;

use Bojaghi\Contract\Module;
use WP_Post;
use WP_Query;
use WP_Screen;

/**
 * Customize edit.php for CPT 'ttp_threads'
 */
class AdminEdit implements Module
{
    public function __construct()
    {
        add_action('current_screen', [$this, 'init']);
    }

    public function init(WP_Screen $screen): void
    {
        // hooks only for the list page.
        if ('edit' === $screen->base && 'ttp_threads' === $screen->post_type) {
            add_action('add_meta_boxes_ttp_threads', [$this, 'editMetaBoxes']);
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
     * Edit 'where' clause of the main query
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
        $callback = function ($where) use (&$callback) {
            remove_filter('posts_where', $callback);
            $where .= ' AND post_parent=0';
            return $where;
        };
        add_filter('posts_where', $callback, 10, 2);

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

    public function removeQuickEdit(bool $enabled, string $post_type): bool
    {
        return 'ttp_threads' === $post_type ? false : $enabled;
    }
}
