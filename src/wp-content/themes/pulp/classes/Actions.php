<?php

namespace Pulp;

use Pulp\Glide;

class Actions
{
    // if you make Templates Parts page, set the ID here. Filters.php will reference this as well.
    const TEMPLATE_PARTS_PAGE_ID = null;

    public function __construct()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        add_action('wp_head', [$this, 'noindex'], 1);
        add_action('wp_head', [$this, 'gtmHead'], 3);
        add_action('wp_head', [$this, 'ga'], 10);
        add_action('wp_head', [$this, 'noconflict'], 99);
        add_action('wp_head', [$this, 'externalAssets'], 7);
        add_action('wp_head', [$this, 'globalJsVars']);
        add_action('wp_head', [$this, 'yoast'], ~PHP_INT_MAX);
        add_action('wp_footer', [$this, 'footerScripts'], 10);
        add_action('after_body_start', [$this, 'gtmBody'], 1);
        add_action('after_body_start', [$this, 'skip'], 5);
        add_action('after_body_start', [$this, 'symbols'], 10);
        add_action('wp_enqueue_scripts', [$this, 'assets'], 10);
        add_action('after_setup_theme', [$this, 'menus'], 10);
        add_action('after_setup_theme', [$this, 'themeSupport'], 10);
        add_action('acf/init', [$this, 'siteOptionsMenus']);
        add_action('wp_content_img_tag', [$this, 'getImageTag'], 10, 3);
        add_action('plugins_loaded', [$this, 'deactivateProductionPlugins']);
        add_action('pre_get_posts', [$this, 'modifyQuery'], 10);
        add_action("wp_ajax_save_focal_point", [$this, "saveFocalPoint"]);
        add_action("wp_ajax_remove_focal_point", [$this, "removeFocalPoint"]);
        add_action("wp_ajax_get_focal_point", [$this, "getFocalPoint"]);
        add_action('template_redirect', [$this, 'templateRedirects']);
        $this->glideSetup();
        $this->images();
    }

    public static function getTemplatePartsPageId()
    {
        return self::TEMPLATE_PARTS_PAGE_ID;
    }

    /**
     * Init Glide
     */
    public function glideSetup()
    {
        if (!defined('DISABLE_GLIDE')) {
            return;
        }
        if (DISABLE_GLIDE) {
            return;
        }
        $glide = new Glide();
        $glide->addActions();
    }

    /*
    * Passes images in post content through Glide
    */
    public function getImageTag($filtered_image, $context, $attachment_id)
    {

        if (DISABLE_GLIDE) {
            return $filtered_image;
        }

        // $attachment_id can be missing if the the uploads and db are out of sync
        if (!$attachment_id) {
            return $filtered_image;
        }
        if (get_post_mime_type($attachment_id) === 'image/svg+xml') {
            return $filtered_image;
        }
        $maxWidth = 1410;
        $glide = new Glide();
        $processor = new \WP_HTML_Tag_Processor($filtered_image);
        // $processor isn't looking at any specific tag yet, so we need to move the pointer to the image we sent in as the HTML to process
        $processor->next_tag();
        // now we can work with it
        $w = $processor->get_attribute('width');
        $h = $processor->get_attribute('height');

        if ($w > $maxWidth) {
            $ratio = $maxWidth / $w;
            $w = $maxWidth;
            $h = ceil($h * $ratio);
        }
        $processor->remove_attribute('srcset');
        $processor->remove_attribute('sizes');
        $processor->set_attribute('loading', 'lazy');
        $processor->set_attribute('src', $glide->imageUrl($attachment_id, [
            'w' => $w,
            'h' => $h
        ]));

        // send back the HTML, now with the Glide URL in place
        return $processor->get_updated_html();
    }

    /**
     * Attemps to load versioned asset in mix-manifest or falls back to enqueued path
     */
    public static function getAsset(string $filename)
    {
        $manifestPath   = get_template_directory() . '/mix-manifest.json';
        $manifest       = ( file_exists($manifestPath) ) ? json_decode(file_get_contents($manifestPath), true) : array();
        $asset =  ( @array_key_exists($filename, $manifest) ) ? $manifest[ $filename ] : $filename;
        return get_template_directory_uri() . $asset;
    }

    /**
     * Registers/Unregistered theme assets
     */
    public function assets()
    {
        wp_register_style('theme-base', get_template_directory_uri() . '/style.css'); // no versioning because this doesn't change
        wp_register_style('theme', $this->getAsset('/assets/css/theme.css'), false, null);

        wp_register_script('theme', $this->getAsset('/assets/js/theme.js'), ['jquery'], null, true);

        wp_enqueue_style('theme-base');
        wp_enqueue_style('theme');
        wp_enqueue_script('theme');

        wp_localize_script(
            'site-scripts',
            'ajax_global',
            array(
                'url'   => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce'),
            )
        );
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('classic-theme-styles');
    }

    /**
     * Declares theme support
     */
    public function themeSupport()
    {
        remove_theme_support('core-block-patterns');
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('disable-layout-styles');
    }

    /**
     * Registers/Unregistered theme assets in the footer
     */
    public function footerScripts()
    {
        // wp_dequeue_style('core-block-supports');
    }

    /**
     * Registers menu locations
     */
    public function menus()
    {
        register_nav_menus(
            array(
                'primary'   => 'Primary',
                'footer'  => 'Footer',
                'secondary-1'  => 'Secondary 1',
                'secondary-2'  => 'Secondary 2',
            )
        );
    }

    /**
     * Register Site Options Menus
     */
    public function siteOptionsMenus()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page('Site Options');
            // acf_add_options_page('Footer');
        }
    }

    /**
     * Registers image sizes
     * add_image_size('size', width, height, hard crop)
     */
    public function images()
    {
        add_image_size('hero', 1410, 700, true);
        add_image_size('loop', 450, 330, true);
    }

    /**
     * Modifies the query before making db request
     */
    public function modifyQuery($query)
    {
    }

    /**
     * The next several methods insert various static parts into the theme
     */
    public function externalAssets()
    {
        get_template_part('parts/hooked/external-assets');
    }

    public function noconflict()
    {
        get_template_part('parts/hooked/noconflict');
    }

    public function symbols()
    {
        get_template_part('parts/hooked/symbols');
    }

    public function skip()
    {
        get_template_part('parts/hooked/skip');
    }

    public function ga()
    {
        get_template_part('parts/hooked/ga');
    }

    public function gtmHead()
    {
        get_template_part('parts/hooked/gtm', null, ['head' => true]);
    }

    public function gtmBody()
    {
        get_template_part('parts/hooked/gtm', null, ['head' => false]);
    }

    public function globalJsVars()
    {
        get_template_part('parts/hooked/js-vars');
    }

    public function noindex()
    {
        get_template_part('parts/hooked/noindex');
    }

    /**
     * Removes the comment that Yoast drops in at the end of the page
     */
    public function yoast()
    {
        ob_start(
            function ($o) {
                return preg_replace('/^\n?<!--.*?[Y]oast.*?-->\n?$/mi', '', $o);
            }
        );
    }

    /**
     * Auto-disables plugins defined in wp-config
     */
    public function deactivateProductionPlugins()
    {
        if (defined('DISABLED_LOCAL_PLUGINS')) {
            deactivate_plugins(DISABLED_LOCAL_PLUGINS);
        }
    }

    /*
    * Saves focal point for images
    */
    public function saveFocalPoint()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], "focal_point_nonce")) {
            exit();
        }
        $saved = update_post_meta($_REQUEST["post_id"], "focal-point", $_REQUEST["points"]);
        echo json_encode(['success' => $saved]);
        die();
    }

    /*
    * Remove focal point for images
    */
    public function removeFocalPoint()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], "focal_point_nonce")) {
            exit();
        }
        $removed = delete_post_meta($_REQUEST["post_id"], "focal-point");
        // this always returns true because either their wasn't one set to begin with ($removed = false), or the meta was actually deleted ($removed = true); For the UI, we always want to remove a visible focal point
        echo json_encode(['success' => true]);
        die();
    }
    /*
    * Retrieves focal point for images
    */
    public function getFocalPoint()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], "focal_point_nonce")) {
            exit();
        }
        $value = get_post_meta($_REQUEST["post_id"], "focal-point", true);
        echo json_encode($value);
        die();
    }

    public function templateRedirects()
    {
        global $post;
        if (is_page() && ( $post->post_parent == self::TEMPLATE_PARTS_PAGE_ID || $post->ID == self::TEMPLATE_PARTS_PAGE_ID )) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
        }
    }
}
