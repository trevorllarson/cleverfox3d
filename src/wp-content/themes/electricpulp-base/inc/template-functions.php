<?php
/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function electricpulp_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'electricpulp_pingback_header' );



/**
 * Change the Query Parameters
 * - Activate if you need control over the main query, useful for excluding posts that may be featured at the top of the blog page
 */
// add_action( 'pre_get_posts', 'change_query_parameters', 1 );
function change_query_parameters( $query ) {
  // Check that we are only checking on the front-end of the site
  if(!is_admin()) {
    // Check that this is only on the main blog posts query and on the actual listings page
    if($query->is_main_query() and !is_single()) {
    }
  }
}

/**
 * @param int|string|WP_Term $menu Menu ID, slug, name, or object.
 * @return array
 */
function wpGetMenuArray($menu) {

    $array_menu = wp_get_nav_menu_items($menu);

    $menu = [];

    foreach ($array_menu as $m) {
        if (empty($m->menu_item_parent)) {
            $menu[$m->ID] = [];
            $menu[$m->ID]['ID']      =   $m->ID;
            $menu[$m->ID]['title']       =   $m->title;
            $menu[$m->ID]['target']       =   $m->target;
            $menu[$m->ID]['url']         =   $m->url;
            $menu[$m->ID]['children']    =   array();
        }
    }

    $submenu = [];

    foreach ($array_menu as $m) {
        if ($m->menu_item_parent) {
            $submenu[$m->ID] = [];
            $submenu[$m->ID]['ID']       =   $m->ID;
            $submenu[$m->ID]['title']    =   $m->title;
            $submenu[$m->ID]['target']    =   $m->target;
            $submenu[$m->ID]['url']  =   $m->url;
            $menu[$m->menu_item_parent]['children'][$m->ID] = $submenu[$m->ID];
        }
    }
    return $menu;

}

function googleAnalyticsId() {
	return defined('GA_ID') ? GA_ID : '';
}

function googleAnalyticsHead($id = '') {

    if(defined('WP_ENV') && (WP_ENV === 'local' || WP_ENV === 'staging')) return '';

	if (empty($id)) {
		$id = googleAnalyticsId();
	}

    if (empty($id)) return '';

    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $id; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?= $id; ?>');
    </script>
    <?php

    return '';
}

function googleTagManagerId() {
	return defined('GTM_ID') ? GTM_ID : '';
}

function googleTagManagerHead($id = '') {

    if (empty($id)) {
        $id = googleTagManagerId();
    }

    if (empty($id)) return '';

    ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer', '<?= $id; ?>' );</script>
    <!-- End Google Tag Manager -->
    <?php

    return '';
}

function googleTagManagerBody($id = '') {

    if (empty($id)) {
        $id = googleTagManagerId();
    }

    if(empty($id)) return '';

    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $id; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php

    return '';

}

// Style the separator for better visibility in the admin menu of different sections
function admin_inline_css() {
    echo '<style>
        #adminmenu li.wp-menu-separator {
            margin: 8px 0 14px;
            cursor: inherit;
            background: #4a4a4a;
        }
    </style>';
}
add_action('admin_head', 'admin_inline_css');

// Add new dividers at given locations. Confirm the index doesn't exist or it will be overwritten
//add_action('admin_menu', function () {
//    global $menu;
//    $menu[21] = ['', 'read', '', '', 'wp-menu-separator'];
//    $menu[24] = ['', 'read', '', '', 'wp-menu-separator'];
//});
