<?php

/**
 * Enqueue scripts and styles.
 */
function electricpulp_scripts()
{
    global $epHelpers;

    $versionedCss = $epHelpers->compiledAsset('/css/app.css', 'assets/mix-manifest.json');
    $versionedJs  = $epHelpers->compiledAsset('/js/app.js', 'assets/mix-manifest.json');

    wp_enqueue_style('site-styles', '/assets' . $versionedCss, false, null);
    wp_enqueue_script('site-scripts', '/assets' . $versionedJs, ['jquery'], null, true);

    wp_localize_script('site-scripts', 'ajax_global', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax-nonce')
    ));

//    wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . GOOGLE_API_KEY, false, null, true );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'electricpulp_scripts');

/**
 * Set up theme menues
 */
function electricpulp_menus()
{
    register_nav_menus([
        'primary' => esc_html__('Primary', 'electricpulp'),
    ]);
}
add_action('after_setup_theme', 'electricpulp_menus');

/**
 * ACF Options page
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page('Site Options');
}

/**
 * Image Sizes
 * add_image_size( identifier, width, height, hard crop )
 */
add_image_size('hero', 1200, 400, true);

/**
 * Image Type Support
 * http://www.iana.org/assignments/media-types/media-types.xhtml
 */
function electricpulp_mime_types($mimes)
{
    $mimes['webp'] = 'image/webp';
    $mimes['svg']  = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'electricpulp_mime_types');

/**
 * Fixes SVG uploads not properly having their height and width set
 */
function electricpulp_svg_meta_data($data, $id)
{
    $attachment = get_post($id);
    $mime_type  = $attachment->post_mime_type;

    if ($mime_type == 'image/svg+xml') {
        if (empty($data) || empty($data['width']) || empty($data['height'])) {
            $url            = wp_make_link_relative(wp_get_attachment_url($id));
            $xml            = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $url);
            $attr           = $xml->attributes();
            $viewbox        = explode(' ', $attr->viewBox);
            $data['width']  = isset($attr->width) && preg_match('/\d+/', $attr->width, $value) ? (int) $value[0] : (count($viewbox) == 4 ? (int) $viewbox[2] : null);
            $data['height'] = isset($attr->height) && preg_match('/\d+/', $attr->height, $value) ? (int) $value[0] : (count($viewbox) == 4 ? (int) $viewbox[3] : null);
        }
    }

    return $data;
}
add_filter('wp_update_attachment_metadata', 'electricpulp_svg_meta_data', 10, 2);

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function electricpulp_content_width()
{
    // This variable is intended to be overruled from themes.w
    // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $GLOBALS['content_width'] = apply_filters('electricpulp_content_width', 640);
}
add_action('after_setup_theme', 'electricpulp_content_width', 0);

/**
 * Scroll to Gravity Form upon submission and reloading of page
 */
add_filter('gform_confirmation_anchor', '__return_true');
