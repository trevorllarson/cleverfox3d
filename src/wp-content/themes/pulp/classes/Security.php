<?php

namespace Pulp;

class Security
{
    public function __construct()
    {
        add_filter('embed_oembed_discover', '__return_false');
        add_filter('auto_update_plugin', '__return_false');
        add_filter('auto_update_theme', '__return_false');
        add_filter('xmlrpc_enabled', '__return_false');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result'); // Don't filter oEmbed results.
        remove_action('wp_head', 'wp_oembed_add_discovery_links'); // Remove oEmbed discovery links.
        remove_action('wp_head', 'wp_oembed_add_host_js'); // Remove oEmbed JavaScript from the front-end and back-end.
        remove_action('wp_head', 'wp_generator');
        add_filter('rest_endpoints', [$this, 'disableRestApiUsers']);
    }

    /**
     * Disables WP REST API user enumeration
     *
     * @param array $endpoints REST endpoints.
     * @return array
     */
    public function disableRestApiUsers(array $endpoints): array
    {
        // Prevents console error while editing posts.
        if (current_user_can('edit_posts')) {
            return $endpoints;
        }

        if (isset($endpoints['/wp/v2/users'])) {
            unset($endpoints['/wp/v2/users']);
        }
        if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
            unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
        }
        return $endpoints;
    }
}
