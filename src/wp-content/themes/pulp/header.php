<?php

use Pulp\Template;

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php do_action('after_body_start'); ?>

    <header id="site-header" role="banner">

        <div class="container">
            <div class="layout">
                <?php get_template_part('parts/element/logo'); ?>

                <nav id="primary-navigation" arial-label="primary navigation">
                    <div id="navigation-header">
                        <button id="site-navigation-close" aria-controls="primary-navigation" aria-expanded="true" aria-label="close navigation">
                        <span id="site-navigation-close-text">Close</span>

                        <svg id="site-navigation-close-icon" aria-hidden="true" width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <use href="#close-icon" />
                        </svg>
                    </button>
                    </div>
                    <?php
                    wp_nav_menu(
                        [
                            'theme_location' => 'primary',
                            'container'      => false
                        ]
                    );
                    ?>
                </nav>

                <button id="site-navigation-open" aria-controls="primary-navigation" aria-expanded="false" aria-label="open navigation">
                    <span id="site-navigation-open-text">Menu</span>
                    <svg id="site-navigation-option-icon" width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line y1="1" x2="20" y2="1" stroke="currentColor" stroke-width="2"/>
                        <line y1="7" x2="20" y2="7" stroke="currentColor" stroke-width="2"/>
                        <line y1="13" x2="20" y2="13" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main id="site-main">
