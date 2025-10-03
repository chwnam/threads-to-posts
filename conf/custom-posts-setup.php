<?php

// No direct access
if (!defined('ABSPATH')) {
    exit;
}

// base64 encoded svg.
$icon = '';
$svg  = file_get_contents(dirname(__DIR__) . '/assets/threads.txt');
if ($svg) {
    $icon = "data:image/svg+xml;base64,$svg";
}

return [
    // Begin: ttp_threads
    [
        // post_type 
        'ttp_threads',
        // arguments
        [
            'label'                           => __('Threads', 'ttp'),
            'labels'                          => [
                //'name'                     => _x('', 'ttp_threads label', 'ttp'),
                //'singular_name'            => _x('', 'ttp_threads label', 'ttp'),
                //'add_new'                  => _x('', 'ttp_threads label', 'ttp'),
                //'add_new_item'             => _x('', 'ttp_threads label', 'ttp'),
                'edit_item'                => _x('View Threads Post', 'ttp_threads label', 'ttp'),
                //'new_item'                 => _x('', 'ttp_threads label', 'ttp'),
                //'view_item'                => _x('', 'ttp_threads label', 'ttp'),
                //'view_items'               => _x('', 'ttp_threads label', 'ttp'),
                //'search_items'             => _x('', 'ttp_threads label', 'ttp'),
                //'not_found'                => _x('', 'ttp_threads label', 'ttp'),
                //'not_found_in_trash'       => _x('', 'ttp_threads label', 'ttp'),
                //'parent_item_colon'        => _x('', 'ttp_threads label', 'ttp'),
                //'all_items'                => _x('', 'ttp_threads label', 'ttp'),
                //'archives'                 => _x('', 'ttp_threads label', 'ttp'),
                //'attributes'               => _x('', 'ttp_threads label', 'ttp'),
                //'insert_into_item'         => _x('', 'ttp_threads label', 'ttp'),
                //'uploaded_to_this_item'    => _x('', 'ttp_threads label', 'ttp'),
                //'featured_image'           => _x('', 'ttp_threads label', 'ttp'),
                //'set_featured_image'       => _x('', 'ttp_threads label', 'ttp'),
                //'remove_featured_image'    => _x('', 'ttp_threads label', 'ttp'),
                //'use_featured_image'       => _x('', 'ttp_threads label', 'ttp'),
                //'menu_name'                => _x('', 'ttp_threads label', 'ttp'),
                //'filter_items_list'        => _x('', 'ttp_threads label', 'ttp'),
                //'filter_by_date'           => _x('', 'ttp_threads label', 'ttp'),
                //'items_list_navigation'    => _x('', 'ttp_threads label', 'ttp'),
                //'items_list'               => _x('', 'ttp_threads label', 'ttp'),
                //'item_published'           => _x('', 'ttp_threads label', 'ttp'),
                //'item_published_privately' => _x('', 'ttp_threads label', 'ttp'),
                //'item_reverted_to_draft'   => _x('', 'ttp_threads label', 'ttp'),
                //'item_trashed'             => _x('', 'ttp_threads label', 'ttp'),
                //'item_scheduled'           => _x('', 'ttp_threads label', 'ttp'),
                //'item_updated'             => _x('', 'ttp_threads label', 'ttp'),
                //'item_link'                => _x('', 'ttp_threads label', 'ttp'),
                //'item_link_description'    => _x('', 'ttp_threads label', 'ttp'),
            ],
            'description'                     => _x('Scrapped threads media', 'Description of ttp_threads', 'ttp'),
            'public'                          => true,
            'hierarchical'                    => true,
            'exclude_from_search'             => false,
            'publicly_queryable'              => true,
            'show_ui'                         => true,
            'show_in_menu'                    => true,
            'show_in_nav_menus'               => true,
            'show_in_admin_bar'               => false,
            'show_in_rest'                    => true,
            'rest_base'                       => 'ttp_threads',
            'rest_namespace'                  => 'wp/v2',
            'rest_controller_class'           => \WP_REST_Posts_Controller::class,
            'autosave_rest_controller_class'  => \WP_REST_Autosaves_Controller::class,
            'revisions_rest_controller_class' => \WP_Rest_Revisions_Controller::class,
            'late_route_registration'         => false,
            'menu_position'                   => null,
            'menu_icon'                       => $icon,
            'capability_type'                 => ['threads_post', 'threads_posts'],
            'capabilities'                    => [
                'edit_post'          => 'edit_threads_post',
                'read_post'          => 'read_threads_post',
                'create_posts'       => 'create_threads_posts',
                'delete_post'        => 'delete_threads_post',
                'edit_posts'         => 'edit_threads_posts',
                'edit_others_posts'  => 'edit_others_threads_posts',
                'delete_posts'       => 'delete_threads_posts',
                'publish_posts'      => 'publish_threads_posts',
                'read_private_posts' => 'read_private_threads_posts',
            ],
            'map_meta_cap'                    => false,
            'supports'                        => false,
            'register_meta_box_cb'            => null,
            'taxonomies'                      => [],
            'has_archive'                     => true,
            'rewrite'                         => [
                'slug'       => 'ttp',
                'with_front' => false,
                'feeds'      => false,
                'pages'      => true,
                'ep_mask'    => EP_PERMALINK,
            ],
            'query_var'                       => 'ttp',
            'can_export'                      => true,
            'delete_with_user'                => null,
            'template'                        => [],
            'template_lock'                   => false,
        ],
    ],
    // End: ttp_threads
];
