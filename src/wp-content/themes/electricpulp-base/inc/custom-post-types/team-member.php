<?php
/*
	Example Custom Post Type include
	All functionality related to this post type should live in this file.
 */
function electricpulp_team_member_init()
{

    $singleLabel = 'Team Member';
    $pluralLabel = 'Team Members';

    // register the type
    register_post_type(
        'team-member',
        array(
            'labels'              => array(
                'name'                => _x($pluralLabel, 'Post Type General Name'),
                'singular_name'       => _x($singleLabel, 'Post Type Singular Name'),
                'menu_name'           => __($pluralLabel),
                'parent_item_colon'   => __('Parent ' . $singleLabel),
                'all_items'           => __('All ' . $pluralLabel),
                'view_item'           => __('View ' . $singleLabel),
                'add_new_item'        => __('Add New ' . $singleLabel),
                'add_new'             => __('Add New'),
                'edit_item'           => __('Edit ' . $singleLabel),
                'update_item'         => __('Update ' . $singleLabel),
                'search_items'        => __('Search ' . $pluralLabel),
                'not_found'           => __('Not Found'),
                'not_found_in_trash'  => __('Not found in Trash'),
            ),
            'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-layout', // https://developer.wordpress.org/resource/dashicons/
            'can_export'          => true,
            'has_archive'         => true, //set to false to use a custom gut page for the /team-member root url
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'rewrite'             => array('with_front' => false, 'slug' => ''), //you can use slashes in this slug for organization
            'taxonomies'          => array('team-member-category'),
            'show_in_rest'        => true // Set to false to disable Gutenberg-style editor
        )
    );

    // register the taxonomies for the type
    register_taxonomy(
        'team-member-category',
        'team-member',
        array(
            'hierarchical' => true,
            'label' => $singleLabel . ' Categories',
            'rewrite' => array('with_front' => false, 'slug' => 'team-member-category'), // set with_front to false to remove any custom permalink settings you might have for the standard Posts.
            'show_in_rest'        => true // Set to false to disable Gutenberg-style editor
        )
    );
}

add_action('init', 'electricpulp_team_member_init');
