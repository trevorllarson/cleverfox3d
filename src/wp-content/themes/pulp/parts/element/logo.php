<?php

$before = '<a href="' . esc_url(home_url()) . '" rel="home" class="site-logo">';
$after  = '</a>';
if (is_front_page()) {
    $before = '<div class="site-logo">';
    $after  = '</div>';
}
?>
<?php echo $before; // phpcs:ignore ?>
<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.svg" width="146" height="41" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
<?php echo $after;// phpcs:ignore ?>
