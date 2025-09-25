<?php
function electricpulp_security_setup()
{

    add_filter('auto_update_plugin', '__return_false');
    add_filter('auto_update_theme', '__return_false');

    // Disable XML-RPC
    add_filter('xmlrpc_enabled', '__return_false');
}
add_action('after_setup_theme', 'electricpulp_security_setup');

/**
 * Disable WP REST API user enumeration
 */
function disable_rest_api_users($endpoints)
{
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
}
add_filter('rest_endpoints', 'disable_rest_api_users');

//Remove the REST API endpoint for oEmbed. THIS IS REQUIRED FOR THE EMBED GUT BLOCK
// remove_action('rest_api_init', 'wp_oembed_register_route');

// Turn off oEmbed auto discovery.
add_filter('embed_oembed_discover', '__return_false');

//Don't filter oEmbed results.
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

//Remove oEmbed discovery links.
remove_action('wp_head', 'wp_oembed_add_discovery_links');

//Remove oEmbed JavaScript from the front-end and back-end.
remove_action('wp_head', 'wp_oembed_add_host_js');

// Remove WP Generator Tag
remove_action('wp_head', 'wp_generator');
