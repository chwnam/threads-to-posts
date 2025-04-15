<?php

// No direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    // Begin: ttp_threads
    [
        // post_type 
        'ttp_threads',
        // arguments
        [
            'label'                           => __('Threads', 'ttp'),
            /*
            'labels'                          => [
                'name'                     => _x('', 'ttp_threads label', 'ttp'),
                'singular_name'            => _x('', 'ttp_threads label', 'ttp'),
                'add_new'                  => _x('', 'ttp_threads label', 'ttp'),
                'add_new_item'             => _x('', 'ttp_threads label', 'ttp'),
                'edit_item'                => _x('', 'ttp_threads label', 'ttp'),
                'new_item'                 => _x('', 'ttp_threads label', 'ttp'),
                'view_item'                => _x('', 'ttp_threads label', 'ttp'),
                'view_items'               => _x('', 'ttp_threads label', 'ttp'),
                'search_items'             => _x('', 'ttp_threads label', 'ttp'),
                'not_found'                => _x('', 'ttp_threads label', 'ttp'),
                'not_found_in_trash'       => _x('', 'ttp_threads label', 'ttp'),
                'parent_item_colon'        => _x('', 'ttp_threads label', 'ttp'),
                'all_items'                => _x('', 'ttp_threads label', 'ttp'),
                'archives'                 => _x('', 'ttp_threads label', 'ttp'),
                'attributes'               => _x('', 'ttp_threads label', 'ttp'),
                'insert_into_item'         => _x('', 'ttp_threads label', 'ttp'),
                'uploaded_to_this_item'    => _x('', 'ttp_threads label', 'ttp'),
                'featured_image'           => _x('', 'ttp_threads label', 'ttp'),
                'set_featured_image'       => _x('', 'ttp_threads label', 'ttp'),
                'remove_featured_image'    => _x('', 'ttp_threads label', 'ttp'),
                'use_featured_image'       => _x('', 'ttp_threads label', 'ttp'),
                'menu_name'                => _x('', 'ttp_threads label', 'ttp'),
                'filter_items_list'        => _x('', 'ttp_threads label', 'ttp'),
                'filter_by_date'           => _x('', 'ttp_threads label', 'ttp'),
                'items_list_navigation'    => _x('', 'ttp_threads label', 'ttp'),
                'items_list'               => _x('', 'ttp_threads label', 'ttp'),
                'item_published'           => _x('', 'ttp_threads label', 'ttp'),
                'item_published_privately' => _x('', 'ttp_threads label', 'ttp'),
                'item_reverted_to_draft'   => _x('', 'ttp_threads label', 'ttp'),
                'item_trashed'             => _x('', 'ttp_threads label', 'ttp'),
                'item_scheduled'           => _x('', 'ttp_threads label', 'ttp'),
                'item_updated'             => _x('', 'ttp_threads label', 'ttp'),
                'item_link'                => _x('', 'ttp_threads label', 'ttp'),
                'item_link_description'    => _x('', 'ttp_threads label', 'ttp'),
            ],
            */
            'description'                     => _x('Scrapped threads media', 'Description of ttp_threads', 'ttp'),
            'public'                          => false,
            'hierarchical'                    => true,
            'exclude_from_search'             => true,
            'publicly_queryable'              => false,
            'show_ui'                         => true,
            'show_in_menu'                    => true,
            'show_in_nav_menus'               => false,
            'show_in_admin_bar'               => false,
            'show_in_rest'                    => false,
            'rest_base'                       => 'ttp_threads',
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
            'supports'                        => false,
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
            'query_var'                       => false,
            'can_export'                      => false,
            'delete_with_user'                => null,
            'template'                        => [],
            'template_lock'                   => false,
        ],
    ],
    // End: ttp_threads
];
