<?php

namespace Pulp;

use Pulp\Actions as Actions;

class Filters
{
    public function __construct()
    {
        add_filter('wp_sitemaps_enabled', '__return_false');
        add_filter('upload_mimes', [$this, 'uploadMimes'], 10);
        add_filter('wp_update_attachment_metadata', [$this, 'updateAttachmentMetadata'], 10, 2);
        add_filter('map_meta_cap', [$this, 'mapMetaCap'], 10, 4);
        // add_filter('robots_txt', [$this, 'robotsTxt'], 0, 2);
        add_filter('wpseo_title', [$this, 'wpseoTitle'], 10);
        add_filter('wpseo_metadesc', [$this, 'wpseoMetaDesc'], 10);
        add_filter('wpseo_opengraph_title', [$this, 'wpseoOgTitle'], 10);
        add_filter('wpseo_exclude_from_sitemap_by_post_ids', [$this, 'excludePagesFromSitemap'], 10, 2);
        add_filter('excerpt_more', [$this, 'excerptMore'], 10);
        add_filter('excerpt_length', [$this, 'excerptLength'], 10);
        add_filter('get_the_archive_title', [$this, 'getTheArchiveTitle'], 10, 3);
    }

    /**
     * Adds image type support
     * http://www.iana.org/assignments/media-types/media-types.xhtml
     */
    public function uploadMimes($mimes)
    {
        $mimes['webp'] = 'image/webp';
        $mimes['svg']  = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Fixes SVG uploads not properly having their height and width set
     */
    public function updateAttachmentMetadata($data, $id)
    {
        $attachment = get_post($id);
        $mime_type  = $attachment->post_mime_type;

        if ($mime_type == 'image/svg+xml') {
            if (empty($data) || empty($data['width']) || empty($data['height'])) {
                $url            = wp_make_link_relative(wp_get_attachment_url($id));
                $xml            = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . $url);
                $attr           = $xml->attributes();
                $viewbox        = explode(' ', $attr->viewBox);
                $data['width']  = isset($attr->width) && preg_match('/\d+/', $attr->width, $value) ? (int) $value[0] : ( count($viewbox) == 4 ? (int) $viewbox[2] : null );
                $data['height'] = isset($attr->height) && preg_match('/\d+/', $attr->height, $value) ? (int) $value[0] : ( count($viewbox) == 4 ? (int) $viewbox[3] : null );
            }
        }
        return $data;
    }

    /**
     * Removes customizer features
     */
    public function mapMetaCap($caps = array(), $cap = '', $user_id = 0, $args = array())
    {
        if ($cap == 'customize') {
            return ['nope']; // return an unknown capability
        }
        return $caps;
    }

    /**
     * Adds yoast sitemap link to robots.txt
     */
    public function robotsTxt($output, $public)
    {
        $options = get_option('wpseo');
        if (class_exists('WPSEO_Sitemaps') && ( $options['enable_xml_sitemap'] == true )) {
            $homeURL = get_home_url();
            $output .= "Sitemap: $homeURL/sitemap_index.xml\n";
        }
        return $output;
    }

    /**
     * Removes default "Home" from front page title
     */
    public function wpseoTitle($title)
    {
        $title = str_replace('Home - ', '', $title);
        return $title;
    }

    /**
     * Modifies the OpenGraph title
     */
    public function wpseoOgTitle($title)
    {
        $title = str_replace('Home - ', '', $title);
        return $title;
    }

    /**
     * Enforces at least something for the meta description
     */
    public function wpseoMetaDesc($description)
    {
        if (empty($description)) {
            if (! empty(get_the_excerpt())) {
                $description = get_the_excerpt();
            } elseif (! empty(get_the_content())) {
                $description = substr(get_the_content(), 0, 150);
            } else {
                $description = get_the_title();
            }
        }
        return $description;
    }

    /**
     * Replaces the default "more"
     */
    public function excerptMore($more)
    {
        return '<span class="more"> &hellip;</span>';
    }

    /**
     * Sets the excerpt length
     */
    public function excerptLength($length)
    {
        return 20;
    }

    /**
     * Returns an archive title with out the automated prefix
     */
    public function getTheArchiveTitle($title, $original_title, $prefix)
    {
        return $original_title;
    }

    public function excludePagesFromSitemap($excluded_ids)
    {
        $excludedPageId = Actions::getTemplatePartsPageId();
        if (!$excludedPageId) {
            return $excluded_ids;
        }
        $excluded_ids[] = $excludedPageId;
        $child_pages = get_pages(array( 'child_of' => $excludedPageId ));
        foreach ($child_pages as $child_page) {
            $excluded_ids[] = $child_page->ID;
        }
        return $excluded_ids;
    }
}
