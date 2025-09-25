<?php
/**
 * Remove unwanted default settings, scripts, etc. from core WP and WP Plugins
 * ---------------------------------------------------------------------------
 */



/**
 * Remove Generator Tag that displays the WordPress version
 */
remove_action('wp_head', 'wp_generator');



/**
 * Remove Emoji Script from loading on the front
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );



/**
 * Custom theme doesn't need customizer features
 */
function electricpulp_remove_customize_capability( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	if ($cap == 'customize') {
		return array('nope'); // return an unknown capability
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'electricpulp_remove_customize_capability', 10, 4 );



/**
 * Removes the comment that Yoast drops in at the end of the page
 */
add_action('wp_head',function() { ob_start(function($o) {
    return preg_replace('/^\n?<!--.*?[Y]oast.*?-->\n?$/mi','',$o);
}); },~PHP_INT_MAX);



/**
 * Removes the admin bar from the front of the site
 */
function removeAdminBarHeading() {
    remove_action('wp_head', '_admin_bar_bump_cb');
}
//add_action('get_header', 'removeAdminBarHeading');
//show_admin_bar( false );
