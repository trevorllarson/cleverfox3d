<?php
/*
 * Plugin Name:  EP Disable Password Change Notifications
 * Description:  Disables admin notification of password changes.
 * Author:       Electric Pulp
 * Author URI:   https://electricpulp.com
 * Version:      1.0.0
*/
if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification()
    {
        // silence is golden
    }
}
