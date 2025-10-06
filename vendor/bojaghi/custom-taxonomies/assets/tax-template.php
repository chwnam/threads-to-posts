<?php
/**
 * @link         https://developer.wordpress.org/reference/functions/register_taxonomy/
 * @see          get_taxonomy_labels()
 * @see          _update_post_term_count()
 * @see          wp_get_object_terms()
 * @see          post_categories_meta_box()
 * @see          post_tags_meta_box()
 *
 * @noinspection PhpExpressionResultUnusedInspection
 */
#@@@TEMPLATE_BEGIN@@@
[
    // Taxonomy name. Maximum 32 characters.
    '@@@TAXONOMY@@@',

    // Object types. Required.
    [],

    // Arguments.
    [
        'labels'                => [
            'name'                       => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'singular_name'              => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'search_items'               => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'popular_items'              => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'all_items'                  => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'parent_item'                => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'parent_item_colon'          => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'name_field_description'     => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'slug_field_description'     => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'parent_field_description'   => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'desc_field_description'     => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'edit_item'                  => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'view_item'                  => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'update_item'                => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'add_new_item'               => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'new_item_name'              => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'template_name'              => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'separate_items_with_commas' => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'add_or_remove_items'        => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'choose_from_most_used'      => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'not_found'                  => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'no_terms'                   => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'filter_by_item'             => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'items_list_navigation'      => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'items_list'                 => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'most_used'                  => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'back_to_items'              => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_link'                  => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_link_description'      => _x('', '@@@TAXONOMY@@@ label', '@@@TEXTDOMAIN@@@'),
        ],
        'description'           => '',
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'show_in_rest'          => true,
        'rest_base'             => '@@@TAXONOMY@@@',
        'rest_namespace'        => 'wp/v2',
        'rest_controller_class' => WP_REST_Terms_Controller::class,
        'show_tagcloud'         => true,
        'show_in_quick_edit'    => true,
        'show_admin_column'     => false,
        'meta_box_cb'           => null,
        'meta_box_sanitize_cb'  => null,
        'capabilities'          => [
            'manage_terms' => 'manage_categories',
            'edit_terms'   => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts',
        ],
        'rewrite'               => [
            'slug'         => '@@@TAXONOMY@@@',
            'with_front'   => true,
            'hierarchical' => false,
            'ep_mask'      => EP_NONE,
        ],
        'query_var'             => '@@@TAXONOMY@@@',
        'update_count_callback' => null,
        'default_term'          => [
            'name'        => '',
            'slug'        => '',
            'description' => _x('', '@@@TAXONOMY@@@ default term description', '@@@TEXTDOMAIN@@@'),
        ],
        'sort'                  => false,
        'args'                  => [],
    ],
]#@@@TEMPLATE_END@@@
;
