<!doctype html>
<html <?php language_attributes(); ?>>
<head>

	<?php
	googleTagManagerHead();
	googleAnalyticsHead();
	?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php electricpulp_favicons() ?>

	<?php wp_head(); ?>

	<script>var $ = jQuery.noConflict();</script>

</head>

<body <?php body_class(); ?>>

    <?php googleTagManagerBody(); ?>

	<a href="#siteMain" class="visually-hidden-focusable"><?php esc_html_e( 'Skip to content', 'electricpulp' ); ?></a>

	<header class="site-header" id="siteHeader" role="banner">
		<div id="site-branding">
			<?php echo electricpulp_get_site_logo(); ?>
		</div>
		<nav id="site-navigation">
			<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'electricpulp' ); ?></button>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container' => false,
				'menu_id'        => 'menu-primary',
			) );
			?>
		</nav>
	</header>

	<main class="site-main" id="siteMain" role="main">
