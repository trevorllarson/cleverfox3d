<?php
function electricpulp_setup()
{

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress handle displaying the <title> tag
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

}
add_action('after_setup_theme', 'electricpulp_setup');

/**
 * SEO Configurations
 */
require get_template_directory() . '/inc/seo.php';

/**
 * Modify Search
 * 2020-120-11 Mc commented out since it seems like unecessary overriding
 * of default behavior that doesn't make a ton of sense. Also, with ACF blocks
 * all of the content in those blocks is now in the post content already, so
 * no need to slow down search like this and include the meta for no reason.
 */
// require get_template_directory() . '/inc/search.php';

/**
 * Helper Functions
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * Remove things from output
 */
require get_template_directory() . '/inc/remove.php';

/**
 * Setup theme components and assets
 */
require get_template_directory() . '/inc/register.php';

/**
 * Tags used by the templates
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Editor mods and custom login
 */
require get_template_directory() . '/inc/admin.php';

/**
 * Custom ACF Blocks
 */
require get_template_directory() . '/inc/acf-blocks.php';

/**
 * Security-related adjustments
 */
require get_template_directory() . '/inc/security.php';

/**
 * Custom Post Types
 * Use a separate file for each post type, named as the plural for the type (member.php, books.php, etc)
 */
// require get_template_directory() . '/inc/custom-post-types/team-member.php';
