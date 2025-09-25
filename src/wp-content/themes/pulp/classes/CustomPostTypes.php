<?php

    // TODO: WIP

    namespace Pulp;

    class CustomPostTypes
    {
        public function __construct()
        {
            add_action('init', [$this, 'registerCustomPostTypes']);
            add_action('init', [$this, 'registerCustomTaxonomies']);
        }

        public function registerCustomPostTypes()
        {
//            $this->registerCommercialProperty();
        }

        public function registerCustomTaxonomies()
        {
            // Register custom taxonomies here
            // Example:
            /*
            register_taxonomy('custom_taxonomy', 'custom_type', [
                'labels' => [
                    'name' => __('Custom Taxonomies'),
                    'singular_name' => __('Custom Taxonomy')
                ],
                'public' => true,
                'hierarchical' => true,
                'show_admin_column' => true,
            ]);
            */
        }

        public function registerCommercialProperty()
        {
            $singleLabel = 'Commercial Property';
            $pluralLabel = 'Commercial Properties';

            // register the type
            register_post_type(
                'commercial-property',
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
                    'menu_position'       => 27,
                    'menu_icon'           => 'dashicons-building', // https://developer.wordpress.org/resource/dashicons/
                    'can_export'          => true,
                    'has_archive'         => true, //set to false to use a custom gut page for the /team-member root url
                    'exclude_from_search' => false,
                    'publicly_queryable'  => true,
                    'capability_type'     => 'post',
                    'rewrite'             => array('with_front' => false, 'slug' => ''), //you can use slashes in this slug for organization
                    'show_in_rest'        => false // Set to false to disable Gutenberg-style editor
                )
            );
        }
    }
