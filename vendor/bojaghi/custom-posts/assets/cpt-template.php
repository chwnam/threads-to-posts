<?php
/**
 * @link         https://developer.wordpress.org/reference/functions/register_post_type/
 * @see          \WP_Post_Type::set_props()
 * @see          get_post_type_capabilities()
 * @see          get_post_type_labels()
 *
 * @noinspection PhpExpressionResultUnusedInspection
 */
#@@@TEMPLATE_BEGIN@@@
[
    // Post type name. Maximum 20 characters.
    '@@@POST_TYPE@@@',

    // Post type arguments.
    [
        'label'                           => __('', '@@@TEXTDOMAIN@@@'),
        'labels'                          => [
            'name'                     => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'singular_name'            => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'add_new'                  => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'add_new_item'             => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'edit_item'                => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'new_item'                 => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'view_item'                => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'view_items'               => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'search_items'             => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'not_found'                => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'not_found_in_trash'       => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'parent_item_colon'        => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'all_items'                => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'archives'                 => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'attributes'               => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'insert_into_item'         => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'uploaded_to_this_item'    => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'featured_image'           => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'set_featured_image'       => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'remove_featured_image'    => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'use_featured_image'       => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'menu_name'                => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'filter_items_list'        => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'filter_by_date'           => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'items_list_navigation'    => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'items_list'               => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_published'           => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_published_privately' => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_reverted_to_draft'   => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_trashed'             => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_scheduled'           => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_updated'             => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_link'                => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
            'item_link_description'    => _x('', '@@@POST_TYPE@@@ label', '@@@TEXTDOMAIN@@@'),
        ],
        'description'                     => _x('', 'Description of @@@POST_TYPE@@@', '@@@TEXTDOMAIN@@@'),
        'public'                          => true,
        'hierarchical'                    => false,
        'exclude_from_search'             => false,
        'publicly_queryable'              => true,
        'show_ui'                         => true,
        'show_in_menu'                    => true,
        'show_in_nav_menus'               => true,
        'show_in_admin_bar'               => true,
        'show_in_rest'                    => true,
        'rest_base'                       => '@@@POST_TYPE@@@',
        'rest_namespace'                  => 'wp/v2',
        'rest_controller_class'           => \WP_REST_Posts_Controller::class,
        'autosave_rest_controller_class'  => \WP_REST_Autosaves_Controller::class,
        'revisions_rest_controller_class' => \WP_Rest_Revisions_Controller::class,
        'late_route_registration'         => false,
        'menu_position'                   => null,
        'menu_icon'                       => null,
        'capability_type'                 => 'post',
        'capabilities'                    => [],
        'map_meta_cap'                    => false,
        'supports'                        => ['title', 'editor'],
        'register_meta_box_cb'            => null,
        'taxonomies'                      => [],
        'has_archive'                     => false,
        'rewrite'                         => [
            'slug'       => '',
            'with_front' => false,
            'feeds'      => false,
            'pages'      => true,
            'ep_mask'    => EP_PERMALINK,
        ],
        'query_var'                       => true,
        'can_export'                      => true,
        'delete_with_user'                => null,
        'template'                        => [],
        'template_lock'                   => false,
    ]
]#@@@TEMPLATE_END@@@
;
