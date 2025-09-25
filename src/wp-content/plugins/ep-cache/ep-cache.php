<?php

/**
 * Plugin Name:  EP Cache
 * Description:  Better nginx cache management.
 * Version:      1.0
 * Author:       Electric Pulp
 * Author URI:   https://electricpulp.com/
 * License:      GPLv2 or later
 * Text Domain:  ep-cache
 */

if (!defined('ABSPATH')) exit;

class EpCache
{

	private $screen = 'tools_page_ep-cache';
	private $capability = 'manage_options';
	private $admin_page = 'tools.php?page=ep-cache';

	public function __construct()
	{
		add_filter('option_ep_cache_path', 'sanitize_text_field');
		add_filter('option_ep_cache_purge_singular', 'sanitize_text_field');
		add_filter('option_ep_cache_purge_all', 'sanitize_text_field');
		add_filter('option_ep_cache_auto', 'absint');
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_plugin_actions_links'));

		if (get_option('ep_cache_auto')) {
			add_action('init', array($this, 'register_purge_actions'), 20);
		}

		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_menu', array($this, 'add_admin_menu_page'));
		add_action('admin_bar_menu', array($this, 'add_admin_bar_node'), 100);
		add_action('load-' . $this->screen, array($this, 'do_admin_actions'));
		add_action('load-' . $this->screen, array($this, 'add_settings_notices'));
		add_action('template_redirect', array($this, 'simulate_cache'));
	}

	/* auto-pruge on these events */
	public function register_purge_actions()
	{
		add_action('clean_post_cache', array($this, 'purge_post'), 10, 2);
		add_action('clean_term_cache', array($this, 'purge_term'), 10, 3);
		add_action('switch_theme', array($this, 'purge_all'));
	}

	public function register_settings()
	{
		register_setting('ep-cache', 'ep_cache_path', 'sanitize_text_field');
		register_setting('ep-cache', 'ep_cache_purge_singular', 'sanitize_text_field');
		register_setting('ep-cache', 'ep_cache_purge_all', 'sanitize_text_field');
		register_setting('ep-cache', 'ep_cache_auto', 'absint');
	}

	public function add_settings_notices()
	{

		$path_error = $this->is_valid_path();

		if (isset($_GET['message']) && !isset($_GET['settings-updated'])) {

			// show cache purge success message
			if ($_GET['message'] === 'cache-purged') {
				add_settings_error('', 'ep_cache_path', __('Cache purged.', 'ep-cache'), 'updated');
			}

			// show cache purge failure message
			if ($_GET['message'] === 'purge-cache-failed') {
				add_settings_error('', 'ep_cache_path', sprintf(__('Cache could not be purged. %s', 'ep-cache'), wptexturize($path_error->get_error_message())));
			}
			if ($_GET['message'] === 'purge-url-failed') {
				add_settings_error('', 'ep_cache_path', __('URL was not in cache.', 'ep-cache'), 'updated');
			}
		} elseif (is_wp_error($path_error) && $path_error->get_error_code() === 'fs') {

			// show cache path problem message
			add_settings_error('', 'ep_cache_path', wptexturize($path_error->get_error_message('fs')));
		}
	}

	public function do_admin_actions()
	{
		if (isset($_GET['action']) && $_GET['action'] === 'purge-all' && wp_verify_nonce($_GET['_wpnonce'], 'purge-all')) {
			$result = $this->purge_all();
			wp_safe_redirect(admin_url(add_query_arg('message', is_wp_error($result) ? 'purge-cache-failed' : 'cache-purged', $this->admin_page)));
			exit;
		}
		if (isset($_POST['ep_cache_purge_url']) && $_POST['ep_cache_purge_url'] !== '') {
			if (wp_verify_nonce($_POST['_purge_nonce'], 'ep_purge')) {
				$result = $this->purge_url($_POST['ep_cache_purge_url']);
				wp_safe_redirect(admin_url(add_query_arg('message', is_wp_error($result) ? 'purge-url-failed' : 'cache-purged', $this->admin_page)));
				exit;
			}
		}
	}

	public function add_admin_bar_node($bar)
	{
		if (!current_user_can($this->capability)) {
			return;
		}

		$bar->add_node(array(
			'id' => 'ep-cache',
			'title' => __('Cache', 'ep-cache'),
			'href' => admin_url($this->admin_page)
		));

		$bar->add_node(array(
			'parent' => 'ep-cache',
			'id' => 'cache-settings',
			'title' => __('Settings', 'ep-cache'),
			'href' => admin_url($this->admin_page)
		));
		$bar->add_node(array(
			'parent' => 'ep-cache',
			'id' => 'purge-url',
			'title' => __('Purge URL', 'ep-cache'),
			'href' => admin_url($this->admin_page) . '#cache-tools'
		));
		$bar->add_node(array(
			'parent' => 'ep-cache',
			'id' => 'purge-all',
			'title' => __('Purge Everything', 'ep-cache'),
			'href' => wp_nonce_url(admin_url(add_query_arg('action', 'purge-all', $this->admin_page)), 'purge-all')
		));
	}

	public function add_admin_menu_page()
	{
		add_management_page(
			__('Cache', 'ep-cache'),
			__('Cache', 'ep-cache'),
			$this->capability,
			'ep-cache',
			array($this, 'show_settings_page')
		);
	}

	public function show_settings_page()
	{
		require_once plugin_dir_path(__FILE__) . '/includes/settings-page.php';
	}

	public function add_plugin_actions_links($links)
	{
		return array_merge(
			array('<a href="' . admin_url($this->admin_page) . '">' . __('Settings', 'ep-cache') . '</a>'),
			$links
		);
	}

	private function make_hash($url)
	{
		$url = parse_url($url);
		$path = isset($url['path']) ? $url['path'] : '/'; 
		$query = isset($url['query']) ? '?' . $url['query'] : ''; 
		$key = $url['scheme'].'GET'.$url['host'].$path.$query;
		// error_log('make hash: ' . $key);
		return md5($key); 
	}

	private function make_file_path($hash)
	{
		$path = get_option('ep_cache_path');
		return $path . substr($hash, -1) . DIRECTORY_SEPARATOR . substr($hash,-3,2) . DIRECTORY_SEPARATOR . $hash;
	}

	public function purge_url($url)
	{
		global $wp_filesystem;
		
		$path_error = $this->is_valid_path(); // also initializes WP_Filesystem
		
		// abort if cache zone path is not valid
		if ( is_wp_error( $path_error ) ) {
			return $path_error;
		}
		
		$hash = $this->make_hash($url);
		$file = $this->make_file_path($hash);
		$wp_filesystem->delete($file);
	}

	public function purge_all()
	{
		// error_log('purge_all');
		global $wp_filesystem;
		
		$path_error = $this->is_valid_path(); // also initializes WP_Filesystem
		
		// abort if cache zone path is not valid
		if ( is_wp_error( $path_error ) ) {
			return $path_error;
		}
		
		$path = get_option('ep_cache_path');
		$wp_filesystem->rmdir( $path, true ); // nuke
		$wp_filesystem->mkdir( $path ); // restore
		return true;
	}

	public function purge_post($postId, $post)
	{
		// this action can run multiple times per request, so bail early
		if (did_action( 'clean_post_cache' ) > 1) {
			return;
		}

		$clearOnePostTypes = apply_filters('ep_cache_single_types', explode(',', str_replace(' ', '', get_option('ep_cache_purge_singular'))));
		$clearAllPostTypes = apply_filters('ep_cache_all_types', explode(',', str_replace(' ', '', get_option('ep_cache_purge_all'))));

		// only clear this item
		if (in_array($post->post_type, $clearOnePostTypes)) {
			$this->purge_url(get_the_permalink($post));
		}
		// clear everything
		if (in_array($post->post_type, $clearAllPostTypes) || $post->post_type == 'nav_menu_item') {
			$this->purge_all();
		}
	}

	public function purge_term($terms, $taxonomy, $cleanAll)
	{
		// error_log('purge term');

		// term updates usually pass in $cleanAll = true, but we probably don't want to blow out all URLs when we update terms?
		// if( $cleanAll ) {
		// 	$this->purge_all();
		// 	return;
		// }

		// clear the taxonomy archive URL
		// nav updates trigger this too, so make sure we get a valid URL back to purge
		foreach ($terms as $termId) {
			$url = get_term_link($termId, $taxonomy);
			if (is_wp_error($url)) {
				continue;
			}
			$this->purge_url($url);
		}
	}

	private function initialize_filesystem()
	{

		$path = get_option('ep_cache_path');

		// if the cache directory doesn't exist, try to create it
		if (!file_exists($path)) {
			mkdir($path);
		}

		// load WordPress file API?
		if (!function_exists('request_filesystem_credentials')) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		ob_start();
		$credentials = request_filesystem_credentials('', '', false, $path, null, true);
		ob_end_clean();

		if ($credentials === false) {
			return false;
		}

		if (!WP_Filesystem($credentials, $path, true)) {
			return false;
		}

		return true;
	}

	private function is_valid_path()
	{

		global $wp_filesystem;

		$path = get_option('ep_cache_path');

		if (empty($path)) {
			return new WP_Error('empty', __('"Cache Zone Path" is not set.', 'ep-cache'));
		}

		if ($this->initialize_filesystem()) {

			if (!$wp_filesystem->exists($path)) {
				return new WP_Error('fs', __('"Cache Zone Path" does not exist.', 'ep-cache'));
			}

			if (!$wp_filesystem->is_dir($path)) {
				return new WP_Error('fs', __('"Cache Zone Path" is not a directory.', 'ep-cache'));
			}

			$list = $wp_filesystem->dirlist($path, true, true);

			if (is_array($list) && !$this->validate_dirlist($list)) {
				return new WP_Error('fs', __('"Cache Zone Path" does not appear to be a Nginx cache zone directory.', 'ep-cache'));
			}

			if (!$wp_filesystem->is_writable($path)) {
				return new WP_Error('fs', __('"Cache Zone Path" is not writable.', 'ep-cache'));
			}

			return true;
		}

		return new WP_Error('fs', __('Filesystem API could not be initialized.', 'ep-cache'));
	}

	private function validate_dirlist($list)
	{

		if (defined('SIMULATE_CACHE')) return true; // bypass this for local development

		foreach ($list as $item) {
						
			// abort if file is not a MD5 hash
			if ($item['type'] === 'f' && (strlen($item['name']) !== 32 || !ctype_xdigit($item['name']))) {
				return false;
			}

			// validate subdirectories recursively
			if ($item['type'] === 'd' && !$this->validate_dirlist($item['files'])) {
				return false;
			}
		}

		return true;
	}

	public function simulate_cache()
	{
		global $wp_filesystem;

		if (!defined('SIMULATE_CACHE')) return;
		if (!SIMULATE_CACHE) return;
		if (is_admin()) return;
		if (strpos($_SERVER['REQUEST_URI'], 'robots.txt') !== false) return; // not sure why this one keeps showing up with every request (even admin), maybe valet?

		$path_error = $this->is_valid_path(); // also initializes WP_Filesystem
		
		// abort if cache zone path is not valid
		if ( is_wp_error( $path_error ) ) {
			return;
		}

		$path = get_option('ep_cache_path');
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; // should always be https but check anyway
		$url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$hash = $this->make_hash($url); 
		$file = $this->make_file_path($hash);
		if ($wp_filesystem->exists($file)) return;
		
		// error_log('adding key: ' . $url);
		// error_log('adding hash: ' . $hash);
		$level1 = substr($hash, -1);
		$level2 = substr($hash, -3, 2);
		$wp_filesystem->mkdir($path.$level1); // wp_filesystem will make if doesn't exist, but not overwrite if it does
		$wp_filesystem->mkdir($path.$level1.'/'.$level2);
		$wp_filesystem->touch($file);
	}
}

new EpCache;
