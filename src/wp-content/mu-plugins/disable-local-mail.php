<?php

/**
 * PLugin Name:  EP Disable Local Mail
 * Description:  Disables wp_mail() for local environments.
 * Author:       Electric Pulp
 * Author URI:   https://electricpulp.com
 * Version:      1.0.0
 */
if (defined('WP_ENV')) {
	if (WP_ENV === 'local') {
		if (!function_exists('wp_mail')) {
			function wp_mail()
			{
				// silence is golden
			}
		}
	}
}
