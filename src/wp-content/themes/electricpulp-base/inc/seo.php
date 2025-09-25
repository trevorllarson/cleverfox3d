<?php
// remove WP's auto-sitemap in favor of our custom one
add_filter('wp_sitemaps_enabled', '__return_false');
// Custom robots.txt..
function custom_robots( $output, $public ) {
	$options = get_option( 'wpseo' );
	if ( class_exists( 'WPSEO_Sitemaps' ) && ( $options['enable_xml_sitemap'] == true ) ) {
		$homeURL = get_home_url();
		$output .= "Sitemap: $homeURL/sitemap_index.xml\n";
	}
	return $output;
}
add_filter( 'robots_txt', 'custom_robots', 0, 2 );


// WP SEO Description Update
add_filter('wpseo_title', 'title_control');
function title_control($title) {

    // Default SEO title check to remove the "Home - " from the homepage's title
    $title = str_replace('Home - ', '', $title);

    return $title;

}


// WP SEO Description Update
add_filter('wpseo_metadesc', 'meta_description_control');
function meta_description_control($description) {

    if(empty($description)) {

        if(!empty(get_the_excerpt())) {
            $description = get_the_excerpt();
        } else if(!empty(get_the_content())) {
            $description = substr(get_the_content(), 0, 150);
        } else {
            $description = get_the_title();
        }

    }

    return $description;
}


/**
 * Get Primary Post Term
 * ---------------------
 * Get the primary term of a post, based on a provided taxonomy and post id,
 * or falling back to the existing post, and the standard Post category taxonomy
 */
function get_primary_post_term( $taxonomy = 'category', $post_id = false ) {

    // Make sure a taxonomy has been provided
    if(!$taxonomy) return false;

    // If no post ID is provided, set it to the current
    if(!$post_id) $post_id = get_the_ID();

    // Make sure Yoast is active
    if(class_exists('WPSEO_Primary_Term')) {

        // Get the primary term.
        $wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post_id );
        $wpseo_primary_term = $wpseo_primary_term->get_primary_term();

        // If we have one, return it.
        if($wpseo_primary_term) {
            $term_object = get_term($wpseo_primary_term);

            if($term_object->name === "Uncategorized") return false;

            return $term_object;
        }

    }

    // If no primary is found, get all the terms for the post
    $terms = get_the_terms($post_id, $taxonomy);

    // If no terms are available for the post, fail
    if (!$terms || is_wp_error($terms)) return false;

    if($terms[0]->name === "Uncategorized") return false;

    // Return the first term if terms are available
    return $terms[0];

}
