<?php
/**
 * Extend WordPress search to include custom fields
 *
 * https://adambalee.com
 */

/**
 * Join posts and postmeta tables
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 */
function electricpulp_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'electricpulp_search_join' );

/**
 * Modify the search query with posts_where
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 */
function electricpulp_search_where( $where ) {
    global $pagenow, $wpdb;

    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
add_filter( 'posts_where', 'electricpulp_search_where' );

/**
 * Prevent duplicates
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 */
function electricpulp_search_distinct( $where ) {

    if ( is_search() ) {
      return "DISTINCT";
    }

    return $where;
}
add_filter( 'posts_distinct', 'electricpulp_search_distinct' );

/**
 * Return a HTTP 404 for empty search results
 */
function electricpulp_search_404_empty_results()
{
    if (is_search() && !have_posts()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }
}
add_action( 'get_header', 'electricpulp_search_404_empty_results' );
