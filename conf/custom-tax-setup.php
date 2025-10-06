<?php

// No direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    // 'ttp_tag' BEGIN
    [
        // Taxonomy name. Maximum 32 characters.
        'ttp_tag',

        // Object types. Required.
        ['ttp_threads'],

        // Arguments.
        [
            'labels'                => [
//                'name'                       => _x('', 'ttp_tag label', 'ttp_tag'),
//                'singular_name'              => _x('', 'ttp_tag label', 'ttp_tag'),
//                'search_items'               => _x('', 'ttp_tag label', 'ttp_tag'),
//                'popular_items'              => _x('', 'ttp_tag label', 'ttp_tag'),
//                'all_items'                  => _x('', 'ttp_tag label', 'ttp_tag'),
//                'parent_item'                => _x('', 'ttp_tag label', 'ttp_tag'),
//                'parent_item_colon'          => _x('', 'ttp_tag label', 'ttp_tag'),
//                'name_field_description'     => _x('', 'ttp_tag label', 'ttp_tag'),
//                'slug_field_description'     => _x('', 'ttp_tag label', 'ttp_tag'),
//                'parent_field_description'   => _x('', 'ttp_tag label', 'ttp_tag'),
//                'desc_field_description'     => _x('', 'ttp_tag label', 'ttp_tag'),
//                'edit_item'                  => _x('', 'ttp_tag label', 'ttp_tag'),
//                'view_item'                  => _x('', 'ttp_tag label', 'ttp_tag'),
//                'update_item'                => _x('', 'ttp_tag label', 'ttp_tag'),
//                'add_new_item'               => _x('', 'ttp_tag label', 'ttp_tag'),
//                'new_item_name'              => _x('', 'ttp_tag label', 'ttp_tag'),
//                'template_name'              => _x('', 'ttp_tag label', 'ttp_tag'),
//                'separate_items_with_commas' => _x('', 'ttp_tag label', 'ttp_tag'),
//                'add_or_remove_items'        => _x('', 'ttp_tag label', 'ttp_tag'),
//                'choose_from_most_used'      => _x('', 'ttp_tag label', 'ttp_tag'),
//                'not_found'                  => _x('', 'ttp_tag label', 'ttp_tag'),
//                'no_terms'                   => _x('', 'ttp_tag label', 'ttp_tag'),
//                'filter_by_item'             => _x('', 'ttp_tag label', 'ttp_tag'),
//                'items_list_navigation'      => _x('', 'ttp_tag label', 'ttp_tag'),
//                'items_list'                 => _x('', 'ttp_tag label', 'ttp_tag'),
//                'most_used'                  => _x('', 'ttp_tag label', 'ttp_tag'),
//                'back_to_items'              => _x('', 'ttp_tag label', 'ttp_tag'),
//                'item_link'                  => _x('', 'ttp_tag label', 'ttp_tag'),
//                'item_link_description'      => _x('', 'ttp_tag label', 'ttp_tag'),
            ],
            'description'           => 'Tag for storing and indexing \'topic_tag\' field.',
            'public'                => false,
            'publicly_queryable'    => false,
            'hierarchical'          => false,
            'show_ui'               => false,
            'show_in_menu'          => false,
            'show_in_nav_menus'     => false,
            'show_in_rest'          => false,
            'rest_base'             => 'ttp_tag',
            'rest_namespace'        => 'wp/v2',
            'rest_controller_class' => \WP_REST_Terms_Controller::class,
            'show_tagcloud'         => false,
            'show_in_quick_edit'    => false,
            'show_admin_column'     => true,
            'meta_box_cb'           => null,
            'meta_box_sanitize_cb'  => null,
            'rewrite'               => [
                'slug'         => 'ttp_tag',
                'with_front'   => true,
                'hierarchical' => false,
                'ep_mask'      => EP_NONE,
            ],
            'query_var'             => 'ttp_tag',
            'update_count_callback' => null,
            'sort'                  => false,
            'args'                  => [],
        ],
    ] // 'ttp_tag' END
];
