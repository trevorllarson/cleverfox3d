<?php /*
   * Plugin Name: EP Resizable Editor Sidebar
   * Description: Enables functionality to make the Gutenberg sidebar width resizable
   * Version: 1.0.6
   * Author: Electric Pulp
   * Author URI: https://electricpulp.com
   * Licence: GPLv2 or later
   */ ?>
<?php function ep_rs_enqueue()
{
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('ep_rs_script', plugin_dir_url(__FILE__) . 'script.js', array('jquery-ui-resizable'), null, true);
    wp_enqueue_style('ep_rs_style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('admin_enqueue_scripts', 'ep_rs_enqueue', 20);
